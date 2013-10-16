<?php
namespace Zizoo\BookingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankTransferType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
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
        return 'bank_transfer';
    }
}
?>
