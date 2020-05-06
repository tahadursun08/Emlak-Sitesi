<?php

namespace App\Controller\Admin;

use App\Entity\House;
use App\Form\HouseType;
use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/house")
 */
class HouseController extends AbstractController
{
    /**
     * @Route("/", name="admin_house_index", methods={"GET"})
     */
    public function index(HouseRepository $houseRepository): Response
    {
        $houses=$houseRepository->getAllHouses();
        return $this->render('admin/house/index.html.twig', [
            'houses' => $houses,
        ]);
    }

    /**
     * @Route("/new", name="admin_house_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $house = new House();
        $form = $this->createForm(HouseType::class, $house);
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

            $entityManager->persist($house);
            $entityManager->flush();

            return $this->redirectToRoute('admin_house_index');
        }

        return $this->render('admin/house/new.html.twig', [
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
     * @Route("/{id}", name="admin_house_show", methods={"GET"})
     */
    public function show(House $house): Response
    {
        return $this->render('admin/house/show.html.twig', [
            'house' => $house,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_house_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, House $house): Response
    {
        $form = $this->createForm(HouseType::class, $house);
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

                return $this->redirectToRoute('admin_house_index');

        }

        return $this->render('admin/house/edit.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_house_delete", methods={"DELETE"})
     */
    public function delete(Request $request, House $house): Response
    {
        if ($this->isCsrfTokenValid('delete'.$house->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($house);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_house_index');
    }
}
