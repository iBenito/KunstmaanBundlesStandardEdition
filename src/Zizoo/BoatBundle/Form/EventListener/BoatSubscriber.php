<?php
// src/Zizoo/BoatBundle/Form/EventListener/BoatSubscriber.php
namespace Zizoo\BoatBundle\Form\EventListener;

use Zizoo\BoatBundle\Entity\BoatImage;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BoatSubscriber implements EventSubscriberInterface
{
    protected $em;
    protected $uploadImage;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(   FormEvents::BIND        => 'bind',
                        FormEvents::POST_BIND   => 'postBind');
    }

    public function postBind(FormEvent $event)
    {
        $boat = $event->getData();
        $form = $event->getForm();
        
        if (null !== $boat->getImageFile()){
            if ($form->isValid()){
                $image = $this->uploadImage;
                $this->em->flush();
                $boat->getImageFile()->move(
                    $image->getUploadRootDir(),
                    $image->getId().'.'.$image->getPath()
                );

            }

            $boat->setImageFile(null);
        }
        
        if (!$form->isValid()){
            $errors = $form->getErrors();
            
        }

    }
    
    public function bind(FormEvent $event)
    {
        $boat = $event->getData();
        $form = $event->getForm();

        if (null !== $boat->getImageFile()){
            
            $image = new BoatImage();
            $image->setBoat($boat);
            $boat->addImage($image);

            $this->em->persist($image);
            
            $imageFile = $boat->getImageFile();
            $image->setPath($imageFile->guessExtension());
            $image->setMimeType($imageFile->getMimeType());
            
            $this->uploadImage = $image;

        }
        
    }
}
?>
