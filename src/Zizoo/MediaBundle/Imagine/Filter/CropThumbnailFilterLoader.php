<?php
namespace Zizoo\MediaBundle\Imagine\Filter;

use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\ThumbnailFilterLoader;
use Liip\ImagineBundle\Imagine\Filter\Loader\CropFilterLoader;


use Imagine\Image\ImageInterface;

class CropThumbnailFilterLoader implements LoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        $cropClass = $this->container->get('liip_imagine.filter.loader.crop.class');
        $thumClass = $this->container->get('liip_imagine.filter.loader.thumbnail.class');
        
        $cropFilter     = new $cropClass;
        $thumbFilter    = new $thumClass;
        
        $image = $cropFilter->load($image, $options);
        $image = $thumbFilter->load($image, $options);

        return $image;
    }
}
