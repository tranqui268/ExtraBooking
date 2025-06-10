<?php

namespace   App\Repositories\RepairOrder;

use App\Models\Appointment;
use App\Models\RepairOrder;
use App\Repositories\BaseRepository;

class RepairOrderRepository extends BaseRepository implements RepairOrderRepositoryInterface{

    public function __construct(RepairOrder $repairOrder){
        parent::__construct($repairOrder);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }

    public function getRepairOrderLookup($data){
        $input = $data['input'];
        $repairOrders = RepairOrder::whereHas('appointment', function($query) use ($input){
            $query->whereHas('customer', function($q) use ($input){
                $q->where('phone',$input);
            })
            ->orWhereHas('vehicle', function($q) use ($input){
                $q->where('license_plate',$input);
            });
        })
        ->with([
            'repairOrderPart.part',
            'review',
            'appointment' => function ($q){
                $q->select('id','appointment_date','service_id','total_amount')
                  ->with([
                    'service:id,service_name'
                  ]);
            }
        ])
        ->orderByDesc(
            Appointment::select('appointment_date')
            ->whereColumn('appointments.id','repair_orders.appointment_id')
            ->take(5)
        )
        ->get();

        return $repairOrders;
    }
    
    public function findByAppointmentId($appointmentId)
    {
        return RepairOrder::where('appointment_id', $appointmentId)->first();
    }

}