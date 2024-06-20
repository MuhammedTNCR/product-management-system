<?php

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function create(array $params);
    public function index(array $params);
    public function search(array $params);
}
