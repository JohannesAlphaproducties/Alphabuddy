<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Entity\WorkOrders;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/company")
 */
class CompanyController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/index", name="index_company")
     */
    public function index()
    {
        return $this->render('company/import.html.twig');
    }

    /**
     * @Route("/import/{page}", name="import_company")
     */
    public function importCompany($page, LoggerInterface $logger, CompanyRepository $companyRepository): Response
    {
        //nodige velden om de api request te maken
        $fields = array(
            "api_group" => ("38555"),
            "api_secret" => ("30H45gjKNvyh5AkiozB5b6OvTmuwF5KzWhuRnDs0ZQ8Vhi3vIvrJs55biqxNm7d8exQs18jROhCVpwhYSzyauubCKzgXxkVCIBUAz4hebK0r78fPxDQRYRkyhrlFB77aySAYMI3OiT5gRbWeFCWtk7Sro3WQ1UQqCgefO7TXRO3yQPDlhqErnnJBhwh8gXoJ3tOIrxv8"),
            "amount" => ("100"),
            "pageno" => ($page),
        );

        //request maken naar de API
        $client = HttpClient::create();
        $response = $client->request('POST', "https://app.teamleader.eu/api/getCompanies.php", [
            'body' => $fields,
        ]);

        //waardes ophalen uit de response
        $content = $response->getContent();

        //decrypten
        $results = json_decode($content, true);

        $number = 0;

        foreach ($results as $item) {

            $company = new Company();
            //alle waardes invullen
            $company
                ->setTlId($item['id'])
                ->setName($item['name'])
                ->setWebsite($item['website'])
                ->setType(null)
                ->setBillingAddress($item['street'] . ' ' . $item['number'])
                ->setBillingZip($item['zipcode'])
                ->setBillingTown($item['city'])
                ->setEmail($item['email'])
                ->setPhone($item['telephone'])
                ->setFax($item['fax']);

            //checken of het bedrijf al bestaat
            if (!$companyRepository->findOneBy(['tlId' => $item['id']])) {
                $number++;
                $this->em->persist($company);
                $this->em->flush();
            }

        }
        //hoeveel bedrijven er zijn opgehaald
        return new Response($number);
    }

    /**
     * @Route("/", name="show_companys")
     */
    public function showCompanys(Request $request, PaginatorInterface $paginator)
    {
        $results = $this->getDoctrine()->getRepository(Company::class)->findAllCompanies();

        return $this->render('company/index.html.twig', [
            'companys' => $results,
        ]);
    }

    /**
     * @Route("/show/{id}", name="showOne_company")
     */
    public function showOneCompany($id)
    {
        $company = $this->getDoctrine()->getRepository(Company::class)->find($id);
        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findTicketsCompany($id);
        $workOrders = $this->getDoctrine()->getRepository(WorkOrders::class)->findWorkOrdersCompany($id);

        return $this->render('company/show.html.twig', [
            'company' => $company,
            'tickets' => $tickets,
            'workOrders' => $workOrders,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_company")
     */
    public function editCompany(Request $request, Company $company): Response
    {
        $form = $this->createForm(CompanyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($company);
            $this->em->flush();
        }
        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_company")
     */
    public function deleteCompany($id)
    {
        //find company
        $company = $this->getDoctrine()->getRepository(Company::class)->find($id);

        foreach ($company->getTickets() as $tickets) {
            $this->em->remove($tickets);
        }
        foreach ($company->getWorkorders() as $workOrders) {
            $this->em->remove($workOrders);
        }

        //remove company
        $this->em->remove($company);
        $this->em->flush();

        return $this->redirectToRoute('show_companys');
    }
}