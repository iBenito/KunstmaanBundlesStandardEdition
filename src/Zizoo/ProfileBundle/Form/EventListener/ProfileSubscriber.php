<?php
// src/Zizoo/ProfileBundle/Form/EventListener/ProfileSubscriber.php
namespace Zizoo\ProfileBundle\Form\EventListener;

use Zizoo\ProfileBundle\Entity\ProfileAvatar;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProfileSubscriber implements EventSubscriberInterface
{
    protected $em;
    protected $uploadAvatar;
    
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
        $profile = $event->getData();
        $form = $event->getForm();
        
        if (null !== $profile->getAvatarFile()){
            if ($form->isValid()){
                $avatar = $this->uploadAvatar;
                $this->em->flush();
                $profile->getAvatarFile()->move(
                    $avatar->getUploadRootDir(),
                    $avatar->getId().'.'.$avatar->getPath()
                );

            }
            
            $profile->setAvatarFile(null);
        }
        
        if (!$form->isValid()){
            $errors = $form->getErrors();
            
        }

    }
    
    public function bind(FormEvent $event)
    {
        $profile = $event->getData();
        $form = $event->getForm();

        if (null !== $profile->getAvatarFile()){
            
            $avatar = new ProfileAvatar();
            $avatar->setProfile($profile);
            $profile->addAvatar($avatar);

            $this->em->persist($avatar);
            
            $avatarFile = $profile->getAvatarFile();
            $avatar->setPath($avatarFile->guessExtension());
            $avatar->setMimeType($avatarFile->getMimeType());
            
            $this->uploadAvatar = $avatar;

        }
        
    }
}
?>
