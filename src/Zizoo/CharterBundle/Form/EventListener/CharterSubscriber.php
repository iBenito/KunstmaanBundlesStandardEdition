<?php
// src/Zizoo/CharterBundle/Form/EventListener/CharterSubscriber.php
namespace Zizoo\CharterBundle\Form\EventListener;

use Zizoo\CharterBundle\Entity\CharterLogo;

use Zizoo\MediaBundle\Form\Type\MediaType;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CharterSubscriber implements EventSubscriberInterface
{
    protected $em;
    protected $uploadLogoFile;
    protected $oldLogo;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(   FormEvents::PRE_SET_DATA    => 'preSetData',
                        FormEvents::BIND            => 'bind',
                        FormEvents::POST_BIND       => 'postBind');
    }

    public function preSetData(FormEvent $event)
    {
        $charter    = $event->getData();
        $form       = $event->getForm();

        // check if the product object is "new"
        // If you didn't pass any data to the form, the data is "null".
        // This should be considered a new "Product"
        if ($charter && $charter->getId()) {
            $form->add('logo', 'zizoo_media', array(  'property_path'     => 'logo',
                                                      'label'             => 'Logo',
                                                      'file_path'         => 'webPath',
                                                      'aspect_ratio'      => 1.48,
                                                      'allow_delete'      => false,
                                                      'data_class'        => 'Zizoo\CharterBundle\Entity\CharterLogo'
                                                    ))
            ->add('logo_file', 'file', array(   'required'      => false, 
                                                'label'         => 'New',
                                                'property_path' => 'logoFile'))
            ->add('description', 'textarea', array('required'       => true,
                                                    'label'         => 'Description',
                                                    'property_path' => 'about'));
        }
    }
    
    public function postBind(FormEvent $event)
    {
        $charter = $event->getData();
        $form = $event->getForm();
        
        if (null !== $charter->getLogoFile()){
            if ($form->isValid()){
                
                // Get backup logo and delete if it is already persisted, i.e. has ID
                $oldLogo = $charter->getLogo();
                if ($oldLogo->getId()){
                    $this->em->remove($oldLogo);
                    
                } 

                //$logo = $charter->getLogo();
                //$this->em->persist($logo);
                $logo = $this->oldLogo;
                $logo->setCharter($charter);
                $charter->setLogo($logo);
                        
                $this->em->flush();
                
                $charter->getLogoFile()->move(
                    $logo->getUploadRootDir(),
                    $logo->getId().'.'.$logo->getPath()
                );
                
//                $logo = $this->uploadLogo;
//                
//                
//                $logo = $charter->getLogo();
                
                

            } else {
                // Not valid so re-persist backup logo if it has ID
//                $logo = $this->oldLogo;
//                
//                if ($logo->getId()){
//                    $charter->setLogo($logo);
//                    $logo->setCharter($charter);
//
//                    $this->em->persist($logo);
//                    
//                    $this->em->flush();
//                }
                
                
            }
            
            $charter->setLogoFile(null);
        }
        
        if (!$form->isValid()){
            $errors = $form->getErrors();
            
        }

    }
    
    public function bind(FormEvent $event)
    {
        $charter = $event->getData();
        $form = $event->getForm();

        if (null === $charter->getLogoFile()){
            
           $logo       = $charter->getLogo();
           if (!$logo->getId()) {
               
                $charter->setLogo(null);
                $this->em->persist($charter);
                
           } 

        } else {
            
            
            //$logo = $charter->getLogo();
            $logo = new CharterLogo();
                       
            // Set logo to new upload
            $logo->setOrder(0);
            $logoFile   = $charter->getLogoFile();
            $logo->setPath($logoFile->guessExtension());
            $logo->setMimeType($logoFile->getMimeType());
            //$logo->setCharter($charter);
            
            //$charter->setLogo($logo);
                        
            $validator = $this->getValidator();
            $
            
            $this->em->persist($logo);
            
            $this->oldLogo = $logo;
            //$this->em->flush();
        }
        
    }
}
?>
