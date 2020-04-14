<?php

namespace App\Controller;

use App\Entity\WorkOrders;
use Mpdf\Mpdf;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    /**
     * @Route("/pdf/{id}", name="pdf")
     */
    public function index($id)
    {
        return $this->renderView('pdf/pdf.html.twig');
    }

}
