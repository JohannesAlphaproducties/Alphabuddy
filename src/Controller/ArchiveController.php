<?php
//dit is de goeie versie
namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\WorkOrders;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArchiveController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/archive/tickets", name="archive_tickets")
     */
    public function archiveTickets(PaginatorInterface $paginator, Request $request)
    {
        $results = $this->getDoctrine()->getRepository(Ticket::class)->findClosedTickets();

        $closedTickets = $paginator->paginate($results, $request->query->getInt('page', 1), 10);

        return $this->render('archive/ticket.twig', [
            'tickets' => $closedTickets,
        ]);
    }
    
    /**
     * @Route("/archive/workOrders", name="archive_workOrders")
     */
    public function archiveWorkOrders(PaginatorInterface $paginator, Request $request)
    {
        $results = $this->getDoctrine()->getRepository(WorkOrders::class)->findClosedWorkOrders();

        $closedWorkOrders = $paginator->paginate($results, $request->query->getInt('page', 1),10);

        return $this->render('archive/workOrder.html.twig', [
            'workOrders' => $closedWorkOrders,
        ]);
    }

    /**
     * @Route("/empty/tickets", name="empty_ticket_archive")
     */
    public function emptyTicketArchive()
    {
        $results = $this->getDoctrine()->getRepository(Ticket::class)->findClosedTickets();

        foreach ($results as $result) {
            $this->em->remove($result);
            $this->em->flush();
        }

        return $this->redirectToRoute('archive_tickets');
    }

    /**
     * @Route("/empty/workOrders", name="empty_work_orders_archive")
     */
    public function emptyWorkOrdersArchive()
    {
        $results = $this->getDoctrine()->getRepository(WorkOrders::class)->findClosedWorkOrders();

        foreach ($results as $result) {
            $this->em->remove($result);
            $this->em->flush();
        }

        return $this->redirectToRoute('archive_workOrders');
    }
}
