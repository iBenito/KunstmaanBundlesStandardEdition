<?php
namespace Zizoo\BoatBundle\Form\Type;

use Zizoo\BoatBundle\Form\DataTransformer\BoatTypeTransformer;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of RecipientsType
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class EquipmentType extends AbstractType
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $queryBuilderClosure = function(EntityRepository $er)
        {
            return $er->createQueryBuilder('boat_equipment')
                        ->orderBy('boat_equipment.order', 'ASC');
        };
        $builder->add('equipment', 'entity', array(
            'class'     => 'ZizooBoatBundle:Equipment',
            'property'  => 'name',
            'query_builder' => $queryBuilderClosure,
            'expanded'  => $options['expanded'],
            'multiple'  => $options['multiple']
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message'   => 'The selected equipment does not exist',
            'expanded'          => false,
            'multiple'          => false
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'zizoo_equipment_selector';
    }
}
