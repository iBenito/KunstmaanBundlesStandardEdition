<?php
namespace Zizoo\BillingBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PayoutSettingsType extends AbstractType
{
    public function __construct() {
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder->add('payout_method', 'choice', array( 'property_path' => 'payoutMethod',
//                                                        'choices'       => array(   'bank_account'  => 'Bank Account',
//                                                                                    'paypal'        => 'PayPal'),
//                                                        'multiple'      => false,
//                                                        'expanded'      => false));
        $builder->add('payout_method', 'entity', array(
                                                'class' => 'ZizooBillingBundle:PayoutMethod',
                                                'query_builder' => function(EntityRepository $er) {
                                                    return $er->createQueryBuilder('p')
                                                        ->where('p.enabled = TRUE')
                                                        ->orderBy('p.order, p.name', 'ASC');
                                                },
                                        ));
        $builder->add('bank_account', new BankAccountType(), array('property_path' => 'bankAccount'));
        $builder->add('paypal', new PayPalType(), array('property_path' => 'payPal'));

    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'            => 'Zizoo\BillingBundle\Form\Model\PayoutSettings',
                                        'cascade_validation'    => true));
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zizoo_billing_payout_settings';
    }
}
?>
