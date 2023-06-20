<?php

namespace App\Services;

interface ResourceServiceInterface
{
    public function all(): mixed;
    public function getById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
}
