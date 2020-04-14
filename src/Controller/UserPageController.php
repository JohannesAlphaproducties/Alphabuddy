<?php

namespace App\Controller;

use App\Entity\Hours;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/users", name="user_index")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $hours = $this->getDoctrine()->getRepository(Hours::class)->findAll();

        return $this->render('user_page/index.html.twig', [
            'controller_name' => 'UserPageController',
            'users' => $users,
            'hours' => $hours,
        ]);
    }


    /**
     * @Route("/delete/user/{id}")
     * @return RedirectResponse
     */
    public function deleteUser($id)
    {
        //get user by id
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        //delete user
        if (isset($user)) {
            if ( $this->isGranted('ROLE_ADMIN') ) {

                $this->em->remove($user);

                $this->em->flush();
            } else {
                echo "you are not allowed to delete this item";
            }
        }
        return $this->redirectToRoute('user_index');
    }
}
