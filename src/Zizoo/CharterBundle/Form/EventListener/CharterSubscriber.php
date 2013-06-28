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
        return array(   FormEvents::PRE_SET_DATA    => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $charter    = $event->getData();
        $form       = $event->getForm();

        // check if the product object is "new"
        // If you didn't pass any data to the form, the data is "null".
        // This should be considered a new "Product"
        if ($charter && $charter->getId()) {
            $form->add('logo', 'zizoo_media', array(    'property_path'     => 'logo',
                                                        'label'             => 'Logo',
                                                        'file_path'         => 'webPath',
                                                        'aspect_ratio'      => 1.48,
                                                        'crop_js'           => 'logoCrop',
                                                        'delete_js'         => 'logoDelete',
                                                        'dropzone'          => array(
                                                            'upload_url'        => 'ZizooCharterBundle_Charter_SetLogo',
                                                            'upload_params'     => array(),
                                                            'upload_param_name' => 'logoFile',
                                                            'upload_error_js'   => 'logoUploadError',
                                                            'upload_success_js' => 'logoUploadSuccess',
                                                        ),
                                                      'allow_delete'      => true,
                                                      'data_class'        => 'Zizoo\CharterBundle\Entity\CharterLogo'
                                                    ))
            ->add('description', 'textarea', array('required'       => true,
                                                    'label'         => 'Description',
                                                    'property_path' => 'about'));
        }
    }
    
    
}
?>
