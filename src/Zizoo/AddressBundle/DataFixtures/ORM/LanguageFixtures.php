<?php

namespace Zizoo\AddressBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zizoo\AddressBundle\Entity\Language;

class LanguageFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        return;
        $getLanguages = file_get_contents(dirname(__FILE__).'/languages.json');
       
        $languages = json_decode($getLanguages);
        
        foreach ($languages as $language)
        {
            $languageEntity = new Language();
            $languageEntity->setLanguageCode($language->language_code);
            $languageEntity->setName($language->name);
            $languageEntity->setNativeName($language->native_name);
            
            $manager->persist($languageEntity);
            
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 0;
    }

}