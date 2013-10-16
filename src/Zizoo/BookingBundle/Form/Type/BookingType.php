<?php

// src/Zizoo/BookingBundle/Form/Type/TermsType.php
namespace Zizoo\BookingBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookingType extends AbstractType
{
    
    protected $container;
    protected $uniqueId;
    
    public function __construct(Container $container) {
        $this->container    = $container;
        $this->uniqueId     = uniqid();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder->add('payment_method', 'zizoo_payment_method', array('required' => 'true', 'label' => false));
        
        $builder->add('instalment_option', 'entity', array(
            'class' => 'ZizooBookingBundle:InstalmentOption',
            'required'  => true,
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('o')
                    ->orderBy('o.order', 'ASC')
                    ->where('o.enabled = TRUE');
            },
            'attr'          => array('class' => 'gray small'),
            'expanded'      => true,
            'multiple'      => false,
            'label'         => 'Instalment Options'
        ));    
            
        $builder->add('message_to_owner', new MessageToOwnerType(), array('label' => 'Message to owner'));
            
        $builder->add('billing', new BillingType(), array('label' => false));
        
        $builder->add('custom_fields', new CustomFieldsType($this->container), array('label' => false));
        
    }
       
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\Booking',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                $paymentMethod = $data->getPaymentMethod();
                if ($paymentMethod['method']=='credit_card') {
                    return array('booking.credit_card');
                } else {
                    return array('booking.bank_transfer');
                }
            },
//            'allowed_methods' => array(),
//            'default_method'  => null,
//            'predefined_data' => array(),
        ));
            
//        $resolver->setRequired(array(
//            'amount',
//            'currency',
//        ));
        
//        $resolver->setAllowedTypes(array(
//            'allowed_methods' => 'array',
//            'amount'          => array('numeric', 'closure'),
//            'currency'        => 'string',
//            'predefined_data' => 'array',
//        ));
    }
    
    public function getName()
    {
        return 'transaction';
    }
    
    public function getUniqueId()
    {
        return $this->uniqueId;
    }
}

?>
