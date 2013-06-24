<?php

namespace Zizoo\UserBundle\Twig;


use Zizoo\UserBundle\Entity\User;
use Zizoo\CharterBundle\Entity\Charter;

class UserExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'messageSender' => new \Twig_Filter_Method($this, 'messageSender'),
        );
    }
    
    

    public function messageSender(User $user){
        $charter = $user->getCharter();
        if ($charter){
            return $user->getUsername() . ' at ' . $charter->getCharterName();
        } else {
            return $user->getUsername();
        }
    }
    
    public function getName()
    {
        return 'user_extension';
    }
}
?>
