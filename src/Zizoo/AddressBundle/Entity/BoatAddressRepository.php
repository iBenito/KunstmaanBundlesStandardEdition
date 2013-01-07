<?php

namespace Zizoo\AddressBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BoatAddressRepository
 *
 */
class BoatAddressRepository extends EntityRepository
{
    
    public function getUniqueLocations(){
        
        $qb = $this->createQueryBuilder('address')
                    ->select('address.locality, address.subLocality, address.state, address.province, country.iso as countryISO, country.printableName as countryName')
                    ->leftJoin('address.country', 'country')
                    ->addOrderBy('country.printableName, address.locality', 'asc');

         return $qb->getQuery()
                   ->getResult();
        
    }
    
    public function search($search)
    {
        $qb = $this->createQueryBuilder('address')
                   ->select('address, boat, country')
                   ->leftJoin('address.boat', 'boat')
                   ->leftJoin('address.country', 'country')
                   ->where('address.locality = :search')
                   ->orWhere('address.subLocality = :search')
                   ->orWhere('address.state = :search')
                   ->orWhere('address.province = :search')
                   ->orWhere('country.printableName = :search')
                   ->setParameter('search', $search);

        return $qb->getQuery()
                  ->getResult();
    }   
    
}
