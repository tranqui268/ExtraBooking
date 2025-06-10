<?php 

namespace App\Repositories\RepairOrder;

use App\Repositories\RepositoryInterface;

interface RepairOrderRepositoryInterface extends RepositoryInterface{
    public function getRepairOrderLookup($data);

    public function findByAppointmentId($appointmentId);  
}