<?php

namespace App\Repositories;
use App\DTOs\Request;

interface Repository
{
    public function create(Request $request);
    public function update(int $id, Request $request);
    public function getAll(): array;
    public function getById(int $id);
    public function delete(int $id): bool;
}