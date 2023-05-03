<?php

namespace App\Controller;
use App\Entity\Note;


use App\Entity\Rating;
use App\Entity\Service;
use App\Form\ServiceType;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;

use Endroid\QrCode\Writer\PngWriter;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'display_services')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $repository = $this->getDoctrine()->getRepository(Service::class)->findAll();


        $pagination = $paginator->paginate(
            $repository,
            $request->query->getInt('page', 1),
            3
        );


        return $this->render('service/index.html.twig', ['listS' => $pagination]);


    }
    #[Route('/services/free', name: 'display_services_free')]
    public function indexfree(Request $request, PaginatorInterface $paginator): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $repository = $this->getDoctrine()->getRepository(Service::class)->findAll();


        $pagination = $paginator->paginate(
            $repository,
            $request->query->getInt('page', 1),
            3
        );


        return $this->render('service/indexfree.html.twig', ['listS' => $pagination]);


    }

    #[Route('/exportExcel', name: 'exportExcel')]
    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'Libellé');
        $sheet->setCellValue('B1', 'Prix');
        $sheet->setCellValue('C1', 'Disponibilité');
        $sheet->setCellValue('D1', 'Catégorie');
        $sheet->setCellValue('E1', 'Description');

        $services = $this->getDoctrine()->getRepository(Service::class)->findAll();


        $row = 2;
        foreach ($services as $service) {
            $sheet->setCellValue('A' . $row, $service->getServLib());
            $sheet->setCellValue('B' . $row, $service->getServPrix());
            $sheet->setCellValue('C' . $row, $service->getServDispo());
            $sheet->setCellValue('D' . $row, $service->getCatLib());
            $sheet->setCellValue('E' . $row, $service->getServDesc());
            $row++;
        }



        $writer = new Xlsx($spreadsheet);
        $filename = 'services.xlsx';
        $writer->save($filename);

        return $this->file($filename);
    }


    #[Route('/ajouterService', name: 'ajouterService')]
    public function ajouterService(Request $request, SluggerInterface $slugger): Response
    {

        $Services = new Service();
        $form = $this->createForm(ServiceType::class, $Services);

        $qrCodes = [];


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $fileUpload = $form->get('servimg')->getData();

            $fileName = md5(uniqid()) . '.' . $fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir') . '/public/uploads', $fileName);

            $Services->setServImg($fileName);



            $em = $this->getDoctrine()->getManager();



            //GENEREATE QR CODE

            $url = 'https://www.google.com/search?q=';

            $objDateTime = new \DateTime('NOW');
            $dateString = $objDateTime->format('d-m-Y H:i:s');

            $path = dirname(__DIR__, 2) . '/public/';


            // set qrcode
            $result = Builder::create()

                ->writer(new PngWriter())
                ->writerOptions([])
                ->data('Custom QR code contents')
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->size(400)
                ->margin(10)
                ->labelText($dateString)
                ->labelAlignment(new LabelAlignmentCenter())
                ->labelMargin(new Margin(15, 5, 5, 5))
                ->logoPath($path . 'uploads/' . $fileName)
                ->logoResizeToWidth('100')
                ->logoResizeToHeight('100')
                ->backgroundColor(new Color(255, 255, 255))
                ->build();

            //generate name
            $namePng = uniqid('', '') . '.png';

            //Save img png
            $result->saveToFile($path . 'uploads/' . $namePng);

            $result->getDataUri();

            $Services->setQrcode($namePng);


            $em->persist($Services);
            $em->flush();


            $this->addFlash(
                'notice', 'Service a été bien ajoutée ! '
            );

            return $this->redirectToRoute('display_services');

        }

        return $this->render('service/createService.html.twig',
            ['f' => $form->createView(), 'qrCodes' => $qrCodes]
        );
    }

    #[Route('/modifierService/{servid}', name: 'modifierService')]
    public function modifierService(Request $request, $servid): Response
    {
        $Services = $this->getDoctrine()->getManager()->getRepository(Service::class)->find($servid);

        $form = $this->createForm(ServiceType::class, $Services);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $fileUpload = $form->get('servimg')->getData();
            $fileName = md5(uniqid()) . '.' . $fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir') . '/public/uploads', $fileName);

            $Services->setServImg($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($Services);
            $em->flush();
            $this->addFlash(
                'notice', 'Service a été bien modifié '
            );

            return $this->redirectToRoute('display_services');

        }

        return $this->render('service/modifierService.html.twig',
            ['f' => $form->createView()]
        );
    }

    #[Route('/suppressionService/{servid}', name: 'suppressionService')]
    public function suppressionServices(Service $Services): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($Services);
        $em->flush();
        $this->addFlash(
            'noticedelete', 'Le Service a été bien supprimé '
        );

        return $this->redirectToRoute('display_services');
    }

    #[Route('/detailServices/{servid}', name: 'detailServices')]
    public function detailServices(\Symfony\Component\HttpFoundation\Request $req, $servid)
    {
        $em = $this->getDoctrine()->getManager();
        $Services = $em->getRepository(Service::class)->find($servid);



        return $this->render('Service/detailService.html.twig', array(
            'Id' => $Services->getServid(),
            'Libille' => $Services->getServlib(),

            'Servdispo' => $Services->getServDispo(),
            'Description' => $Services->getServdesc(),
            'image' => $Services->getServimg(),
            'Prix' => $Services->getServprix(),
            'Categorie' => $Services->getCatLib(),




        ));
    }

    #[Route('/detailServices2/{servid}', name: 'detailServices2')]
    public function detailServices2(\Symfony\Component\HttpFoundation\Request $req, $servid)
    {
        $em = $this->getDoctrine()->getManager();
        $Services = $em->getRepository(Service::class)->find($servid);


        return $this->render('Service/detailService2.html.twig', array(
            'Id' => $Services->getServid(),
            'Libille' => $Services->getServlib(),

            'Servdispo' => $Services->getServDispo(),
            'Description' => $Services->getServdesc(),
            'image' => $Services->getServimg(),
            'Prix' => $Services->getServprix(),
            'Categorie' => $Services->getCatLib(),


        ));
    }


    // FRONT AFFICHAGE SERVICES :
    #[Route('/frontServices', name: 'display_services_front')]
    public function frontServices(Request $request): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $repository = $this->getDoctrine()->getRepository(Service::class)->findAll();


        return $this->render('front/displayFrontServices.html.twig', ['listS' => $repository]);
    }

    #[Route('/frontServicesfree', name: 'display_services_front_free')]
    public function frontServicesfree(Request $request): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $repository = $this->getDoctrine()->getRepository(Service::class)->findAll();


        return $this->render('front/displayFrontServicesfree.html.twig', ['listS' => $repository]);
    }



    /**
     * @Route("/ajax_search/", name="ajax_search")
     */
    public function chercherService(\Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');

        $x = $em
            ->createQuery(
                'SELECT s
                FROM App\Entity\Service s
                WHERE s.servlib LIKE :str'
            )
            ->setParameter('str', '%' . $requestString . '%')->getResult();

        $services = $x;
        if (!$services) {
            $result['services']['error'] = "Service non trouvé :( ";
        } else {
            $result['services'] = $this->getRealEntities($services);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($services)
    {
        foreach ($services as $service) {
            $realEntities[$service->getServId()] = [$service->getServimg(), $service->getServDispo(), $service->getServlib(), $service->getServPrix()];
        }
        return $realEntities;
    }

    #[Route('/top', name: 'top')]
    public function afficherTopfiveService()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder();
        $query->select('s.servid, s.note')
            ->from('App\Entity\Service', 's')
            ->orderBy('s.note', 'DESC')
            ->setMaxResults(3);
        $res = $query->getQuery();
        $serviceEvalues = $res->execute();
        $note = 0;
        //count
        $i = 0;


        $j = 0;

        foreach ($serviceEvalues as $se) {
            $note = $note + $se["note"];
            $i++;

            $noteMoy = $note / $i;
            $noteMoy = round($noteMoy);

            $service = $em->getRepository(Service::class)->findOneBy(array('servid' => $se['servid']));
            $serviceTop[$j] = $service;
            $j++;
        }
        return $this->render('front/top.html.twig', array('id' => $se['servid'], 'note' => $se['note'], 'topfive' => $serviceTop));
    }



    #[Route('/noterService/{servid}/{note}', name: 'noterService')]
    public function noterService(Request $request, $servid,$note): Response
    {
        $Services = $this->getDoctrine()->getManager()->getRepository(Service::class)->find($servid);

        $form = $this->createForm(ServiceType::class, $Services);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $fileUpload = $form->get('servimg')->getData();
            $fileName = md5(uniqid()) . '.' . $fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir') . '/public/uploads', $fileName);


            $em = $this->getDoctrine()->getManager();

            $em->persist($Services);
            $em->flush();
            $this->addFlash(
                'notice', 'Service a été bien noté '
            );

            return $this->redirectToRoute('display_services');
        }

        return $this->render('service/modifierService.html.twig',
            ['f' => $form->createView()]
        );
    }


    /**
     * @Route("/services/{id}/note", name="service_note")
     */
    public function addNoteToService(Request $request, Service $service)
    {
        $note = $request->request->get('note');
        $notes = new  Note();


        if ($note) {
            $notes = new Note();
            $notes->setNote($note);
            $notes->setServid($service->getServid());


            $service->setNote($note);


            $em = $this->getDoctrine()->getManager();
            $em->persist($service);

            $em->persist($notes);
            $em->flush();

            $this->addFlash('success', 'Note ajouté avec succés!');
        } else {
            $this->addFlash('error', 'Veuillez donner une note!');
        }

        return $this->redirectToRoute('display_services_front');
    }

    #[Route('/getNoterServicePage/{servid}', name: 'getNoterServicePage')]
    public function getNoterServicePage(\Symfony\Component\HttpFoundation\Request $req, $servid)
    {
        $em = $this->getDoctrine()->getManager();
        $Services = $em->getRepository(Service::class)->find($servid);


        return $this->render('Service/getNoterServicePage.html.twig', array(
            'Id' => $Services->getServid(),
            'Libille' => $Services->getServlib(),

            'Servdispo' => $Services->getServDispo(),
            'Description' => $Services->getServdesc(),
            'image' => $Services->getServimg(),
            'Prix' => $Services->getServprix(),
            'Categorie' => $Services->getCatLib(),


        ));
    }
}