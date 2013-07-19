<?php


namespace Zizoo\UserBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotBlankValidator;

class AccountSettingsValidator extends ConstraintValidator
{

    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function validate($accountSettings, Constraint $constraint)
    {
        $trans = $this->container->get('translator');
        
        if (null === $token = $this->container->get('security.context')->getToken()) {
            $this->context->addViolationAt('password', 'Error', array(), null);
        }

        if (!is_object($user = $token->getUser())) {
            $this->context->addViolationAt('password', 'Error', array(), null);
        }
        
        if ($accountSettings->getPassword()!=null){
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $allegedCurrentPassword = $encoder->encodePassword($accountSettings->getPassword(), $user->getSalt());
            if ($allegedCurrentPassword!=$user->getPassword()){
                $this->context->addViolationAt('password', $trans->trans('zizoo_user.message.account_settings_not_changed'), array(), null);
            }
        }
        
    }
    
}