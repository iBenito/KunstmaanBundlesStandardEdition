<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zizoo\BaseBundle\Entity\Feedback;
use Zizoo\BaseBundle\Form\FeedbackType;

use Zizoo\AddressBundle\Form\Model\SearchBoat;
use Zizoo\AddressBundle\Form\Type\SearchBoatType;

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
    
    public function howAction()
    {
        $user = $this->getUser();
        
        return $this->render('ZizooBaseBundle:Page:how.html.twig',array(
            'user' => $user
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

                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact enquiry from symblog')
                    ->setFrom('enquiries@symblog.co.uk')
                    ->setTo('email@email.com')
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
       
        return $this->render('ZizooBaseBundle:Page:login_widget.html.twig', array(
            // last username entered by the user
            'user' => $user,
            'logged_in' => $isLoggedIn,
            'show_login_form' => $showLoginForm
        ));
    }
    
}