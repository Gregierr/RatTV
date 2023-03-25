<?php

namespace App\Service;

interface CrudInterface
{
    public function add(array $data);
    public function delete(int $id);
    public function update(int $id, array $data);
    public function get(int $id);
}