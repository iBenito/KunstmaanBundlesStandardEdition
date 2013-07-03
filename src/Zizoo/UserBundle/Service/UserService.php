<?php
namespace Zizoo\UserBundle\Service;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Entity\Group;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\AddressBundle\Entity\ProfileAddress;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class UserService
{
    private $em;
    private $container;
    
    public function __construct(EntityManager $em, ContainerInterface $container) {
        $this->em = $em;
        $this->container = $container;
        
        require_once $this->container->getParameter('braintree_path').'/lib/Braintree.php';
        \Braintree_Configuration::environment($this->container->getParameter('braintree_environment'));
        \Braintree_Configuration::merchantId($this->container->getParameter('braintree_merchant_id'));
        \Braintree_Configuration::publicKey($this->container->getParameter('braintree_public_key'));
        \Braintree_Configuration::privateKey($this->container->getParameter('braintree_private_key'));
    }
    
    public function confirmUser($token, $email){
        $user = $this->em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        
        if ($user && $user->getConfirmationToken()===$token){
            $user->setConfirmationToken(null);
            $user->setIsActive(1);
            
            $this->em->persist($user);
            $this->em->flush();
            
            $this->getPaymentUser($user);
            
            return $user;
        } else {
            return null;
        }
    }
    
    public function getPaymentUser(User $user){
        try {
            $customer = null;
            try {
                $customer = \Braintree_Customer::find($user->getId());
            } catch (\Braintree_Exception_NotFound $e){
                $profile = $user->getProfile();
                $customer = \Braintree_Customer::create(array(
                    'id'        => $user->getId(),
                    'firstName' => $profile->getFirstName(),
                    'lastName'  => $profile->getLastName(),
                    'email'     => $user->getEmail()
                ));
                return $customer;
            }
            return $customer;        
        } catch (\Exception $e){
            return null;
        }
    }
    
    public function updatePaymentUser(User $user){
        try {
            $profile = $user->getProfile();
            $updateResult = Braintree_Customer::update(
                $user->getId(),
                array(
                    'firstName' => $profile->getFirstName(),
                    'lastName'  => $profile->getLastName(),
                    'email'     => $user->getEmail()
              )
            );
            return $updateResult;
        } catch (\Exception $e){
            return null;
        }
    }
    
    public function registerUser(User $user, Profile $profile, $charter=false){
        $user->setSalt(md5(time()));
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($password);
        $user->setConfirmationToken(uniqid());
        
        $groupRepo = $this->em->getRepository('ZizooUserBundle:Group');
        if ($charter){
            $user->addGroup($groupRepo->findOneByRole('ROLE_ZIZOO_CHARTER_ADMIN'));
        } else {
            $user->addGroup($groupRepo->findOneByRole('ROLE_ZIZOO_USER'));
        }
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setProfile($profile);
        $profile->setAddress($profileAddress);
        
        $profile->setCreated($user->getCreated());
        $profile->setUpdated($user->getUpdated());
        $profile->setUser($user);
        
        $this->em->persist($user);
        $this->em->persist($profile);
        $this->em->persist($profileAddress);
        $this->em->flush();
        
        return $user;
    }
    
    public function registerFacebookUser(User $user, $obj){
        if ($user->getFacebookUID()==$obj['id'] && $user->getEmail()==$obj['email']){
            $user->setSalt(md5(time()));
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setIsActive(1);

            $zizooUserGroup = $this->em->getRepository('ZizooUserBundle:Group')->findOneByRole('ROLE_ZIZOO_USER');

            $user->addGroup($zizooUserGroup);

            $profile = new Profile();
            $profile->setFirstName($obj['first_name']);
            $profile->setLastName($obj['last_name']);
            $profile->setCreated(new \DateTime());
            $profile->setUpdated($profile->getCreated());
            $profile->setUser($user);
            $user->setProfile($profile);
            
            $this->em->persist($zizooUserGroup);
            $this->em->persist($profile);
            $this->em->persist($user);
            $this->em->flush();
            
            $this->getPaymentUser($user);
            
            return $user;
        } else {
            return null;
        }
    }
    
    public function linkFacebookUser(User $linkUser, $obj){
        $existingUser = $this->em->getRepository('ZizooUserBundle:User')->findOneByEmail($obj['email']);
        if (!$existingUser) return null;
        
        if ($linkUser->getFacebookUID()==$obj['id'] && $linkUser->getEmail()==$obj['email']){
            $existingUser->setFacebookUID($obj['id']);
            $existingUser->setIsActive(1);
            $existingUser->setConfirmationToken(null);
            $this->em->persist($existingUser);
            $this->em->flush();
            
            $this->getPaymentUser($existingUser);
            
            return $existingUser;
        } else {
            return null;
        }
    }
    
    public function changeEmail(User $user, $newEmail)
    {
        $user->setChangeEmailToken(uniqid());
        $user->setNewEmail($newEmail);
        
        $this->em->persist($user);
        $this->em->flush();
    }
    
    public function confirmChangeEmail($token, $email){
        $user = $this->em->getRepository('ZizooUserBundle:User')->findOneByEmail($email);
        
        if ($user && $user->getChangeEmailToken()===$token){
            $user->setChangeEmailToken(null);
            $user->setEmail($user->getNewEmail());
            $user->setNewEmail(null);
            
            $this->em->persist($user);
            $this->em->flush();
            
            return $user;
        } else {
            return null;
        }
    }
    
    
}


?>
