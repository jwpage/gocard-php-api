<?php

namespace Jwpage\GoCard;

/**
 * A class to represent Go Card history information. 
 */
class History
{
    /**
     * @var \DateTime
     */
    public $startTime;
    /**
     * @var string 
     */
    public $startLocation;
    /**
     * @var \DateTime
     */
    public $endTime;
    /**
     * @var string 
     */
    public $endLocation;
    /**
     * @var float 
     */
    public $cost;

    /**
     * Creates a new History item
     * 
     * @param \DateTime $startTime 
     * @param string    $startLocation 
     * @param \DateTime $endTime 
     * @param string    $endLocation 
     * @param float     $cost 
     */
    public function __construct($startTime, $startLocation, $endTime, $endLocation, $cost)
    {
        $this->startTime     = $startTime;
        $this->startLocation = $startLocation;
        $this->endTime       = $endTime;
        $this->endLocation   = $endLocation;
        $this->cost          = $cost;
    }
}