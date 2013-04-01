<?php

namespace Zizoo\ReservationBundle\Twig;

use Zizoo\ReservationBundle\Entity\Reservation;

class ReservationExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'Reservation_numberOfDays'          => new \Twig_Filter_Method($this, 'numberOfDays'),
            'Reservation_statusToString'        => new \Twig_Filter_Method($this, 'statusToString'),
        );
    }
    
    public function numberOfDays(Reservation $reservation){
        $from   = $reservation->getCheckIn();
        $to     = $reservation->getCheckOut();
        if (!$from || !$to) return '...';
        $interval = $from->diff($to);
        return $interval->days;
    }
    
    public function statusToString(Reservation $reservation)
    {
        switch ($reservation->getStatus()){
            case Reservation::STATUS_REQUESTED:
                return 'Requested';
                break;
            case Reservation::STATUS_ACCEPTED:
                return 'Accepted';
                break;
            case Reservation::STATUS_EXPIRED:
                return 'Expired';
                break;
            case Reservation::STATUS_DENIED:
                return 'Denied';
                break;
            case Reservation::STATUS_SELF:
                return 'Reserved';
                break;
            default:
                return 'Unkown status';
                break;
        }
    }
    
    public function getName()
    {
        return 'reservation_extension';
    }
}
?>
