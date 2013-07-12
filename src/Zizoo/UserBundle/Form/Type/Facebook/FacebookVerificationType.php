<?php
namespace Zizoo\UserBundle\Form\Type\Facebook;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FacebookVerificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('facebookUID', 'hidden', array());
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array());
    }
    
    public function getName()
    {
        return 'zizoo_verification_facebook';
    }
}

?>
