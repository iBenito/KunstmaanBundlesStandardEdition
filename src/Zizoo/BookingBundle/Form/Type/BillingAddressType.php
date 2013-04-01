<?php
// src/Zizoo/BookingBundle/Form/Type/UserType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BillingAddressType extends AbstractType
{
    protected $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_name', 'text', array('label'           => 'zizoo_booking.label.first_name',
                                                  'property_path'   => 'firstName'));
        
        $builder->add('last_name', 'text', array('label'            => 'zizoo_booking.label.last_name',
                                                  'property_path'   => 'lastName'));
        
        $builder->add('street_address', 'text', array('label'           => 'zizoo_booking.label.street_address',
                                                        'property_path' => 'streetAddress'));
        
        $builder->add('extended_address', 'text', array('label'         => 'zizoo_booking.label.extended_address',
                                                        'property_path' => 'extendedAddress',
                                                        'required'      => false));
        
        $builder->add('locality', 'text', array('label'             => 'zizoo_booking.label.locality',
                                                  'property_path'   => 'locality'));
        
        $builder->add('region', 'text', array('label'           => 'zizoo_booking.label.region',
                                                'required'      => false,
                                                'property_path' => 'region'));
        
        $builder->add('postal_code', 'text', array('label'              => 'zizoo_booking.label.postal_code',
                                                    'property_path'     => 'postalCode'));
        
        $countryChoices = $this->em->getRepository('ZizooAddressBundle:Country')->allCountriesAsSelect();
        
        
        $builder->add('country_code_alpha2', 'choice', array(
                                                    'choices'   => $countryChoices,
                                                    'multiple'      => false,
                                                    'expanded'      => false,
                                                    'label'         => 'zizoo_booking.label.country',
                                                    'property_path'    => 'countryCodeAlpha2'
                                                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\BillingAddress',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getParent()->getData();
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
        return 'billing';
    }
}
?>
