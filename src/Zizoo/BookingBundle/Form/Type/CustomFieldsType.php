<?php

// src/Zizoo/BookingBundle/Form/Type/TermsType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomFieldsType extends AbstractType
{
    
    protected $router;
    
    public function __construct(ContainerInterface $container) {
        $this->router = $container->get('router');
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder->add('terms', 'zizoo_terms', array('property_path' => 'termsAccepted', 
                                                    'terms_url'     => $this->router->generate('ZizooBaseBundle_terms'),
                                                    'terms_text'    => 'zizoo_user.label.terms',
                                                    'terms_link'    => 'zizoo_user.label.terms_link'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\CustomFields',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getParent()->getData();
                if ($data->getPaymentMethod()->getID()=='credit_card') {
                    return array('booking.credit_card');
                } else {
                    return array('booking.bank_transfer');
                }
            },
        ));
    }
    
    public function getName()
    {
        return 'custom_fields';
    }
}

?>
