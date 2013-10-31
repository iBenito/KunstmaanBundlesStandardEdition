<?php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstalmentOptionType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zizoo\BookingBundle\Form\Model\MessageToOwner',
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getParent()->getData()->getPaymentMethod();
                if ($data['method']=='credit_card') {
                    return array('booking.credit_card');
                } else {
                    return array('booking.bank_transfer');
                }
            },
        ));
    }
    
//    public function getParent()
//    {
//        return 'choice';
//    }
    
    public function getName()
    {
        return 'instalment_option';
    }
}

?>
