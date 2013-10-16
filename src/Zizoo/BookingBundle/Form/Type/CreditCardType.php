<?php
// src/Zizoo/BookingBundle/Form/Type/UserType.php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreditCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cardholder_name', 'text', array('label'          => array('value' => 'zizoo_booking.label.cardholder', 'class' => 'credit_card'),
                                                        'property_path' => 'cardHolder'));
        
        $builder->add('number', 'text', array('label'           => array('value' => 'zizoo_booking.label.credit_card_number', 'class' => 'credit_card'),
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
                                                    'label'         => array('value' => 'zizoo_booking.label.expiration_date_month', 'class' => 'credit_card'),
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
                                                    'label'         => array('value' => 'zizoo_booking.label.expiration_date_year', 'class' => 'credit_card'),
                                                    'attr'          => array('class' => 'sensitive'),
                                                    'property_path' => 'expiryYear'
                                                ));
        
        $builder->add('cvv', 'text', array('label'          => array('value' => 'zizoo_booking.label.cvv', 'class' => 'credit_card'),
                                            'attr'          => array('class' => 'sensitive'),
                                            'property_path' => 'cvv'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\CreditCard',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getParent()->getData();
                if ($data['method']=='credit_card') {
                    return array('booking.credit_card');
                } else {
                    return array('booking.bank_transfer');
                }
            },
        ));
    }

    public function getName()
    {
        return 'credit_card';
    }
}
?>
