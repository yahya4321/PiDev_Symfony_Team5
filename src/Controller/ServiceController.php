<?php

namespace App\Controller;
use App\Entity\Service;
use App\Entity\User;
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
    public function index( Request  $request,PaginatorInterface  $paginator): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $repository = $this->getDoctrine()->getRepository(Service::class)->findAll();



        $pagination = $paginator->paginate(
            $repository,
            $request->query->getInt('page', 1), // Current page number
            3 // Number of items per page
        );





        return $this->render('service/index.html.twig', ['listS'=>$pagination]);



    }

    #[Route('/exportExcel', name: 'exportExcel')]

    public function exportExcel() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the sheet
        $sheet->setCellValue('A1', 'servlib');
        $sheet->setCellValue('B1', 'servprix');
        $sheet->setCellValue('C1', 'servdispo');
        $sheet->setCellValue('D1', 'catlib');
        $sheet->setCellValue('E1', 'servdesc');

        // Get the products from the database
        $products = $this->getDoctrine()->getRepository(Service::class)->findAll();

        // Add the products to the sheet
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->getServLib());
            $sheet->setCellValue('B' . $row, $product->getServPrix());
            $sheet->setCellValue('C' . $row, $product->getServDispo());
            $sheet->setCellValue('D' . $row, $product->getCatLib());
            $sheet->setCellValue('E' . $row, $product->getServDesc());
            $row++;
        }

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'services.xlsx';
        $writer->save($filename);

        // Return the Excel file as a response
        return $this->file($filename);
    }


    #[Route('/ajouterService', name: 'ajouterService')]
    public function ajouterService(Request $request,SluggerInterface $slugger): Response
    {

        $Services = new Service();
        $form = $this->createForm(ServiceType::class,$Services);

        $qrCodes = [];



        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()) {

            $fileUpload= $form->get('servimg')->getData();

            $fileName= md5(uniqid()). '.' .$fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir').'/public/uploads',$fileName);

            $Services->setServImg($fileName);

            // USER :
            $User= $this->getDoctrine()->getManager()->getRepository(User::class)->find(
                1
            );

            $em = $this->getDoctrine()->getManager();

            $Services->setIdUser($User);




            //GENEREATE QR CODE

            $url = 'https://www.google.com/search?q=';

            $objDateTime = new \DateTime('NOW');
            $dateString = $objDateTime->format('d-m-Y H:i:s');

            $path = dirname(__DIR__, 2).'/public/';


            // set qrcode
            $result =Builder::create()
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
                ->logoPath($path.'uploads/'.$fileName)
                ->logoResizeToWidth('100')
                ->logoResizeToHeight('100')
                ->backgroundColor(new Color(255, 255, 255))
                ->build()
            ;

            //generate name
            $namePng = uniqid('', '') . '.png';

            //Save img png
            $result->saveToFile($path.'uploads/'.$namePng);

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
            ['f'=>$form->createView(),'qrCodes'=>$qrCodes]
        );
    }

    #[Route('/modifierService/{servid}', name: 'modifierService')]
    public function modifierService(Request $request,$servid): Response
    {
        $Services= $this->getDoctrine()->getManager()->getRepository(Service::class)->find($servid);

        $form = $this->createForm(ServiceType::class,$Services);

        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()) {

            $fileUpload= $form->get('servimg')->getData();
            $fileName= md5(uniqid()). '.' .$fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir').'/public/uploads',$fileName);

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
            ['f'=>$form->createView()]
        );
    }

    #[Route('/suppressionService/{servid}', name: 'suppressionService')]
    public function suppressionServices(Service  $Services): Response
    {
        $em = $this->getDoctrine()->getManager();
        //MISE A JOURS
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

            'Servdispo' =>$Services->getServDispo(),
            'Description' => $Services->getServdesc(),
            'image'=>$Services->getServimg(),
            'Prix' => $Services->getServprix(),
            'Categorie'=>$Services->getCatLib(),
            'User'=>$Services->getIdUser()->getNomUser(),
             'mail'=>$Services->getIdUser()->getEmailUser()

        ));
    }


    // FRONT AFFICHAGE SERVICES :
    #[Route('/frontServices', name: 'display_services_front')]
    public function frontServices( Request  $request): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $repository = $this->getDoctrine()->getRepository(Service::class)->findAll();









        return $this->render('front/displayFrontServices.html.twig', ['listS'=>$repository]);
    }




    // METIER AJAX SEARCH :
    //SEARCH

    /**
     * @Route("/ajax_search/", name="ajax_search")
     */
    public function chercherService(\Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');// ooofkdokfdfdf

      $x =  $em
            ->createQuery(
                'SELECT P
                FROM App\Entity\Service P
                WHERE P.servlib LIKE :str'
            )
            ->setParameter('str', '%'.$requestString.'%')            ->getResult();


        $products =  $x;
        if(!$products) {
            $result['products']['error'] = "Services non trouvé :( ";
        } else {
            $result['products'] = $this->getRealEntities($products);
        }
        return new Response(json_encode($result));
    }





    // LES  attributs
    public function getRealEntities($products){
        foreach ($products as $products){
            $realEntities[$products->getServId()] = [$products->getServimg(),$products->getServDispo(),$products->getServlib(),$products->getServPrix()];

        }
        return $realEntities;
    }



    // TOP SERV METier
    #[Route('/top', name: 'top')]

    public function  afficherTopfiveProduct() {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder(); // dql
        $query->select('p.servid ,p.note')
            ->from('App\Entity\Service','p')
            ->orderBy('p.note','DESC')
            ->setMaxResults(3);
        $res = $query->getQuery();
        $produitEvalues= $res->execute();
        $note = 0;
        //count
        $i = 0;

        //tableau
        $j=0;

        foreach ($produitEvalues as $pe) {
            $note = $note + $pe["note"];
            $i++;


            $noteMoy = $note / $i;

            $noteMoy = round($noteMoy);

            $prod = $em->getRepository(Service::class)->findOneBy(array('servid' => $pe['servid']));

            $produitTop[$j] = $prod;

            $j++;
        }
        return $this->render('front/top.html.twig',array('id'=>$pe['servid'],'note'=>$pe['note'],'topfive'=>$produitTop));


    }

}