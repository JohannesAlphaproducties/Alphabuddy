<?php

namespace App\Controller;

use App\Entity\Hours;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\WorkOrders;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $hours = $this->getDoctrine()->getRepository(Hours::class)->findAll();
        $workOrders = $this->getDoctrine()->getRepository(WorkOrders::class)->findOpenWorkOrders();
        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findOpenTickets();
        $contract = $this->getUser()->getContract();


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $users,
            'workOrders' => $workOrders,
            'tickets' => $tickets,
            'hours' => $hours,
            'contract' => $contract,
        ]);
    }

}