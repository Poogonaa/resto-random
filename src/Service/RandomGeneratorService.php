<?php

namespace App\Service;

use App\Repository\RestaurantRepository;
use Exception;

class RandomGeneratorService
{
    private RestaurantRepository $repository;

    public function __construct(RestaurantRepository $repository)
    {
        $this->repository = $repository;
    }

    public function randomGenerator(): ?int
    {
        $restaurants = $this->repository->findBy([]);
        if (sizeof($restaurants) == 0) {
            return -1;
        } else {
            try {
                return $restaurants[random_int(0, sizeof($restaurants) - 1)]->getId();
            } catch (Exception $e) {
                return -2;
            }
        }
    }
}
