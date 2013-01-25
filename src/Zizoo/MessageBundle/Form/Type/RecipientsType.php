<?php
namespace Zizoo\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zizoo\MessageBundle\DataTransformer\RecipientsMultipleDataTransformer;

/**
 * Description of RecipientsType
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class RecipientsType extends AbstractType
{
    /**
     * @var RecipientsDataTransformer
     */
    private $recipientsTransformer;

    /**
     * @param RecipientsDataTransformer $transformer
     */
    public function __construct(RecipientsMultipleDataTransformer $transformer)
    {
        $this->recipientsTransformer = $transformer;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->recipientsTransformer);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected recipient does not exist'
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'zizoo_recipients_selector';
    }
}
