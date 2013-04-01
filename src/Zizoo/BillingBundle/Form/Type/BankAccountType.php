<?php
namespace Zizoo\BillingBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BankAccountType extends AbstractType
{
    protected $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('account_owner', 'text', array(   'required'      => true,
                                                        'property_path' => 'accountOwner',
                                                        'label'         => 'Account Owner',
                                                        'attr'          => array('autocomplete' => 'off')));
        
        $builder->add('bank_name', 'text', array(   'required'      => true,
                                                    'property_path' => 'bankName',
                                                    'label'         => 'Bank Name',
                                                    'attr'          => array('autocomplete' => 'off')));
        
        $countryChoices = $this->em->getRepository('ZizooAddressBundle:Country')->allCountriesAsSelect();
        $builder->add('bank_country', 'choice', array(
                                                    'choices'       => $countryChoices,
                                                    'multiple'      => false,
                                                    'expanded'      => false,
                                                    'label'         => 'Bank Country',
                                                    'property_path' => 'bankCountry'
                                                ));
        
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
