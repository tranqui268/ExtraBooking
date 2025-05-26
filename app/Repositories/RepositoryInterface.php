<?php
namespace App\Repositories;

interface RepositoryInterface{
    public function getAll();

    public function paginate($perPage = 10);

    public function getById($id);

    public function create(array $data);

    public function update($id, array $data);

    public function filters($filters);
    public function softDelete($id);

    public function delete($id);
}