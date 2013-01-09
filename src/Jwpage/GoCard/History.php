<?php

namespace Jwpage\GoCard;

class History
{
    public $startTime;
    public $startLocation;
    public $endTime;
    public $endLocation;
    public $cost;

    public function __construct($startTime, $startLocation, $endTime, $endLocation, $cost)
    {
        $this->startTime     = $startTime;
        $this->startLocation = $startLocation;
        $this->endTime       = $endTime;
        $this->endLocation   = $endLocation;
        $this->cost          = $cost;
    }
}