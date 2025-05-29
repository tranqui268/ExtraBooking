<?php 

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Repositories\BaseRepository;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface{
    public function __construct(Employee $employee){
        parent::__construct($employee);
    }

    public function filters($filters)
    {
        
    }

    public function softDelete($id)
    {
        
    }

    public function getEmployeeByUserId($userId){
        return Employee::where('user_id',$userId)->first();
    }
}