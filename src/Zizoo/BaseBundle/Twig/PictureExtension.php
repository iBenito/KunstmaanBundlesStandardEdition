<?php

namespace Zizoo\BaseBundle\Twig;


class PictureExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'pictureWebPath' => new \Twig_Filter_Method($this, 'webPath')
        );
    }

    public function webPath($picture)
    {
        return $picture->getWebPath();
    }

    public function getName()
    {
        return 'picture_extension';
    }
}
?>
