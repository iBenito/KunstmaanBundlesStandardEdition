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
class BoatTypeType extends AbstractType
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
        /**
        $em = $this->container->get('doctrine.orm.entity_manager');
        $boatTypes = $em->getRepository('ZizooBoatBundle:BoatType')->findAll();
        $boatTypeChoices = array();
        foreach ($boatTypes as $boatType){
            $boatTypeChoices[$boatType->getId()] = $boatType->getName();
        }
        $builder->addModelTransformer($this->container->get('zizoo_boat.boat_type_data_transformer'));
         * */
        $queryBuilderClosure = function(EntityRepository $er)
        {
            return $er->createQueryBuilder('boat_type')
                        ->orderBy('boat_type.orderNum', 'ASC');
        };
        $builder->add('boat_type', 'entity', array(
            'class'     => 'ZizooBoatBundle:BoatType',
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
            'invalid_message'   => 'The selected boat type does not exist',
            'expanded'          => false,
            'multiple'          => false
        ));
    }


    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'zizoo_boat_type_selector';
    }
}
