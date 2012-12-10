<?php

namespace Zizoo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zizoo\BaseBundle\Entity\Feedback;
use Zizoo\BaseBundle\Form\FeedbackType;

class PageController extends Controller {

    public function indexAction() 
    {
        $em = $this->getDoctrine()->getEntityManager();
        $boats = $em->getRepository('ZizooBoatBundle:Boat')->getBoats();
        
        $user = $this->getLoggedInUser();

        return $this->render('ZizooBaseBundle:Page:index.html.twig',array(
            'user' => $user,
            'boats' => $boats,
            'main' => TRUE
        ));
    }
    
    public function howAction()
    {
        $user = $this->getLoggedInUser();
        
        return $this->render('ZizooBaseBundle:Page:how.html.twig',array(
            'user' => $user
        ));
    }
    
    public function aboutAction()
    {
        $user = $this->getLoggedInUser();
        
        return $this->render('ZizooBaseBundle:Page:about.html.twig',array(
            'user' => $user
        ));
    }

    public function termsAction()
    {
        $user = $this->getLoggedInUser();
        
        return $this->render('ZizooBaseBundle:Page:terms.html.twig',array(
            'user' => $user
        ));
    }
    
    public function policiesAction()
    {
        $user = $this->getLoggedInUser();
        
        return $this->render('ZizooBaseBundle:Page:policies.html.twig',array(
            'user' => $user
        ));
    }
    
    public function feedbackAction()
    {
        $user = $this->getLoggedInUser();
        
        $feedback = new Feedback();
        $form = $this->createForm(new FeedbackType(), $feedback);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

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
        $user = $this->getLoggedInUser();
        
        return $this->render('ZizooBaseBundle:Page:faq.html.twig',array(
            'user' => $user
        ));
    }
    
    /**
     * Check if a User is logged in 
     * 
     * @return Zizoo\UserBundle\Entity\User $user
     */
    private function getLoggedInUser()
    {
        $user = null;
        
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $user = $this->getUser();
        }
        
        return $user;
    }
    
}