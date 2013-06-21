<?php
namespace Zizoo\ProfileBundle\Entity;

use Zizoo\ProfileBundle\Entity\ProfileAvatar;

class SingleTest
{
    
    protected $avatar;

    public function __construct(ProfileAvatar $avatar)
    {
        $this->avatar = $avatar;
    }
    
    public function setAvatar(ProfileAvatar $avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }
    
    public function getAvatar()
    {
        return $this->avatar;
    }
    
}
?>
