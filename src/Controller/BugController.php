<?php

namespace App\Controller;

use App\Entity\Bug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BugController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/bug/new", name="bug_new")
     * @param Request $request
     * @return RedirectResponse
     */
    public function newBug(Request $request)
    {
        $form = $request->get('bug');

        $bug = new Bug();

        $bug->setMessage($form);
        $this->em->persist($bug);
        $this->em->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/bug/delete/{id}", name="bug_delete")
     * @param Bug $bug
     * @return RedirectResponse
     */
    public function deleteBug(Bug $bug)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->em->remove($bug);
            $this->em->flush();
        }


        return $this->redirectToRoute('profile');
    }
}
