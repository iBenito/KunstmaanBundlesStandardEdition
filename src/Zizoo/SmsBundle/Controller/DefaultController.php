<?php

namespace Zizoo\SmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $from = $this->container->getParameter('twilio.number');
        $to = '+4369918126210';

        $twilioClient = $this->container->get('zizoo_sms.twilio');
        $message = $twilioClient->account->messages->sendMessage(
            $from, // From a valid Twilio number
            $to, // Text this number
            "Hello monkey!"
        );

        $id = $message->sid;

        return $this->render('ZizooSmsBundle:Default:index.html.twig', array('name' => $id));
    }
}
