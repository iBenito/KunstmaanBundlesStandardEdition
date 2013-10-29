<?php
namespace Zizoo\SmsBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Zizoo\SmsBundle\Entity\BookingSmsVerify;
use Zizoo\SmsBundle\Entity\ProfileSmsVerify as ProfileSmsVerify;

use Zizoo\BookingBundle\Entity\Booking;
use Zizoo\ProfileBundle\Entity\Profile;

class SmsAgent {
    
    private $em;
    private $container;

    public function __construct($em, $container) {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Validate Code against verification
     *
     * @param $verification BookingVerify or ProfileVerify
     * @param $code integer
     * @return boolean
     */
    public function verifyCode($verification, $code)
    {
        if ($verification->getCode() === $code){
            $verification->setVerified(true);
            $this->em->persist($verification);
            $this->em->flush();
        }

        return $verification->getVerified();
    }

    /**
     * Send Verification code to Phone number
     *
     * @param $verificationId integer
     * @return string
     */
    public function sendCode($verificationId)
    {
        $verify   = $this->em->getRepository('ZizooSmsBundle:SmsVerifyBase')->findOneById($verificationId);

        $from = $this->container->getParameter('twilio.number');
        $to = $verify->getPhone();

        $twilioClient = $this->container->get('zizoo_sms.twilio');
        $message = $twilioClient->account->messages->sendMessage(
            $from,
            $to,
            "Ahoy from Zizoo! Enter ". $verify->getCode() . "on the Profile page to verify your account"
        );

        return $message->sid;
    }

    /**
     * Create a verification record for a Profile or Booking
     *
     * @param $type Profile or Booking
     * @param $id ProfileId or BookingId
     * @return string
     * @throws \InvalidArgumentException
     */
    public function createVerification($type, $id, $phone)
    {
        $entity = $this->em->getRepository($type)->findOneById($id);
        if (!$entity){
            throw new \InvalidArgumentException('Verification of ' . $type . 'is not possible!');
        }

        $verificationType = ucfirst($this->em->getClassMetadata(get_class($entity))->getTableName());
        $verificationClass = 'Zizoo\\SmsBundle\\Entity\\'.$verificationType.'SmsVerify';
        $reflectionClass = new \ReflectionClass($verificationClass);
        $verification = $reflectionClass->newInstance();

        $method = 'set'.$verificationType;
        $verification->$method($entity);

        // generate "random" 6-digit verification code
        $code = rand(100000, 999999);

        $verification->setCode($code);
        $verification->setPhone($phone);
        $this->em->persist($verification);
        $this->em->flush();

        $message = $this->sendCode($verification->getId());

        return $message;
    }

}
?>
