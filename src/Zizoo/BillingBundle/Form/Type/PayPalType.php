<?php
namespace Zizoo\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PayPalType extends AbstractType
{
    public function __construct() {
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('username', 'email', array(   'required'      => true,
                                                    'property_path' => 'username',
                                                    'label'         => 'PayPal E-mail',
                                                    'attr'          => array('autocomplete' => 'off')));
        

    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class'   => 'Zizoo\BillingBundle\Form\Model\PayPal');
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zizoo_billing_paypal';
    }
}
?>
