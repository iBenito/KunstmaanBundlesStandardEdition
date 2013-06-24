<?php
namespace Zizoo\ProfileBundle\Entity;

use Zizoo\BaseBundle\Entity\ProfileAvatar;

use Doctrine\Common\Collections\ArrayCollection;

class CollectionTest
{
    
    protected $avatar;

    public function __construct()
    {
        $this->avatar = new ArrayCollection();
    }
    
    public function addAvatar(ProfileAvatar $avatar)
    {
        $this->avatar[] = $avatar;
        return $this;
    }
    
    public function removeAvatar(ProfileAvatar $avatar)
    {
        return $this->avatar->remove($avatar);
    }
    
    public function getAvatar()
    {
        return $this->avatar;
    }
    
}
?>
