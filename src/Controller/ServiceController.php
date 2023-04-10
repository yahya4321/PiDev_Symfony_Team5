<?php

namespace App\Controller;
use App\Entity\Service;
use App\Entity\User;
use App\Form\ServiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'display_services')]
    public function index(): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Service::class);

        $Services = $em->findAll();
        return $this->render('service/index.html.twig', ['listS'=>$Services]);
    }

    #[Route('/ajouterService', name: 'ajouterService')]
    public function ajouterService(Request $request): Response
    {

        $Services = new Service(); // init objet
        $form = $this->createForm(ServiceType::class,$Services);




        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid()) {

            $fileUpload= $form->get('servimg')->getData();

            $fileName= md5(uniqid()). '.' .$fileUpload->guessExtension();

            $fileUpload->move($this->getParameter('kernel.project_dir').'/public/uploads',$fileName);

            $Services->setServImg($fileName);

            // USER :
            $User= $this->getDoctrine()->getManager()->getRepository(User::class)->find(
                39
            );

            $em = $this->getDoctrine()->getManager(); // testiii el userID fel service mouch lezem nzidha fel table service c clé etrang  w naamel jointure ?

            $Services->setIdUser($User);// suser mel base id ta3o 39 5ater 3enach login donc logiqment chn5dmo ala user wa7d amel ajout tra taw

            $em->persist($Services);
            $em->flush();


            $this->addFlash(
                'notice', 'Service a été bien ajoutée ! '
            );

            return $this->redirectToRoute('display_services');

        }

        return $this->render('service/createService.html.twig',
            ['f'=>$form->createView()]
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
}