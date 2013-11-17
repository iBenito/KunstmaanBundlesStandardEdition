<?php

namespace Zizoo\MediaBundle\Twig;

use Zizoo\MediaBundle\Entity\Media;

class MediaExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
//            'mediaWebPath' => new \Twig_Filter_Method($this, 'mediaWebPath'),
            'isImage'   => new \Twig_Function_Function(array($this, 'isImage'))
        );
    }

    public function isImage($mimeType)
    {
        if ($mimeType===null) return false;
        $match = preg_match("/image\\/(png|jpeg)/", $mimeType);
        return $match!=0;
    }
    
//    public function mediaWebPath($media, $options = array())
//    {
//        
//        if ($media instanceof ProfileAvatar){
//            $webPath = $media->$media->getWebPath();
//            
//        }
//        
//        return 'images/boats/'.$image->getBoat()->getId().'/'.$size.'/'.$image->getPath();
//    }

    public function getName()
    {
        return 'media_extension';
    }
}
?>
