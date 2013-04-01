<?php

// src/Zizoo/BookingBundle/Form/Type/TermsType.php
namespace Zizoo\BookingBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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
        $builder->add('payment_method', 'entity', array(
            'class' => 'ZizooBookingBundle:PaymentMethod',
            'required'  => true,
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('m')
                    ->orderBy('m.order', 'ASC')
                    ->where('m.enabled = TRUE');
            },
        ));
        
        $builder->add('message_to_owner', new MessageToOwnerType(), array('label' => 'Message to owner'));
            
        $builder->add('credit_card', new CreditCardType(), array('label'    => 'Credit Card'));
        
        $builder->add('billing', new BillingAddressType($this->container->get('doctrine.orm.entity_manager')), array('label' => 'Billing Address'));
        
        //$builder->add('terms', 'checkbox', array('property_path' => 'termsAccepted', 'label' => 'zizoo_user.label.terms'));
        $builder->add('custom_fields', new CustomFieldsType(), array('label' => ' '));
        
    }
       
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\Booking',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                if ($data->getPaymentMethod()=='credit_card') {
                    return array('booking.credit_card');
                } else {
                    return array('booking.bank_transfer');
                }
            },
        ));
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
