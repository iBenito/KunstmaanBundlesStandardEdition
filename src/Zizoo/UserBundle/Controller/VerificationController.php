<?php

// src/Zizoo/UserBundle/Controller/UserController.php;
namespace Zizoo\UserBundle\Controller;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Form\Type\FacebookVerificationType;

use Zizoo\UserBundle\Service\FacebookApiException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class VerificationController extends Controller
{
   
    public function verifyFacebookAction()
    {
        $user       = $this->getUser();
        $request    = $this->getRequest();
        $isPost     = $request->isMethod('POST');
        $fbRedirect = $request->query->get('fb_redirect', false);
        
        $trans      = $this->get('translator');
        
        $facebook   = $this->get('facebook');       
        $obj        = null;
        try {
            $obj = $facebook->api('/me');
        } catch (FacebookApiException $e){
            //$this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_user.verify_facebook_error'). ' ' . $e->getMessage());
            //return $this->redirect($this->generateUrl('ZizooUserBundle_Verification_VerifyFacebook'));
            //$facebook->setAccessToken(null);
        }
        
        
        
        $em = $this->getDoctrine()
                   ->getManager();
        
        $defaultData = array();
        if ($fbRedirect){
            $existingUser = $em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['email']);
            if ($existingUser && $existingUser->getId() != $user->getId()){
                $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_user.verify_facebook_error'). ' a user with email address '.$existingUser->getEmail().' already exists');
                return $this->redirect($this->generateUrl('ZizooUserBundle_Verification_VerifyFacebook'));
            }
            $defaultData = array('facebookUID' => $obj['id']);
        }

        
        $form = $this->createFormBuilder($defaultData)
            ->add('facebookUID', 'hidden')
            ->getForm();

        if ($isPost) {
            $form->bind($request);

            if ($form->isValid()){
                $data = $form->getData();
                
                $user->setFacebookUid($obj['id']);
                
                $em->persist($user);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.verify_facebook_success'));
                return $this->redirect($this->generateUrl('ZizooUserBundle_Verification_VerifyFacebook'));
            }
        }
        
        return $this->render('ZizooUserBundle:Verification:facebook.html.twig', array(  'user'                  => $user,
                                                                                        'form'                  => $form->createView(),
                                                                                        'data'                  => $obj,
                                                                                        'facebook'              => $facebook, 
                                                                                        'ajax'                  => $request->isXmlHttpRequest(),
                                                                                        'fb_redirect'           => $fbRedirect));
    }
    
    
    public function unverifyFacebookAction()
    {
        $user       = $this->getUser();
        $request    = $this->getRequest();

        $trans      = $this->get('translator');
        
        $facebook   = $this->get('facebook');       
        
        try {
            $unlinked = $facebook->api('/me/permissions', 'DELETE');
            if ($unlinked===true){
                $em = $this->getDoctrine()
                            ->getManager();
                
                $user->setFacebookUid(null);
                
                $em->persist($user);
                $em->flush();
                $facebook->setAccessToken(null);
                $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.unverify_facebook_success'). ' ' . $e->getMessage());
            } else {
                $this->get('session')->getFlashBag()->add('notice', $trans->trans('zizoo_user.unverify_facebook_error'). ' ' . $e->getMessage());
            }
            return $this->redirect($this->generateUrl('ZizooUserBundle_Verification_VerifyFacebook'));
        } catch (FacebookApiException $e){
            $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_user.unverify_facebook_error'). ' ' . $e->getMessage());
            return $this->redirect($this->generateUrl('ZizooUserBundle_Verification_VerifyFacebook'));
        } catch (\Exception $e){
            $this->get('session')->getFlashBag()->add('error', $trans->trans('zizoo_user.unverify_facebook_error'). ' ' . $e->getMessage());
            return $this->redirect($this->generateUrl('ZizooUserBundle_Verification_VerifyFacebook'));
        }
        
    }
}

?>
