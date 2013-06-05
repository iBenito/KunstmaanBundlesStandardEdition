<?php
namespace Zizoo\BillingBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankAccountType extends AbstractType
{

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
        
        $builder->add('country', 'entity', array(
                                            'class' => 'ZizooAddressBundle:Country',
                                            'query_builder' => function(EntityRepository $er) {
                                                return $er->createQueryBuilder('c')
                                                    ->orderBy('c.order, c.printableName', 'ASC');
                                            },
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


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(   'data_class'   => 'Zizoo\BillingBundle\Form\Model\BankAccount',
                                        'validation_groups' => function(FormInterface $form) {
                                            $data = $form->getParent()->getData();
                                            if ($data->getPayoutMethod()=='bank_account') {
                                                return array('payout_settings.bank_account');
                                            } else {
                                                return array('payout_settings.paypal');
                                            }
                                        }));
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
