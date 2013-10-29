<?php

namespace Zizoo\SmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function verifyAction(Request $request, $message = '')
    {
        $routes     = $request->query->get('routes');

        $user       = $this->getUser();
        $profile    = $user->getProfile();

        $profileVerified = NULL;
        $verificationFormView = NULL;

        $verificationType = $this->get('zizoo_verify.verify_type');
        $verificationForm = $this->createForm($verificationType);

        $profileVerification = $profile->getVerification();

        if ($request->isMethod('post')){
            $verificationForm->bind($request);
            $verification = $verificationForm->getData();

            $verificationAgent = $this->get('zizoo_verify.sms_agent');
            $validity = $verificationAgent->verifyCode($profileVerification, $verification->getCode());

            $message = ($validity ? 'Great Success' : 'Code Invalid');

            $this->redirect($this->generateUrl($routes['verify_phone_route'], array('message' => $message)));
        }

        if($profileVerification){
            $profileVerified = $profileVerification->getVerified();

            if (!$profileVerified){
                $verificationFormView = $verificationForm->createView();
            }
        }

        return $this->render('ZizooSmsBundle:Default:index.html.twig', array(
            'profile' => $profile,
            'profile_verified' => $profileVerified,
            'verification_form' => $verificationFormView,
            'message' => $message
        ));
    }

    public function sendCodeAction($profileId) {
        $profile   = $this->getDoctrine()->getManager()->getRepository('ZizooProfileBundle:Profile')->findOneById($profileId);
        $verificationAgent = $this->get('zizoo_verify.sms_agent');

        $profileVerification = $profile->getVerification();
        if($profileVerification){
            $message = $verificationAgent->sendCode($profileVerification->getId());
        }
        else{
            $message = $verificationAgent->createVerification('ZizooProfileBundle:Profile', $profileId, $profile->getPhone());
        }

        return $this->redirect($this->generateUrl('ZizooSmsBundle_Default_Verify', array('message' => $message)));
    }
}
