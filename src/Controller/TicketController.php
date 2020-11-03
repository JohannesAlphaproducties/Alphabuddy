<?php

namespace App\Controller;

use App\Entity\Hours;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="ticket_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $results = $this->getDoctrine()->getRepository(Ticket::class)->findOpenTickets();
        $tickets = $paginator->paginate($results, $request->query->getInt('page', 1), 10);

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * @Route("/new", name="ticket_new", methods={"GET","POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $ticket->setUser($this->getUser());
            $entityManager->persist($ticket);
            $entityManager->flush();

            //loop responsible users
            foreach ($ticket->getResponsible() as $user) {
                //check witch users are subscribed
                if ($user->getSubscribedResponsibleTicket() === true) {
                    $emailUser = (new Email())
                        ->subject('Verantwoodelijk gezet op ticket ' . $ticket->getCompany()->getName())
                        ->from('johannes.vlot@alphaproducties.nl')
                        ->to($user->getEmail())
                        ->text('Je bent verantwoordelijk gesteld op ticket https:///buddy.alphaproducties.nl/ticket/'. $ticket->getId())
                    ;
                    $mailer->send($emailUser);
                }
            }
            return $this->redirectToRoute('ticket_show', [
                'id' => $ticket->getId(),
            ]);
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_show", methods={"GET"})
     * @param Ticket $ticket
     * @return Response
     */
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket, $id, MailerInterface $mailer): Response
    {
        $filesystem = new Filesystem();

        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if (isset($_POST['description'])) {
            $description = $_POST['description'];
            $ticket->setDescription($description);

            $this->em->flush();
        }

        $emailCompany = $request->get('companyEmail');
        $subscribedUsers = $this->getDoctrine()->getRepository(User::class)->findSubscribedTicket();
        $name = $ticket->getResponsible()->get('name');

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->getCompany()->setEmail($emailCompany);
            $this->getDoctrine()->getManager()->flush();

            if ($ticket->getStatus() == 'klaar') {
                $html = $this->renderView('pdf/TicketPdf.html.twig', array(
                    'ticket' => $ticket,
                ));

                $html2 = $this->renderView('user_page/subscribedTicket.html.twig', array(
                    'ticket' => $ticket,
                ));

                $filename = $ticket->getId();

                $pdfOptions = new Options();
                $pdfOptions->set('defaultFont', 'Arial');
                $pdfOptions->setIsRemoteEnabled(true);

                $domPdf = new Dompdf($pdfOptions);
                $domPdf->loadHtml($html);
                $domPdf->render();
                $output = $domPdf->output();

                if ($filesystem->exists('../data/tickets/'. $filename. '.pdf')) {
                    //remove existing
                    $filesystem->remove('../data/tickets/'. $filename. '.pdf');
                    //create new
                    file_put_contents('../data/tickets/'. $filename. '.pdf', $output);
                } else {
                    file_put_contents('../data/tickets/'. $filename. '.pdf', $output);
                }

                //check if user is subscribed
                foreach ($subscribedUsers as $subscribedUser) {
                        //fill email user
                        $emailUser = (new Email())
                            ->subject('Ticket '.$ticket->getId(). ' '. $ticket->getCompany()->getName(). ' '. $name)
                            ->from('johannes.vlot@alphaproducties.nl')
                            ->to($subscribedUser->getEmail())
                            ->html($html2)
                        ;
                        $mailer->send($emailUser);
                }

                $message = (new Email())
                    ->subject('Ticket'. $ticket->getId())
                    ->from('johannes.vlot@alphaproducties.nl')
                    ->to($ticket->getCompany()->getEmail())
                    ->text('body')
                    ->attachFromPath('../data/tickets/' . $id . '.pdf')
                    ;
                $mailer->send($message);
            }

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }

    /**
     * @Route("/json/tickets", name="json_tickets")
     */
    public function jsonTickets()
    {
        $tickets = $this->getDoctrine()->getRepository(Ticket::class);
        $results = $tickets->createQueryBuilder('q')
            ->getQuery()
            ->getArrayResult();

        return new JsonResponse($results);
    }


}
