<?php

namespace App\Entity;

use App\Repository\CarRepository;

class CarCustoPrice
{
    const ENGINE = [
        0 => 0,
        1 => 0.1,
        2 => 0.15,
        3 => 0.2,
        4 => 0.25
    ];

    private float $engine = 0;

    public function getEngine(): float
    {
        return $this->engine;
    }

    public function setEngine(float $engine): CarCustoPrice
    {
        $this->engine = $engine;
        return $this;
    }
}
