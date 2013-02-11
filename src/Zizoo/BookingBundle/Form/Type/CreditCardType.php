<?php
// src/Zizoo/BookingBundle/Form/Type/UserType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreditCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cardholder_name', 'text', array('label'          => 'zizoo_booking.label.cardholder',
                                                        'property_path' => 'cardHolder'));
        
        $builder->add('number', 'text', array('label'           => 'zizoo_booking.label.credit_card_number',
                                                'attr'          => array('class' => 'sensitive'),
                                                'property_path' => 'creditCardNumber'));
        
        $builder->add('expiration_month', 'choice', array(
                                                    'choices'   => array(
                                                        '01'    => '01 (Jan)',
                                                        '02'    => '02 (Feb)',
                                                        '03'    => '03 (Mar)',
                                                        '04'    => '04 (Apr)',
                                                        '05'    => '05 (May)',
                                                        '06'    => '06 (Jun)',
                                                        '07'    => '07 (Jul)',
                                                        '08'    => '08 (Aug)',
                                                        '09'    => '09 (Sep)',
                                                        '10'    => '10 (Oct)',
                                                        '11'    => '11 (Nov)',
                                                        '12'    => '12 (Dec)'
                                                    ),
                                                    'multiple'      => false,
                                                    'expanded'      => false,
                                                    'label'         => 'zizoo_booking.label.expiration_date_month',
                                                    'attr'          => array('class' => 'sensitive'),
                                                    'property_path' => 'expiryMonth'
                                                ));
        
        $now = new \DateTime();
        $y = $now->format('Y');
        $yearChoices = array($y => $y);
        for ($i=0; $i<15; $i++){
            $y = $now->format('Y');
            $yearChoices[$y] = $y;
            $now = $now->modify('+1 year');
        }
        
        $builder->add('expiration_year', 'choice', array(
                                                    'choices'       => $yearChoices,
                                                    'multiple'      => false,
                                                    'expanded'      => false,
                                                    'label'         => 'zizoo_booking.label.expiration_date_year',
                                                    'attr'          => array('class' => 'sensitive'),
                                                    'property_path' => 'expiryYear'
                                                ));
        
        $builder->add('cvv', 'text', array('label'          => 'zizoo_booking.label.cvv',
                                            'attr'          => array('class' => 'sensitive'),
                                            'property_path' => 'cvv'));
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Zizoo\BookingBundle\Form\Model\CreditCard',
                     'validation_groups' => 'booking');
    }

    public function getName()
    {
        return 'credit_card';
    }
}
?>
