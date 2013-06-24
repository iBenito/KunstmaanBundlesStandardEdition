<?php

namespace Zizoo\MediaBundle\Twig;

use Zizoo\MediaBundle\Entity\Media;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

class MediaExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'mediaWebPath' => new \Twig_Filter_Method($this, 'mediaWebPath'),
        );
    }

    public function mediaWebPath($media, $options = array())
    {
        
        if ($media instanceof ProfileAvatar){
            $webPath = $media->$media->getWebPath();
            
        }
        
        return 'images/boats/'.$image->getBoat()->getId().'/'.$size.'/'.$image->getPath();
    }

    public function getName()
    {
        return 'image_extension';
    }
}
?>
