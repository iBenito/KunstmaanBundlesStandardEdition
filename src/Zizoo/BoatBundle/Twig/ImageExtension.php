<?php

namespace Zizoo\BoatBundle\Twig;


class ImageExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'imageWebPath' => new \Twig_Filter_Method($this, 'webPath'),
        );
    }

    public function webPath($image)
    {
        return 'images/boats/'.$image->getBoat()->getId().'/'.$image->getPath();
    }

    public function getName()
    {
        return 'image_extension';
    }
}
?>
