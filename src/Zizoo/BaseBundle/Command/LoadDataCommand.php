<?php
namespace Zizoo\BaseBundle\Command;

use Zizoo\UserBundle\Entity\User;
use Zizoo\UserBundle\Entity\Group;
use Zizoo\AddressBundle\Entity\Country;
use Zizoo\AddressBundle\Entity\Marina;
use Zizoo\AddressBundle\Entity\ProfileAddress;
use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\Profile\NotificationSettings;
use Zizoo\MessageBundle\Entity\MessageType;
use Zizoo\BoatBundle\Entity\Equipment;
use Zizoo\BoatBundle\Entity\IncludedExtra;
use Zizoo\BoatBundle\Entity\BoatType;
use Zizoo\BookingBundle\Entity\PaymentMethod;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

use Doctrine\Common\Collections\ArrayCollection;

class LoadDataCommand extends ContainerAwareCommand
{
    private $container, $em;   
    private $groups, $users, $countries, $marinas, $message_types;
    
    protected function configure()
    {
        $this
            ->setName('zizoo:load')
            ->setDescription('Load necessary data into Zizoo')
            ->setDefinition(array(
                new InputOption(
                    'force', null, InputOption::VALUE_NONE,
                    'Test.'
                ),
            ));
    }

    private function createGroup($role)
    {
        $group = new Group();
        $group->setId($role);
        $group->setRole($role);
        return $group;
    }
    
    private function loadGroups()
    {
        $this->groups = array();
        $groupArr = array('ROLE_ZIZOO_USER', 'ROLE_ZIZOO_CHARTER', 'ROLE_ZIZOO_CHARTER_ADMIN', 'ROLE_ZIZOO_ADMIN', 'ROLE_ZIZOO_SUPER_ADMIN');
        foreach ($groupArr as $groupEl){
            $group = $this->createGroup($groupEl);
            $this->em->persist($group);
            $this->groups[$groupEl] = $group;
        }
    }
        
    private function createSuperAdminUser($username, $email, $password, $firstName, $lastName, $country)
    {
        
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setSalt(md5(time()));
        $password = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($password);
        $user->setIsActive(true);
        
        $profile = new Profile();
        $profile->setFirstName($firstName);
        $profile->setLastName($lastName);
        $profile->setCreated(new \DateTime());
        $profile->setUpdated($profile->getCreated());
        
        $profileNotificationSettings = new NotificationSettings();
        $profileNotificationSettings->setBooked(false);
        $profileNotificationSettings->setBooking(false);
        $profileNotificationSettings->setCreated(false);
        $profileNotificationSettings->setEnquiry(false);
        $profileNotificationSettings->setMessage(false);
        $profileNotificationSettings->setReview(false);
        $profileNotificationSettings->setCreated(new \DateTime());
        $profileNotificationSettings->setUpdated($profileNotificationSettings->getCreated());
        $profile->setNotificationSettings($profileNotificationSettings);
        
        $profileAddress = new ProfileAddress();
        $profileAddress->setCountry($this->countries[$country]);
        $profileAddress->setProfile($profile);
        
        $profile->addAddresse($profileAddress);
        $user->setProfile($profile);
        
        
        
        $user->addGroup($this->groups['ROLE_ZIZOO_SUPER_ADMIN']);
        
        $profile->setUser($user);
        
        $this->em->persist($user);
        $this->em->persist($profile);
        $this->em->persist($profileAddress);
    }
    
    private function loadSuperAdminUsers()
    {
        $this->users = array();
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        
        $getUsers = file_get_contents(dirname(__FILE__).'/Data/super_admin_users.json');
        
        $users = json_decode($getUsers);
        
        foreach ($users as $userObj)
        {
            //$this->createSuperAdminUser($userObj->username, $userObj->email, $userObj->password, $userObj->profile->first_name, $userObj->profile->last_name, $userObj->profile->country);
            $user = new User();
            $user->setUsername($userObj->username);
            $user->setEmail($this->container->getParameter($userObj->email));
            $user->setSalt(md5(time()));
            $password = $encoder->encodePassword($userObj->password, $user->getSalt());
            $user->setPassword($password);
            $user->setIsActive(true);

            $profile = new Profile();
            $profile->setFirstName($userObj->profile->first_name);
            $profile->setLastName($userObj->profile->last_name);
            $profile->setCreated(new \DateTime());
            $profile->setUpdated($profile->getCreated());
            $profile->setUser($user);
                        
            $user->setProfile($profile);
            
            $profileAddress = new ProfileAddress();
            $profileAddress->setCountry($this->countries[$userObj->profile->country]);
            $profileAddress->setProfile($profile);
            $profile->addAddresse($profileAddress);

            $user->addGroup($this->groups['ROLE_ZIZOO_SUPER_ADMIN']);
            
            
            $this->em->persist($user);
            $this->em->persist($profile);
            $this->em->persist($profileAddress);
        }
        
    }
    
    
    private function loadCountries()
    {
        $this->countries = array();
        
        $getCountries = file_get_contents(dirname(__FILE__).'/Data/countries.json');
       
        $countries = json_decode($getCountries);
        
        foreach ($countries as $countryObj)
        {
            $country = new Country();
            $country->setIso($countryObj->iso);
            $country->setIso3($countryObj->iso3);
            $country->setName($countryObj->name);
            $country->setNumcode($countryObj->numcode);
            $country->setPrintableName($countryObj->printable_name);
            $country->setOrder($countryObj->order_num);
            $this->countries[$countryObj->iso] = $country;
            $this->em->persist($country);
        }
    }
    
    private function loadMarinas()
    {
        $getMarinas = file_get_contents(dirname(__FILE__).'/Data/marinas.json');
       
        $marinas = json_decode($getMarinas);
        
        foreach ($marinas as $marina)
        {
            $marinaEntity = new Marina();
            $marinaEntity->setName($marina->name);
            $marinaEntity->setLat($marina->latitude);
            $marinaEntity->setLng($marina->longitude);
            
            $this->marinas[] = $marinaEntity;
            $this->em->persist($marinaEntity);
        }
    }
    
    private function loadEquipment()
    {
        $getEquipment = file_get_contents(dirname(__FILE__).'/Data/boat_equipment.json');
       
        $equipment = json_decode($getEquipment);
        
        foreach ($equipment as $e)
        {
            $equipmentEntity = new Equipment();
            $equipmentEntity->setId($e->id);
            $equipmentEntity->setName($e->name);
            $equipmentEntity->setOrder($e->order);
            
            $this->em->persist($equipmentEntity);
        }
    }
    
    private function loadPaymentMethods()
    {
        $getPaymentMethods = file_get_contents(dirname(__FILE__).'/Data/payment_methods.json');
       
        $paymentMethods = json_decode($getPaymentMethods);
        
        foreach ($paymentMethods as $p)
        {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setId($p->id);
            $paymentMethod->setName($p->name);
            $paymentMethod->setOrder($p->order);
            $paymentMethod->setEnabled(true);
            
            $this->em->persist($paymentMethod);
        }
    }
    
    private function loadIncludedExtras()
    {
        $getIncludedExtras = file_get_contents(dirname(__FILE__).'/Data/included_extras.json');
       
        $includedExtras = json_decode($getIncludedExtras);
        
        foreach ($includedExtras as $e)
        {
            $includedExtra = new IncludedExtra();
            $includedExtra->setId($e->id);
            $includedExtra->setName($e->name);
            $includedExtra->setOrder($e->order);
            
            $this->em->persist($includedExtra);
        }
    }
    
    
    private function loadMessageTypes()
    {
        /**
         *  Inquiry
            Expired
            Declined
            Accepted
            Paid
            Canceled 
         */
        
        $this->em->persist(new MessageType('inquiry', 'Inquiry'));

        $this->em->persist(new MessageType('expired', 'Expired'));
        
        $this->em->persist(new MessageType('declined', 'Declined'));
        
        $this->em->persist(new MessageType('accepted', 'Accepted'));
        
        $this->em->persist(new MessageType('paid', 'Paid'));
        
        $this->em->persist(new MessageType('canceled', 'Canceled'));
        
    }
    
    private function loadBoatTypes()
    {
        $getBoatTypes = file_get_contents(dirname(__FILE__).'/Data/boat_types.json');
       
        $boatTypes = json_decode($getBoatTypes);
        
        foreach ($boatTypes as $b)
        {
            $boatType = new BoatType();
            $boatType->setId($b->id);
            $boatType->setName($b->name);
            $boatType->setOrder($b->order);
            
            $this->em->persist($boatType);
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $force = (true === $input->getOption('force'));
        
        if (!$force){
            throw new \InvalidArgumentException('You must specify the --force option to carry out this command, but please be careful!');
        }
        
        $this->container    = $this->getContainer();
        $this->em           = $this->container->get('doctrine.orm.entity_manager');     
        
        $this->loadCountries();
        $this->loadMarinas();
        $this->loadEquipment();
        $this->loadIncludedExtras();
        $this->loadBoatTypes();
        $this->loadPaymentMethods();
        $this->loadGroups();
        $this->loadMessageTypes();
        $this->loadSuperAdminUsers();
        
        $this->em->flush();
    }
}
?>
