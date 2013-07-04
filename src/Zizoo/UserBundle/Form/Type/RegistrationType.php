<?php

// src/Zizoo/UserBundle/Form/Type/RegistrationType.php
namespace Zizoo\UserBundle\Form\Type;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    protected $container;
    
    public function __construct(Container $container) 
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $router     = $this->container->get('router');

        $builder->add('profile', new ProfileType(), array('label' => false));
        $builder->add('user', new UserType(), array('label' => false));
        $builder->add('terms', 'zizoo_terms', array('property_path' => 'termsAccepted', 
                                                    'terms_url'     => $router->generate('ZizooBaseBundle_terms'),
                                                    'terms_text'    => 'zizoo_user.label.terms',
                                                    'terms_link'    => 'zizoo_user.label.terms_link'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\UserBundle\Form\Model\Registration',
                                        'cascade_validation'    => true,
                                        'validation_groups'     => array('registration')));
    }
    
    public function getName()
    {
        return 'zizoo_registration';
    }
}

?>
