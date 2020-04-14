<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\HoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function index()
    {
        $hours = $this->getUser()->getHours();
        $user = $this->getUser();
        $bugs = $this->getDoctrine()->getRepository(Bug::class)->findAll();

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'hours' => $hours,
            'user' => $user,
            'bugs' => $bugs,
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/save/subscription" , name="subscribe_workOrder", methods={"POST"})
     */
    public function subscribeWorkOrder(Request $request)
    {
        $workOrderSwitch = $request->get('workOrderSwitch');


        $this->getUser()->setSubsribedWorkOrder($workOrderSwitch);
        $this->em->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/save/subscription/ticket", name="subscribe_ticket", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscribeTicket(Request $request)
    {
        $ticketSwitch = $request->get('ticketSwitch');

        $this->getUser()->setSubscribedTicket($ticketSwitch);
        $this->em->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/save/subscription/responsible", name="subscribe_responsible_ticket", methods={"POST"})
     */
    public function subscribeResponsible(Request $request)
    {
        $responsibleSwitch = $request->get('ResponsibleSwitch');

        $this->getUser()->setSubscribedResponsibleTicket($responsibleSwitch);
        $this->em->flush();

        return $this->redirectToRoute('profile');

    }

    /**
     * @Route("/save/contract", name="save_contract")
     */
    public function saveContract(Request $request)
    {
        $contractButton = $request->get('contract');

        $this->getUser()->setContract($contractButton);
        $this->em->flush();

        return $this->redirectToRoute('profile');
    }
}
