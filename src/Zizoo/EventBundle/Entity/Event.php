<?php

namespace Zizoo\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Zizoo\EventBundle\Entity\EventRepository")
 */
class Event
{
    
    const STATUS_NEW        = 0;
    const STATUS_RUNNING    = 1;
    const STATUS_COMPLETE   = 2;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="text")
     */
    private $command;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=true)
     */
    private $parameters;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="result", type="smallint", nullable=true)
     */
    private $result;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="retries", type="smallint", nullable=true)
     */
    private $retry;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="smallint", nullable=true)
     */
    private $year;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="smallint", nullable=true)
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="day_of_month", type="smallint", nullable=true)
     */
    private $day_of_month;

    /**
     * @var integer
     *
     * @ORM\Column(name="day_of_week", type="smallint", nullable=true)
     */
    private $day_of_week;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="hour", type="smallint", nullable=true)
     */
    private $hour;

    /**
     * @var integer
     *
     * @ORM\Column(name="minute", type="smallint", nullable=true)
     */
    private $minute;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_run", type="datetime", nullable=true)
     */
    private $lastRun;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return Event
     */
    public function setCommand($command)
    {
        $this->command = $command;
    
        return $this;
    }

    /**
     * Get command
     *
     * @return string 
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return Event
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    
        return $this;
    }

    /**
     * Get parameters
     *
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Event
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set result
     *
     * @param integer $result
     * @return Event
     */
    public function setResult($result)
    {
        $this->result = $result;
    
        return $this;
    }

    /**
     * Get result
     *
     * @return integer 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set retry
     *
     * @param integer $retry
     * @return Event
     */
    public function setRetry($retry)
    {
        $this->retry = $retry;
    
        return $this;
    }

    /**
     * Get retry
     *
     * @return integer 
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Event
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year?$this->year:'*';
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return Event
     */
    public function setMonth($month)
    {
        $this->month = $month;
    
        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month?$this->month:'*';
    }

    /**
     * Set day of month
     *
     * @param integer $day
     * @return Event
     */
    public function setDayOfMonth($day)
    {
        $this->day_of_month = $day;
    
        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDayOfMonth()
    {
        return $this->day_of_month?$this->day_of_month:'*';
    }
    
    /**
     * Set day of month
     *
     * @param integer $day
     * @return Event
     */
    public function setDayOfWeek($day)
    {
        $this->day_of_week = $day;
    
        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDayOfWeek()
    {
        return $this->day_of_week?$this->day_of_week:'*';
    }

    /**
     * Set hour
     *
     * @param integer $hour
     * @return Event
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    
        return $this;
    }

    /**
     * Get hour
     *
     * @return integer 
     */
    public function getHour()
    {
        return $this->hour?$this->hour:'*';
    }

    /**
     * Set minute
     *
     * @param integer $minute
     * @return Event
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
    
        return $this;
    }

    /**
     * Get minute
     *
     * @return integer 
     */
    public function getMinute()
    {
        return $this->minute?$this->minute:'*';
    }

    /**
     * Set lastRun
     *
     * @param \DateTime $lastRun
     * @return Event
     */
    public function setLastRun($lastRun)
    {
        $this->lastRun = $lastRun;
    
        return $this;
    }

    /**
     * Get lastRun
     *
     * @return \DateTime 
     */
    public function getLastRun()
    {
        return $this->lastRun;
    }
}
