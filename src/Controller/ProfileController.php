<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $user = $this->getUser();
        $hours = $user->getHours();
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

        $this->getUser()->setSubscribedWorkOrder($workOrderSwitch);
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscribeResponsible(Request $request)
    {
        $responsibleSwitch = $request->get('ResponsibleSwitch');

        $this->getUser()->setSubscribedResponsibleTicket($responsibleSwitch);
        $this->em->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/save/subscription/responsible/workOrder", name="subscribe_responsible_workOrder", methods={"POST"})
     */
    public function subscribeResponsibleWorkOrder(Request $request)
    {
        $responsibleSwitchWorkOrder = $request->get('ResponsibleSwitchWorkOrder');

        $this->getUser()->setSubscribedResponsibleWorkOrder($responsibleSwitchWorkOrder);
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

    /**
     * @Route("/change/password", name="change_password")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $new_pass_confirm = $request->get('newPassword');

        $user = $this->getUser();

        $new_pass_encoded = $passwordEncoder->encodePassword($user, $new_pass_confirm);
        $this->getUser()->setPassword($new_pass_encoded);
        $this->em->flush();

        $this->addFlash('success', 'Wachtwoord veranderd.');

        return $this->redirectToRoute('profile');
    }

}
