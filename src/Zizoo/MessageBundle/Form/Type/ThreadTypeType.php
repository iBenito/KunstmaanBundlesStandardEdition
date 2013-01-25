<?php
namespace Zizoo\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zizoo\MessageBundle\Form\DataTransformer\ThreadTypeTransformer;

/**
 * Description of RecipientsType
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class ThreadTypeType extends AbstractType
{
    /**
     * @var ThreadTypeTransformer
     */
    private $threadTypeTransformer;

    /**
     * @param ThreadTypeTransformer $transformer
     */
    public function __construct(ThreadTypeTransformer $transformer)
    {
        $this->threadTypeTransformer = $transformer;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->threadTypeTransformer);
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
        return 'zizoo_thread_type_selector';
    }
}
