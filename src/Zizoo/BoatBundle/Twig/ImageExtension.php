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

    public function webPath($image, $size = 'originals')
    {
        return 'images/boats/'.$image->getBoat()->getId().'/'.$size.'/'.$image->getPath();
    }

    public function getName()
    {
        return 'image_extension';
    }
}
?>
