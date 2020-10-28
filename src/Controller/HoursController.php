<?php

namespace App\Controller;

use App\Entity\Hours;
use App\Form\HoursType;
use App\Repository\WorkOrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\This;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hours")
 */
class HoursController extends AbstractController
{
    /**
     * @Route("/excel", name="excel")
     */
    public function excel()
    {
        $spreadsheet = new Spreadsheet();

        //20 tot 20 datums
        $start20 = date('Y-m-20', strtotime('-1 month'));
        $end20 = date('Y-m-20', strtotime('today'));
        if (date('Y-m-d', strtotime('today')) > date('Y-m-20')) {
            $start20 = date('Y-m-20', strtotime('today'));
            $end20 = date('Y-m-20', strtotime('next month'));
        }

        //get user
        $user = $this->getUser();

        //get hours between 20 last month and 20 this month
        $hours = $this->getDoctrine()->getRepository(Hours::class)->findUserHours($user, $start20, $end20);

        //style excel
        $styleArrayTitle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:C1')->applyFromArray($styleArrayTitle);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);

        $numm = 3;
        $numm1 = 3;

        foreach ($hours as $hour) {
            $spreadsheet->getActiveSheet()
                    ->setCellValue('A'.$numm++, $hour['datum'])
                    ->setCellValue('B'.$numm1++ , $hour['sumDayHours']);
        }

        $formula = $numm1 + 2;
        //header titles
        $spreadsheet->getActiveSheet()
            ->setCellValue('A1', 'Datum')
            ->setCellValue('B1', 'Uren')
            ->setCellValue('C1', 'Overuren')
            ->setCellValue('B'. $formula, '=SOM(B3:B'.$numm1.')')
            ->setCellValue('C'. $formula, '=SOM(C3:C'.$numm1.')');

        $writer = new Xlsx($spreadsheet);

        $filename = date('m-Y', strtotime('this month')).'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);

        $writer->save($temp_file);

        return $this->file($temp_file, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/testExcel", name="test_excel")
     */
    public function testExel(Request $request)
    {
        $month = $request->request->get('month');
        dd($month);
        $user = $this->getUser();
        $results = $this->getDoctrine()->getRepository(Hours::class)->findHoursMonthUser($user, $month);

        foreach ($results as $result) {
            dump($result);
        }
        die();



        return $this->render('hours/showMonthHours.html.twig');
    }

    /**
     * @Route("/", name="hours_index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        //get monday's date and friday's date
        $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
        $friday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

        //get user
        $user = $this->getUser();

        //20 tot 20 datums
        $start20 = date('Y-m-20', strtotime('-1 month'));
        $end20 = date('Y-m-20', strtotime('today'));

        if (date('Y-m-d', strtotime('today')) > date('Y-m-20')) {
            $start20 = date('Y-m-20', strtotime('today'));
            $end20 = date('Y-m-20', strtotime('next month'));
        }

        //get hours between 20 last month and 20 this month
        $hours20 = $this->getDoctrine()->getRepository(Hours::class)->findHoursWeek($user, $start20, $end20);
        $results = $this->getDoctrine()->getRepository(Hours::class)->findHoursWeek($user, $monday, $friday);

        //get months for select
        $hoursMonth = $this->getDoctrine()->getRepository(Hours::class)->findHoursMonth($user);

        $hours = $paginator->paginate($results, $request->query->getInt('page', 1), 10);

        return $this->render('hours/index.html.twig', [
            'hours' => $hours,
            'hours20' => $hours20,
            'hoursMonth' => $hoursMonth,
        ]);
    }

    /**
     * @Route("/total/{id}", name="total_hours", methods={"GET", "POST"})
     * @param $id
     * @param WorkOrdersRepository $workOrdersRepository
     * @return Response
     */
    public function getTotalHours($id, WorkOrdersRepository $workOrdersRepository)
    {
        $workorderObject = $workOrdersRepository->find($id);

        $entityManager = $this->getDoctrine()->getManager();

        $dql = "SELECT SUM(e.hours) AS total FROM App\Entity\Hours e " .
            "WHERE e.workorder = ?1";

        $balance = $entityManager->createQuery($dql)
            ->setParameter(1, $workorderObject)
            ->getSingleScalarResult();

        return new Response($balance);
    }

    /**
     * @Route("/personalTotal/{id}", name="total_hours_personal")
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalPersonalHours($id)
    {
        //get monday's date and friday's date
        $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
        $friday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

        //get user
        $user = $this->getUser();

        //get hours and execute query
        $hours = $this->getDoctrine()->getRepository(Hours::class)->findPersonalHours($user, $monday, $friday);

        //get more or no result
        $totalRevenue = $hours->getOneOrNullResult();

        //use array
        $total = $totalRevenue['total'];

        //return values
        if ($total > 0) {
            return new Response($total);
        } else {
            return new Response(0);
        }
    }

    /**
     * @Route("/personalTotal/{id}", name="total_hours_personal")
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalPersonalHours20($id)
    {
        //20 tot 20 datums
        $start20 = date('Y-m-20', strtotime('-1 month'));
        $end20 = date('Y-m-20', strtotime('today'));
        if (date('Y-m-d', strtotime('today')) > date('Y-m-20')) {
            $start20 = date('Y-m-20', strtotime('today'));
            $end20 = date('Y-m-20', strtotime('next month'));
        }

        //get user
        $user = $this->getUser();

        //get hours and execute query
        $hours = $this->getDoctrine()->getRepository(Hours::class)->findPersonalHours($user, $start20, $end20);

        //get more or no result
        $totalRevenue = $hours->getOneOrNullResult();

        //use array
        $total = $totalRevenue['total'];

        //return values
        if ($total > 0) {
            return new Response($total);
        } else {
            return new Response(0);
        }
    }

    /**
     * @Route("/new", name="hours_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $hour = new Hours();
        $form = $this->createForm(HoursType::class, $hour);
        $form->handleRequest($request);
        $id = $request->get('id');

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $hour->setUser($this->getUser());
            $entityManager->persist($hour);
            $entityManager->flush();

            return $this->redirectToRoute('work_orders_show', [
                'id' => $hour->getWorkorder()->getId(),
            ]);
        }

        return $this->render('hours/new.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hours_show", methods={"GET"})
     * @param Hours $hour
     * @return Response
     */
    public function show(Hours $hour): Response
    {
        return $this->render('hours/show.html.twig', [
            'hour' => $hour,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="hours_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Hours $hour): Response
    {
        $form = $this->createForm(HoursType::class, $hour);
        $form->handleRequest($request);
        $id = $hour->getWorkorder()->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('work_orders_show', [
                'id' => $hour->getWorkorder()->getId(),
            ]);
        }

        return $this->render('hours/edit.html.twig', [
            'hour' => $hour,
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    /**
     * @Route("/{id}", name="hours_delete", methods={"DELETE"})
     * @param Request $request
     * @param Hours $hour
     * @return Response
     */
    public function delete(Request $request, Hours $hour): Response
    {
            if ($this->isCsrfTokenValid('delete'.$hour->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($hour);
                $entityManager->flush();
            }

        return $this->redirectToRoute('hours_index');
    }



}
