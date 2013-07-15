<?php

namespace Zizoo\ProfileBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

use Zizoo\MessageBundle\Entity\Message;
use Zizoo\MessageBundle\Entity\MessageRecipient;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Service\ProfileService;
use Zizoo\UserBundle\Entity\User;

class ProfileExtension extends \Twig_Extension
{
    
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
            'threadDisplay'         => new \Twig_Filter_Method($this, 'threadDisplay'),
            'profileCompleteness'   => new \Twig_Filter_Method($this, 'profileCompleteness'),
        );
    }
    
    

    public function threadDisplay($message, $profile){
        if ($message->getSenderProfile()->getId()==$profile->getId()){
            return $profile->getUser()->getUsername();
        } else {
            $messageRecipients = $message->getRecipients();
            foreach ($messageRecipients as $messageRecipient){
                $recipientProfile = $messageRecipient->getRecipientProfile();
                if ($recipientProfile->getId()==$profile->getId()){
                    return $message->getSenderProfile()->getUser()->getUsername();
                }
            }
        }
    }
    
    private function profileCompletenessLevel($profileCompleteness, $level)
    {
        $class = $profileCompleteness >= $level ? ' class="complete"':'';
        return "<li$class><span>$level</span></li>";
    }
    
    public function profileCompleteness(Profile $profile)
    {
        $profileService = $this->container->get('profile_service');
        $profileCompleteness = $profileService->getCompleteness($profile);
        ?>
        <h4>Profile Completeness <?php if ($profileCompleteness <= ProfileService::MAX_PROFILE_COMPLETENESS){ ?><span class="icon cross"></span><?php } ?></h4>
        <ul class="clearfix">
            <?php 
            for ($i=1; $i<=ProfileService::MAX_PROFILE_COMPLETENESS; $i++){
                echo $this->profileCompletenessLevel($profileCompleteness, $i);
            }
            ?>
        </ul>
        <?php
    }
    
    public function getName()
    {
        return 'profile_extension';
    }
}
?>
