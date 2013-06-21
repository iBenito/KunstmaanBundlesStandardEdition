<?php
// src/Zizoo/BaseBundle/Form/DataTransformer/MediaToFileTransformer.php
namespace Zizoo\BaseBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\BaseBundle\Entity\Media;

class MediaToFileTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($media)
    {
        if (null === $media) {
            return "";
        }

        return $media->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $number
     *
     * @return Issue|null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $media = $this->om
            ->getRepository('ZizooBaseBundle:Media')
            ->findOneBy(array('id' => $id))
        ;

        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                'A media entity with id "%d" does not exist!',
                $id
            ));
        }

        return $media;
    }
}
?>
