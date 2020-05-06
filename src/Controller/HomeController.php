<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\House;
use App\Entity\Setting;
use App\Entity\User;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\HouseRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, HouseRepository $houseRepository)
    {
        $setting=$settingRepository->findAll();
        $slider=$houseRepository->findBy(['status'=>'True'],['title'=>'DESC'],5);
        $houses=$houseRepository->findBy(['status'=>'True'],['title'=>'DESC']);
        $newhouses=$houseRepository->findBy([],['title'=>'DESC'],3);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting'=> $setting,
            'slider'=> $slider,
            'houses'=> $houses,
            'newhouses' => $newhouses,
        ]);
    }

    /**
     * @Route("/house/{id}", name="house_show", methods={"GET"})
     */
    public function show(House $house,$id,HouseRepository $houseRepository,ImageRepository $imageRepository,CommentRepository $commentRepository): Response
    {
        $images=$imageRepository->findBy(['house'=>$id]);
        $houses=$houseRepository->findBy([],['title'=>'DESC'],6);
        $comments=$commentRepository->findBy(['houseid'=>$id, 'status'=>'True']);
        return $this->render('home/houseshow.html.twig', [
            'house' => $house,
            'houses'=> $houses,
            'images'=> $images,
            'comments'=> $comments,
        ]);
    }

    /**
 * @Route("/about", name="home_about")
 */
    public function about(SettingRepository $settingRepository): Response
    {
        $setting=$settingRepository->findAll();
        return $this->render('home/aboutus.html.twig', [
            'setting'=> $setting,
        ]);
    }

    /**
     * @Route("/contact", name="home_contact", methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository,Request $request): Response
    {

        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken=$request->request->get('token');
        $setting=$settingRepository->findAll();

        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('form-messeage',$submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();

                $this->addFlash('success','Your message has been sent successfuly');

                //************** SEND EMAIL **********>>>>>>>

                /*$email = (new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject('AllHoliday Your Request')
                    ///->text('Sending emails is fun again!')
                    ->html("Dear ".$form['name']->getData() ."<br>
                            <p>We will evaluate your requests and contact you as soos as possible</p>
                            Thank You for your message <br>
                            ========================================================
                            <br>".$setting[0]->getCompany()."  <br>
                            Address : ".$setting[0]->getAddress()." <br>
                            Phone   : ".$setting[0]->getPhone()."<br>"
                    );

                $transport = new GmailTransport($setting[0]->getSmtpemail(), $setting[0]->getSmtppassword());
                $mailer = new Mailer($transport);
                $mailer->send($email);*/

                //<<<<<<<<<<<<<<<******** SEN MAIL *********************


                return $this->redirectToRoute('home_contact');
            }
        }

        return $this->render('home/contact.html.twig', [
            'setting'=> $setting,
            'form' => $form->createView(),
        ]);
    }
}
