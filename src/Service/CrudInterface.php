<?php

namespace App\Service;

use App\Entity\User;

interface CrudInterface
{
    public function add(array $data);
    public function delete(int $id);
    public function update(int $id, array $data);
    public function get(int $id);
}