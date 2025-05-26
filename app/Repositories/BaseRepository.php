<?php
namespace App\Repositories;

use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface{
    protected $model;

    public function __construct(Model $model){
        $this->model = $model;
    }


    public function getAll(){
        return $this ->model -> all();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    public function getById($id){
        $result =  $this ->model ->find($id);
        return $result;
    }

    public function create(array $data){
        return $this->model->create($data);
    }

    public function update($id,array $data){
        $result = $this -> getById($id);
        if($result){
            $this ->model -> update($data);
            return $result;
        }
        return false;
    }

    public function delete($id){
        $result = $this -> getById($id);
        if($result){
            $this ->model -> delete();
            return $result;
        }
        return false;
    }
}