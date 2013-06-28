<?php

namespace Zizoo\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;

use Liip\ImagineBundle\Imagine\Cache\CacheClearer;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\CharterBundle\Entity\CharterLogo;

use Zizoo\BoatBundle\Entity\Boat;
use Zizoo\BoatBundle\Entity\BoatImage;

class MediaController extends Controller
{
    
    
    private function getMediaEntity(Request $request, EntityManager $em)
    {
        $user       = $this->getUser();
        $profile    = $user->getProfile();
        $charter    = $user->getCharter();
               
        $id         = $request->request->get('id');
        $entityType = $request->request->get('entity_type');
        
        try {
            $mediaEntity    = $em->getRepository($entityType)->findOneById($id);
        } catch (\Exception $e){
            return new JsonResponse(array('error' => $e->getMessage()), 400);
        }
        
        if (!$mediaEntity){
            return new JsonResponse(array('error' => 'Media Entity with ID '.$id.' of type '.$entityType.' not found'), 400);
        }
        
        if ($mediaEntity instanceof ProfileAvatar){
            if (!$profile->getAvatar()->contains($mediaEntity)){
                return new JsonResponse(array('error' => 'Not allowed'), 400);
            }
        } else if ($mediaEntity instanceof CharterLogo){
            if (!$charter || $charter->getLogo()!=$mediaEntity){
                return new JsonResponse(array('error' => 'Not allowed'), 400);
            }
        } else if ($mediaEntity instanceof BoatImage){
            $boat = $mediaEntity->getBoat();
            if (!$charter || !$charter->getBoats()->contains($boat)){
                return new JsonResponse(array('error' => 'Not allowed'), 400);
            }
        }
        
        return $mediaEntity;
    }
    
    public function deleteMediaAction()
    {
        $request    = $this->getRequest();
        $em         = $this->getDoctrine()->getManager();
        $mediaEntity = $this->getMediaEntity($request, $em);
        
        $parentClass = get_parent_class($mediaEntity);
        if ($parentClass !== 'Zizoo\MediaBundle\Entity\Media'){
            return $mediaEntity;
        }
        
        if ($mediaEntity instanceof ProfileAvatar){
            $profile = $mediaEntity->getProfile();
            $profile->removeAvatar($mediaEntity);
            $em->persist($profile);
        } else if ($mediaEntity instanceof CharterLogo){
            $charter = $mediaEntity->getCharter();
            $charter->setLogo(null);
            $em->persist($charter);
        } else if ($mediaEntity instanceof BoatImage){
            $boat = $mediaEntity->getBoat();
            $boat->removeImage($mediaEntity);
            $em->persist($boat);
        } else {
            return new JsonResponse(array('error' => 'Not allowed'), 400);
        }
        
        $em->remove($mediaEntity);
        $em->flush();
        
        return new JsonResponse(array('success' => 'Media Entity with ID '.$mediaEntity->getId().' was successfully deleted'));   
    }
    
    public function cropMediaAction()
    {
        $request    = $this->getRequest();
        $em         = $this->getDoctrine()->getManager();
        $mediaEntity = $this->getMediaEntity($request, $em);
        
        $parentClass = get_parent_class($mediaEntity);
        if ($parentClass !== 'Zizoo\MediaBundle\Entity\Media'){
            return $mediaEntity;
        }
        
        $x1         = $request->request->get('x1');
        $y1         = $request->request->get('y1');
        $x2         = $request->request->get('x2');
        $y2         = $request->request->get('y2');
        $w          = $request->request->get('w');
        $h          = $request->request->get('h');
        
        if ($x1!==null && $y1!==null && $x2!==null && $y2!==null && $w!==null && $h!==null){
            $imagine = new Imagine();
            $path = $mediaEntity->getAbsolutePath();
            $image = $imagine->open($path);
            $image->crop(new Point($x1, $y1), new Box($w, $h))
                    ->save($path);
            
            $liipImageCacheManager = $this->container->get('liip_imagine.cache.manager');
            $liipImagineFilterSets = $this->container->getParameter('liip_imagine.filter_sets');
            foreach ($liipImagineFilterSets as $filterSetName => $filterSetData){
                $liipImageCacheManager->remove($mediaEntity->getWebPath(), $filterSetName);
            }
            
            $mediaEntity->setUpdated(new \DateTime());
            $em->persist($mediaEntity);
            $em->flush();
        }
        
        return new JsonResponse(array('success' => 'Media Entity with ID '.$mediaEntity->getId().' was successfully cropped'));   
        
    }
    

}
?>
