<?php
namespace Zizoo\BillingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BankAccountType extends AbstractType
{
    protected $container;
    
    public function __construct() {
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('iban', 'text', array('required'      => true,
                                            'property_path' => 'iban',
                                            'label'         => 'IBAN',
                                            'attr'          => array('autocomplete' => 'off')));
        
        $builder->add('bic', 'text', array('required'  => true,
                                            'property_path' => 'bic',
                                            'label'         => 'BIC',
                                            'attr'          => array('autocomplete' => 'off')));
  
    }


    public function getDefaultOptions(array $options)
    {
        return array('data_class'   => 'Zizoo\BillingBundle\Form\Model\BankAccount');
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'zizoo_billing_bank_account';
    }
}
?>
