<?php

namespace App\Controller;

use App\Entity\Hours;
use App\Entity\User;
use App\Form\HoursType;
use App\Repository\HoursRepository;
use App\Repository\UserRepository;
use App\Repository\WorkOrdersRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hours")
 */
class HoursController extends AbstractController
{
    /**
     * @Route("/", name="hours_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        //get monday's date and friday's date
        $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
        $friday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

        //get user
        $user = $this->getUser();

        $results = $this->getDoctrine()->getRepository(Hours::class)->findUserHours($user, $monday, $friday);

        $hours = $paginator->paginate($results, $request->query->getInt('page', 1), 10);

        return $this->render('hours/index.html.twig', [
            'hours' => $hours,
        ]);
    }

    /**
     * @Route("/total/{id}", name="total_hours", methods={"GET", "POST"})
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
     * @Route("/new", name="hours_new", methods={"GET","POST"})
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
