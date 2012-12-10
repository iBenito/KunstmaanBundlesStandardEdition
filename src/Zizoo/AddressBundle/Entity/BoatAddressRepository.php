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
                    ->select('address.locality, address.sub_locality, address.state, address.province, country.iso as countryISO, country.printableName as countryName')
                    ->leftJoin('address.country', 'country')
                    ->addOrderBy('country.printableName, address.locality', 'asc');

         return $qb->getQuery()
                   ->getResult();
        
    }
    
}
