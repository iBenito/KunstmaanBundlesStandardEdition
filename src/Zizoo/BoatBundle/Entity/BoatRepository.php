<?php

namespace Zizoo\BoatBundle\Entity;

use Zizoo\BoatBundle\Entity\Price;
use Zizoo\CharterBundle\Entity\Charter;
use Zizoo\BoatBundle\Extensions\DoctrineExtensions\CustomWalker\SortableNullsWalker;
use Zizoo\AddressBundle\Form\Model\SearchBoat;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
/**
 * BoatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BoatRepository extends EntityRepository
{
    public function getBoats($limit = null)
    {
        $qb = $this->createQueryBuilder('b')
                   ->select('b')
                   ->where('b.active = true')
                   ->addOrderBy('b.created', 'DESC');

        if (false === is_null($limit))
            $qb->setMaxResults($limit);

        return $qb->getQuery()
                  ->getResult();
    }
    
    /**
     * 
     * @param string $search      Optional location search value
     * @param string $resFrom     Optional date from
     * @param string $resTo       Optional date until
     * @param string $numGuests   Optional number of guests
     * @return Doctrine\ORM\AbstractQuery[] Results
     * @author Alex Fuckert <alexf83@gmail.com>
     */
    public function searchBoats(SearchBoat $searchBoat)
    {
        // Join boat, image, address, country and reservation
        $qb = $this->createQueryBuilder('boat')
                   ->select('boat, image, address, country, reservation, price, boat_type, equipment')
                   ->leftJoin('boat.image', 'image')
                   ->leftJoin('boat.address', 'address')
                   ->leftJoin('boat.reservation', 'reservation')
                   ->leftJoin('boat.price', 'price')
                   ->leftJoin('boat.address', 'boat_address')
                   ->leftJoin('address.country', 'country')
                   ->leftJoin('boat.boatType', 'boat_type')
                   ->leftJoin('boat.equipment', 'equipment');
        
        // Optionally search by boat location or boat availability location
        $firstWhere = true;
        if ($searchBoat->getLocation()){
            $qb->where('address.locality = :search')
               ->orWhere('address.subLocality = :search')
               ->orWhere('address.state = :search')
               ->orWhere('address.province = :search')
               ->orWhere('country.printableName = :search')
               ->setParameter('search', $searchBoat->getLocation());
            $firstWhere = false;
        }
        
        // Optionally restrict by boat type
        
        // Optionally restrict by number of guests
        if ($searchBoat->getNumGuests()){
            if ($firstWhere){
                $qb->where('boat.nr_guests >= :num_guests');
            } else {
                $qb->andWhere('boat.nr_guests >= :num_guests');
            }
            $qb->setParameter('num_guests', $searchBoat->getNumGuests());
            $firstWhere = false;
        }
        
        $filter = $searchBoat->getFilter();
        if ($filter){
            // Optionally restrict by boat length
            if ($filter->getLengthFrom()){
                if ($firstWhere){
                    $qb->where('boat.length >= :length_from');
                } else {
                    $qb->andWhere('boat.length >= :length_from');
                }
                $qb->setParameter('length_from', $filter->getLengthFrom());
                $firstWhere = false;
            }
            if ($filter->getLengthTo()){
                if ($firstWhere){
                    $qb->where('boat.length <= :length_to');
                } else {
                    $qb->andWhere('boat.length <= :length_to');
                }
                $qb->setParameter('length_to', $filter->getLengthTo());
                $firstWhere = false;
            }

            // Optionally restrict by number of cabins
            if ($filter->getNumCabinsFrom()){
                if ($firstWhere){
                    $qb->where('boat.cabins >= :num_cabins_from');
                } else {
                    $qb->andWhere('boat.cabins >= :num_cabins_from');
                }
                $qb->setParameter('num_cabins_from', $filter->getNumCabinsFrom());
                $firstWhere = false;
            }
            if ($filter->getNumCabinsTo()){
                if ($firstWhere){
                    $qb->where('boat.cabins <= :num_cabins_to');
                } else {
                    $qb->andWhere('boat.cabins <= :num_cabins_to');
                }
                $qb->setParameter('num_cabins_to', $filter->getNumCabinsTo());
                $firstWhere = false;
            }

            // Optionally restrict by boat type
            if ($filter->boatTypeSelected()){           
                $boatTypes = $filter->getBoatType();
                $boatTypeIds = array();
                foreach ($boatTypes as $boatType){
                    $boatTypeIds[] = $boatType->getId();
                }
                if ($firstWhere){
                    $qb->where('boat.boatType IN (:boat_types)');
                } else {
                    $qb->andWhere('boat.boatType IN (:boat_types)');
                }
                $qb->setParameter('boat_types', $boatTypeIds);
                $firstWhere = false;

            }
            
            // Optionally restrict by equipment
            if ($filter->equipmentSelected()){           
                $equipment = $filter->getEquipment();
                $equipmentIds = array();
                foreach ($equipment as $e){
                    $equipmentIds[] = $e->getId();
                }
                if ($firstWhere){
                    $qb->where('equipment.id IN (:e)');
                } else {
                    $qb->andWhere('equipment.id IN (:e)');
                }
                $qb->setParameter('e', $equipmentIds);
                $firstWhere = false;

            }
            
            // Optionally restrict by price
            if ($filter->getPriceFrom()){
                if ($firstWhere){
                    if ($searchBoat->getReservationFrom() && $searchBoat->getReservationTo()){
                        $qb->where('price.price >= :price_from AND price.available >= :res_from AND price.available < :res_to');
                        $qb->setParameter('res_from', $searchBoat->getReservationFrom());
                        $qb->setParameter('res_to', $searchBoat->getReservationTo());
                    } else {
                        $qb->where('price.price >= :price_from OR boat.defaultPrice >= :price_from');
                    }
                } else {
                    if ($searchBoat->getReservationFrom() && $searchBoat->getReservationTo()){
                        $qb->andWhere('price.price >= :price_from AND price.available >= :res_from AND price.available < :res_to');
                        $qb->setParameter('res_from', $searchBoat->getReservationFrom());
                        $qb->setParameter('res_to', $searchBoat->getReservationTo());
                    } else {
                        $qb->andWhere('price.price >= :price_from OR boat.defaultPrice >= :price_from');
                    }
                }
                $qb->setParameter('price_from', (float)$filter->getPriceFrom());
                $firstWhere = false;
            }
            if ($filter->getPriceTo()){
                if ($firstWhere){
                    if ($searchBoat->getReservationFrom() && $searchBoat->getReservationTo()){
                        $qb->where('price.price <= :price_to AND price.available >= :res_from AND price.available < :res_to');
                        $qb->setParameter('res_from', $searchBoat->getReservationFrom());
                        $qb->setParameter('res_to', $searchBoat->getReservationTo());
                    } else {
                        $qb->where('price.price <= :price_to OR boat.defaultPrice <= :price_to');
                    }
                } else {
                    if ($searchBoat->getReservationFrom() && $searchBoat->getReservationTo()){
                        $qb->andWhere('price.price <= :price_to AND price.available >= :res_from AND price.available < :res_to');
                        $qb->setParameter('res_from', $searchBoat->getReservationFrom());
                        $qb->setParameter('res_to', $searchBoat->getReservationTo());
                    } else {
                        $qb->andWhere('price.price <= :price_to OR boat.defaultPrice <= :price_to');
                    }
                }
                $qb->setParameter('price_to', (float)$filter->getPriceTo());
                $firstWhere = false;
            }
        }
        
        if ($firstWhere){
            $qb->where('boat.active = true');
            $firstWhere = false;
        } else {
            $qb->andWhere('boat.active = true');
        }
        
        $qb->addOrderBy('reservation.id', 'asc');

        
        return $qb->getQuery()
                  ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Zizoo\BoatBundle\Extensions\DoctrineExtensions\CustomWalker\SortableNullsWalker')
                  ->setHint('SortableNullsWalker.fields',
                        array(
                            'reservation.id' => SortableNullsWalker::NULLS_LAST,
                        ))
                  ->getResult();
    }
    
    
    public function getMaxBoatValues($boat = null){
        $qb = $this->createQueryBuilder('boat')
                   ->select('MAX(boat.cabins) as max_cabins, MAX(boat.length) as max_length, MAX(boat.defaultPrice) as max_default_price, MIN(boat.lowestPrice) as min_lowest_price');
        if ($boat){
            $qb->where('boat = :boat');
            $qb->setParameter('boat', $boat);
        }
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function getLatestCharterBoats(Charter $charter, $pageSize, $page)
    {
        $start = ($page-1)*($pageSize-1);
        $limit = $pageSize;
        $qb = $this->createQueryBuilder('boat')
                   ->leftJoin('boat.charter', 'charter')
                   ->select('boat')
                   ->where('charter = :charter')
                   ->setParameter('charter', $charter)
                   ->setFirstResult($start)
                   ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }
    
    public function getNumberOfCharterBoats(Charter $charter)
    {
        $qb = $this->createQueryBuilder('boat')
                   ->leftJoin('boat.charter', 'charter')
                   ->select('COUNT(boat.id) as num_boats')
                   ->where('charter = :charter')
                   ->setParameter('charter', $charter);
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    /**
    public function getPrices(Boat $boat, $from, $to){
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Zizoo\BoatBundle\Entity\Price', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'available_from', 'available_from');
        $rsm->addFieldResult('p', 'available_until', 'available_until');
        $rsm->addFieldResult('p', 'price', 'price');
        $rsm->addFieldResult('p', 'date_range', 'date_range');
        $rsm->addFieldResult('p', 'date_range_before', 'date_range_before');
        $rsm->addFieldResult('p', 'date_range_after', 'date_range_after');
        $rsm->addMetaResult('p', 'boat_id', 'boat');

        $query = $this->_em->createNativeQuery('SELECT id, available_from, available_until, price, boat_id,'
                                                .' CASE
                                                        WHEN :from >= available_from AND :until <= available_until THEN DATEDIFF(:until, :from)
                                                        WHEN :from >= available_from AND :from < available_until AND :until > available_until THEN DATEDIFF(available_until, :from)
                                                        WHEN :from < available_from AND :until >= available_from AND :until <= available_until THEN DATEDIFF(:until, available_from)
                                                        WHEN :from < available_until AND :until > available_until THEN DATEDIFF(available_until, available_from)
                                                END AS date_range,'
                                                .' CASE
                                                        WHEN :from >= available_from AND :until <= available_until THEN DATEDIFF(available_from, :from)
                                                        WHEN :from >= available_from AND :from < available_until AND :until > available_until THEN NULL
                                                        WHEN :from < available_from AND :until >= available_from AND :until <= available_until THEN DATEDIFF(available_from, :from)
                                                        WHEN :from < available_until AND :until > available_until THEN DATEDIFF(available_from, :from)
                                                END AS date_range_before,'
                                                .' CASE
                                                        WHEN :from >= available_from AND :until <= available_until THEN NULL
                                                        WHEN :from >= available_from AND :from < available_until AND :until > available_until THEN DATEDIFF(:until, available_until)
                                                        WHEN :from < available_from AND :until >= available_from AND :until <= available_until THEN NULL
                                                        WHEN :from < available_until AND :until > available_until THEN DATEDIFF(:until, available_until)
                                                END AS date_range_after'
                                                .' FROM boat_price'
                                                .' WHERE boat_id = :boat_id'
                                                .' ORDER BY available_from ASC',
                                                $rsm);
        
        $query->setParameter('from', $from);
        $query->setParameter('until', $to);
        $query->setParameter('boat_id', $boat->getId());

        $prices = $query->getResult();
        
        return $prices;
    }*/
        
    
}
