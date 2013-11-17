<?php

namespace Zizoo\UserBundle\Twig;

use Zizoo\UserBundle\Entity\User;
use Zizoo\CharterBundle\Entity\Charter;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\DisabledException;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class UserExtension extends \Twig_Extension
{
    private $em;
    private $trans;
    private $router;
    
    public function __construct(EntityManager $em, Translator $trans, Router $router) 
    {
        $this->em       = $em;
        $this->trans    = $trans;
        $this->router   = $router;
        
    }
    
    public function getFilters()
    {
        return array(
            'messageSender' => new \Twig_Filter_Method($this, 'messageSender'),
        );
    }
    
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('loginError', array($this, 'loginError')),
        );
    }
    
    

    public function messageSender(User $user)
    {
        $charter = $user->getCharter();
        if ($charter){
            return $user->getUsername() . ' at ' . $charter->getCharterName();
        } else {
            return $user->getUsername();
        }
    }
    
    public function loginError($error, $username)
    {
        if ($error === null){
            return '';
        } if ($error instanceof BadCredentialsException){
            return $this->trans->trans('zizoo_user.error.bad_credentials') . ' <a class="forgot_password_link" href="'.$this->router->generate('ZizooUserBundle_forgot_password').'">'.$this->trans->trans('zizoo_user.label.forgot_password').'</a>';
        } else if ($error instanceof DisabledException){
            $userRepo       = $this->em->getRepository('ZizooUserBundle:User');
            $emailUser      = $userRepo->findOneByEmail($username);
            $usernameUser   = $userRepo->findOneByUsername($username);
            if ($emailUser){
                $unconfirmedUser = $emailUser->getConfirmationToken()!=null?$emailUser:null;
            } else if ($usernameUser){
                $unconfirmedUser = $usernameUser->getConfirmationToken()!=null?$usernameUser:null;
            }
            if ($unconfirmedUser===null){
                return 'Please contact support';
            } else {
                return 'You need to <a class="resend_confirmation_link" href="'.$this->router->generate('ZizooUserBundle_resend_confirmation', array('email' => $unconfirmedUser->getEmail())).'">confirm your account</a>';
            }
        } else {
            return $error->getMessage();
        }
    }
    
    public function getName()
    {
        return 'user_extension';
    }
}
?>
