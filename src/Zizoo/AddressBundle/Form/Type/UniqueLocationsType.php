<?php
// src/Zizoo/AddressBundle/Form/Type/UniqueLocationsType.php
namespace Zizoo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UniqueLocationsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('current', $options['current']);
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => 'Zizoo\AddressBundle\Form\Model\UniqueLocation',
            'current'       => '-1'
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'zizoo_unique_locations';
    }
}
?>