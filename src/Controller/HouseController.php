<?php

namespace App\Controller;

use App\Entity\House;
use App\Form\House1Type;
use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/house")
 */
class HouseController extends AbstractController
{
    /**
     * @Route("/", name="user_house_index", methods={"GET"})
     */
    public function index(HouseRepository $houseRepository): Response
    {
        $user=$this->getUser(); //Get login user data
        return $this->render('house/index.html.twig', [
            'houses' => $houseRepository->findBy(['userid'=>$user->getId()]),
        ]);
    }

    /**
     * @Route("/new", name="user_house_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $house = new House();
        $form = $this->createForm(House1Type::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //***************file upload****>>>>>>>>>
            /** @var file $file */
            $file = $form['image']->getData();

            if ($file) {
                // this is needed to safely include the file name as part of the URL
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), //Servis.yaml tanımladığımız resim yolu
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $house->setImage($fileName);
            }
            //<<<<<<<<<<*****file upload end*********>

            $user=$this->getUser(); //Get login user data
            $house->setUserid($user->getId());
            $house->setStatus('New');
            $entityManager->persist($house);
            $entityManager->flush();

            return $this->redirectToRoute('user_house_index');
        }

        return $this->render('house/new.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_house_show", methods={"GET"})
     */
    public function show(House $house): Response
    {
        return $this->render('house/show.html.twig', [
            'house' => $house,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_house_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, House $house): Response
    {
        $form = $this->createForm(House1Type::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var file $file */
            $file = $form['image']->getData();

            if ($file) {
                // this is needed to safely include the file name as part of the URL
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), //Servis.yaml tanımladığımız resim yolu
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $house->setImage($fileName);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_house_index');
        }

        return $this->render('house/edit.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="user_house_delete", methods={"DELETE"})
     */
    public function delete(Request $request, House $house): Response
    {
        if ($this->isCsrfTokenValid('delete'.$house->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($house);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_house_index');
    }
}
