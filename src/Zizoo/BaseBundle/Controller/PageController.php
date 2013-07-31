<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zizoo\BaseBundle\Entity\Feedback;
use Zizoo\BaseBundle\Form\FeedbackType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Zizoo\AddressBundle\Form\Model\SearchBoat;
use Zizoo\AddressBundle\Form\Type\SearchBoatType;

use Zizoo\UserBundle\Form\Model\Registration;

/**
 * Default controller. For single actions for project
 *
 * @author Benito Gonzalez <vbenitogo@gmail.com>
 */
class PageController extends Controller {

    public function indexAction() 
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $boats = $em->getRepository('ZizooBoatBundle:Boat')->getBoats(3);
        
        $user = $this->getUser();
        
        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        
        return $this->render('ZizooBaseBundle:Page:index.html.twig',array(
            'user' => $user,
            'boats' => $boats,
            'main' => true,
            'form' => $form->createView()
        ));
    }
    
    public function howAction($what)
    {
        $user = $this->getUser();

        $form = $this->createForm(new SearchBoatType($this->container), new SearchBoat());
        
        return $this->render('ZizooBaseBundle:Page:how.html.twig',array(
            'user' => $user,
            'form' => $form->createView(),
            'what' => $what
        ));
    }
    
    public function aboutAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Page:about.html.twig',array(
            'user' => $user
        ));
    }

    public function termsAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Page:terms.html.twig',array(
            'user' => $user
        ));
    }
    
    public function policiesAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Page:policies.html.twig',array(
            'user' => $user
        ));
    }
    
    /**
     * Feedback form
     * 
     * @return Response
     */
    public function feedbackAction()
    {
        $user = $this->getUser();
        
        $feedback = new Feedback();
        $form = $this->createForm(new FeedbackType(), $feedback);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $to = $this->container->getParameter('email_info');
                
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact enquiry from ' . $feedback->getName())
                    ->setFrom($feedback->getEmail())
                    ->setTo($to)
                    ->setBody($this->renderView('ZizooBaseBundle:Page:feedbackEmail.txt.twig', array('feedback' => $feedback)));
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('zizoo-notice', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('ZizooBaseBundle_feedback'));
            }
        }

        return $this->render('ZizooBaseBundle:Page:feedback.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
        
    }
    
    public function faqAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Page:faq.html.twig',array(
            'user' => $user
        ));
    }
    
    /**
     * Displays the login widget.
     * 
     * @param boolean $showLoginForm    True to show login form directly in widget if user not logged in.
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function loginWidgetAction($showLoginForm=false)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $isLoggedIn = false;
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $isLoggedIn = true;
        }
        
        $user = $this->getUser();
        $routeName = $request->get('_route');
        $facebook = $this->get('facebook');

        $registration   = new Registration();
        $form = $this->createForm('zizoo_registration', $registration);
       
        if ($user && $user->getCharter()){
            return $this->render('ZizooBaseBundle:Page:login_charter_widget.html.twig', array(
                // last username entered by the user
                'user'              => $user,
                'logged_in'         => $isLoggedIn,
                'show_login_form'   => $showLoginForm
            ));
        } else {
            return $this->render('ZizooBaseBundle:Page:login_user_widget.html.twig', array(
                // last username entered by the user
                'user'              => $user,
                'logged_in'         => $isLoggedIn,
                'show_login_form'   => $showLoginForm,
                'current_route'     => $routeName,
                'facebook'          => $facebook,
                'ajax'              => $request->isXmlHttpRequest(),
                'registerForm'      => $form->createView()
            ));
        }
    }
    
    
    public function loginAction()
    {
        $request    = $this->getRequest();
        $response   = $this->forward('ZizooUserBundle:User:login');
        
        if ($response->isRedirect()){
            return $this->redirect($this->generateUrl($request->get('_route')));
        }
        
        return $this->render('ZizooBaseBundle:Page:login.html.twig', array(
            'response'  => $response->getContent()
        ));
    }
//    
//    public function test1Action()
//    {
//        $user       = $this->getUser();
//        $profile    = $user->getProfile();
//        $em         = $this->getDoctrine()->getEntityManager();
//        
//        $test = new \Zizoo\BaseBundle\Entity\Test();
//        
//
//        
//        
//        $em->persist($test);
//        
//        
//        sleep(10);
//        //$test->setTest('test1');
//        
//        
//        $em->flush();
//        return $this->render('ZizooBaseBundle:Test:test.html.twig', array(
//                
//        ));
//        
//    }
//    
//    
//    public function test2Action()
//    {
//        $em         = $this->getDoctrine()->getEntityManager();
//        $test = new \Zizoo\BaseBundle\Entity\Test();
//        $test->setTest('test2');
//        $em->persist($test);
//        $em->flush();
//        return $this->render('ZizooBaseBundle:Test:test.html.twig', array(
//                
//        ));
//    }
    
}