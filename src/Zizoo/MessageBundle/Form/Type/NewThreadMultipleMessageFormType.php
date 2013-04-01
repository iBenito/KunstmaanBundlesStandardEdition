<?php
namespace Zizoo\MessageBundle\Form\Type;

use Zizoo\MessageBundle\Entity\Thread;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Message form type for starting a new conversation with multiple recipients
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormType extends AbstractType
{
    
    protected $em;
    protected $container;
    
    public function __construct(EntityManager $em, Container $container) {
        $this->em = $em;
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user       = $this->container->get('security.context')->getToken()->getUser();
        $em         = $this->container->get('doctrine.orm.entity_manager');
        
        $security   = $this->container->get('security.context');
        
        $userChoices = array();
        $userIsAdmin = $security->isGranted('ROLE_ZIZOO_ADMIN', $user);
        if ($userIsAdmin){
            $users          = $em->getRepository('ZizooUserBundle:User')->getAllExcept($user);
            foreach ($users as $user){
                $userChoices[$user->getUsername()] = $user->getUsername();
            }
        } else {
            $contacts       = $user->getContactsWithMe();
            foreach ($contacts as $contact){
                if (!$contact->getBlockedAt()){
                    $userChoices[$contact->getSender()->getUsername()] = $contact->getSender()->getUsername();
                }
            }
        }
        
        $messageTypes    = $em->getRepository('ZizooMessageBundle:MessageType')->findAll();
        $messageTypeChoices = array();
        foreach ($messageTypes as $messageType){
            $messageTypeChoices[$messageType->getId()] = $messageType->getName();
        }
        
        $builder
            ->add('recipients', 'zizoo_recipients_selector', array( 'choices'   => $userChoices,
                                                                    'multiple'  => true))
            ->add('subject', 'text')
            ->add('body', 'textarea')
            ->add('message_type', 'zizoo_message_type_selector', array( 'choices'     => $messageTypeChoices));
    }

    public function getName()
    {
        return 'zizoo_user_username_multiple';
    }
}
