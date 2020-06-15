<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\WorkOrders;
use App\Form\WorkOrdersType;
use App\Repository\WorkOrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route("/work/orders")
 */
class WorkOrdersController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="work_orders_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $results = $this->getDoctrine()->getRepository(WorkOrders::class)->findOpenWorkOrders();

        $workOrders = $paginator->paginate($results, $request->query->getInt('page', 1), 10);

        return $this->render('work_orders/index.html.twig', [
            'work_orders' => $workOrders,
        ]);
    }

    /**
     * @Route("/new", name="work_orders_new", methods={"GET","POST"})
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $workOrder = new WorkOrders();
        $form = $this->createForm(WorkOrdersType::class, $workOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workOrder);
            $entityManager->flush();

            //loop responsible users
            foreach ($workOrder->getMechanic() as $user) {
                //check witch users are subscribed
                $emailUser = (new Email())
                    ->subject('Verantwoordelijk gezet op werkbon ' . $workOrder->getTitel())
                    ->from('johannes.vlot@alphaproducties.nl')
                    ->to($user->getEmail())
                    ->text('Je bent verantwoordelijk gesteld op werkbon https://buddy.alphaproducties.nl/work/orders/'. $workOrder->getId())
                    ;
                $mailer->send($emailUser);
            }

            return $this->redirectToRoute('work_orders_show', [
                'id' => $workOrder->getId(),
            ]);
        }

        return $this->render('work_orders/new.html.twig', [
            'work_order' => $workOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="work_orders_show", methods={"GET"})
     */
    public function show(WorkOrders $workOrder): Response
    {
        $hours = $workOrder->getHours();

        return $this->render('work_orders/show.html.twig', [
            'work_order' => $workOrder,
            'hours' => $hours,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="work_orders_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, WorkOrders $workOrder): Response
    {
        $form = $this->createForm(WorkOrdersType::class, $workOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('work_orders_index');
        }

        return $this->render('work_orders/edit.html.twig', [
            'work_order' => $workOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="work_orders_delete", methods={"DELETE"})
     */
    public function delete(Request $request, WorkOrders $workOrder): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrder->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($workOrder);
            $entityManager->flush();
        }
        return $this->redirectToRoute('work_orders_index');
    }

    /**
     * @Route("/download/pdf/{id}", name="download_pdf", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function downloadPdf($id): Response
    {
        //search workOrder on id
        $workOrder = $this->getDoctrine()->getRepository(WorkOrders::class)->find($id);

        //create new pdf options
        $pdfOptions = new Options();
        $pdfOptions->setIsRemoteEnabled(true);

        //create new domPdf with the pdf options
        $domPdf = new Dompdf($pdfOptions);

        //send data to pdf template
        $html = $this->renderView('pdf/pdf.html.twig', array(
            'workOrder' => $workOrder,
        ));

        //load html in pdf
        $domPdf->loadHtml($html);
        //set paper and position
        $domPdf->setPaper('A4', 'portrait');
        //render pdf
        $domPdf->render();
        //download pdf to browser with the right name
       $domPdf->stream('Werkbon'.$workOrder->getId()."-".$workOrder->getTitel().".pdf");
    }

    /**
     * @Route("/search", name="workOrder_search", methods={"POST"})
     */
    public function searchAction(Request $request, WorkOrdersRepository $workOrdersRepository, PaginatorInterface $paginator): Response
    {
        $query = $request->request->get('query');

        $workOrderQuery = $workOrdersRepository->findWorkOrder($query);

        $workOrders = $paginator->paginate($workOrderQuery, $request->query->getInt('page', 1), 10);

        return $this->render('work_orders/index_results.html.twig', ['work_orders' => $workOrders]);
    }

    /**
     * @Route("/sign/{id}", name="sign_workOrder", methods={"GET"})
     */
    public function signWorkOrder($id, WorkOrdersRepository $workOrdersRepository)
    {
        $workOrder = $workOrdersRepository->find($id);
        $companyEmail = $workOrder->getCompany()->getEmail();

        return $this->render('work_orders/sign.html.twig', [
            'id' => $id,
            'companyEmail' => $companyEmail,
        ]);
    }

    /**
     * @Route("/save/sign/{id}", name="save_sign", methods={"POST"})
     * @throws \Swift_DependencyException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function saveSign(Request $request, $id, MailerInterface $mailer)
    {
        $filesystem = new Filesystem();

        //get data sign fields
        $data_uri = $request->get('hidden_field');
        $signedBy = $request->get('signedBy');
        $check = $request->get('check');
        $emailCompany = $request->get('emailCompany');

        //find users
        $subScribedUsers = $this->getDoctrine()->getRepository(User::class)->findSubscribedWorkOrder();
        //find workOrder
        $workOrder = $this->getDoctrine()->getRepository(WorkOrders::class)->find($id);
        //get mechanic name
        $name = $workOrder->getMechanic()->get('name');
        //file name
        $filename = $workOrder->getId();

        ///if company email is empty persist
        if ($workOrder->getCompany()->getEmail() == null) {
            $workOrder->getCompany()->setEmail($emailCompany);
            $this->em->flush();
        }

        //if signed
        if ($data_uri) {
            $encoded_image = explode(",", $data_uri)[1];
            $decoded_image = base64_decode($encoded_image);
            file_put_contents("../data/signatures/" . $id . ".png", $decoded_image);

            //after sign close workorder
            $workOrder->setStatus('afgesloten');
            $workOrder->setSignedBy($signedBy);
            $this->em->persist($workOrder);
            $this->em->flush();
        }

        //data naar pdf template sturen
        $html = $this->renderView('pdf/pdf.html.twig', array(
            'workOrder' => $workOrder,
        ));

        //workorder content voor subscribers
        $html2 = $this->renderView('user_page/subscribeWorkOrder.html.twig', array(
            'workOrder' => $workOrder,
        ));

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        $domPdf = new Dompdf($pdfOptions);
        $domPdf->loadHtml($html);
        $domPdf->render();
        $output = $domPdf->output();

        //generate file if it doesn't exist
        if ($filesystem->exists('../data/workorders/' . $filename . '.pdf')) {
            //remove existing
            $filesystem->remove('../data/workorders/' . $filename . '.pdf');
            //create new
            file_put_contents('../data/workorders/' . $filename . '.pdf', $output);
        } else {
            //create file
            file_put_contents('../data/workorders/' . $filename . '.pdf', $output);
        }

        foreach ($subScribedUsers as $subScribedUser) {
            // Create a message
            $message = (new Email())
                ->subject('Werkbon ' . $workOrder->getTitel() . ' ' . $workOrder->getCompany()->getName() . ' ' . $name)
                ->from('johannes.vlot@alphaproducties.nl')
                ->to($subScribedUser->getEmail())
                ->text('Dit is de email voor de medewerkers die een copy willen ontvangen van een afgehandelde werkbon')
                ->attachFromPath('../data/workorders/' . $id . '.pdf')
            ;

            // Send the message
            $mailer->send($message);
        }

        //send workOrder to company
        if ($check == true) {
            //fill email company
            $messageCompany = (new Email())
                ->subject('Werkbon ' . $workOrder->getTitel() . ' ' . $workOrder->getCompany()->getName() . ' ' . $name)
                ->from('johannes.vlot@alphaproducties.nl')
                ->to($emailCompany)
                ->text('.')
                ->attachFromPath('../data/workorders/' . $id . '.pdf')
                ;
            $mailer->send($messageCompany);
        }
        return $this->redirectToRoute('work_orders_show', [
            'id' => $workOrder->getId(),
        ]);
    }

    /**
     * @Route("/transform/{id}", name="transform", methods={"GET","POST"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function transform(Request $request, $id)
    {

        $workOrder = new WorkOrders();
        $form = $this->createForm(WorkOrdersType::class, $workOrder);
        $form->handleRequest($request);

        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($workOrder);
            $this->em->remove($ticket);
            $this->em->flush();

           return $this->redirectToRoute('work_orders_index');
        }

        return $this->render('work_orders/transform.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);

    }
}
