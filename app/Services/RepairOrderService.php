<?php

namespace App\Services;

use App\Models\Part;
use App\Repositories\Part\PartRepositoryInterface;
use App\Repositories\RepairOrder\RepairOrderRepositoryInterface;
use App\Repositories\RepairOrderPart\RepairOrderPartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RepairOrderService{
    protected $repairOrderRepo;
    protected $repairOrderPartRepo;
    protected $partRepo;

    public function __construct(
        RepairOrderRepositoryInterface $repairOrderRepo,
        RepairOrderPartRepositoryInterface $repairOrderPartRepo,
        PartRepositoryInterface $partRepo
    ){
        $this->repairOrderRepo = $repairOrderRepo;
        $this->repairOrderPartRepo = $repairOrderPartRepo;
        $this->partRepo = $partRepo;
    }

    public function createRepairOrder($data){
        return DB::transaction(function() use ($data){
            $partIds = $data['parts'];
            $quantities = $data['quantities'];
            $partsCost = $this->calPartsCost($partIds,$quantities); 
            $laborCost = 20000;
            $totalCost = $partsCost + $laborCost;

            $repairOrder = $this->repairOrderRepo->findByAppointmentId($data['appointmentId']);

            if ($repairOrder) {
                $repairOrder->update([
                    'parts_cost' => DB::raw("parts_cost + $partsCost"),
                    'total_cost' => DB::raw("total_cost + $partsCost"),
                ]);
            } else{
                $repairOrderData = [
                    'appointment_id' => $data['appointmentId'],
                    'description' => $data['description'],
                    'diagnosis' => $data['diagnosis'],
                    'work_performed' => $data['workPerformed'],
                    'technician_notes' => $data['technicianNotes'],
                    'labor_cost' => $laborCost,
                    'parts_cost' => $partsCost,
                    'total_cost' => $totalCost
                ];

                $repairOrder = $this->repairOrderRepo->create($repairOrderData);
                if (!$repairOrder) {
                    throw new \Exception('Tạo đơn sửa chữa không thành công');
                }

            }
                       
            $this->saveRepairOrderPart(
                $repairOrder->id,
                $repairOrder->technician_notes,
                $partIds,
                $quantities
            );

            $this->partRepo->updatePartsStock($partIds,$quantities);

            return $repairOrder;
        });       
    }

    public function getRepairOrderLookup($data){
        return $this->repairOrderRepo->getRepairOrderLookup($data);
    }

    private function calPartsCost(array $partIds, array $quantities){
        $result = 0;

        $parts = Part::whereIn('id',$partIds)->get()->keyBy('id');

        foreach($partIds as $index => $partId){
            $quantity = $quantities[$index];
            if (isset($parts[$partId])) {
                $unitPrice = $parts[$partId]->unit_price;
                $result += $quantity * $unitPrice;  
            }
        }
        return $result;
    }

    private function saveRepairOrderPart($repairOrderId,$techNotes,array $partIds, array $quantities){
        foreach ($partIds as $index => $partId) {
            $quantity = $quantities[$index];

            $repairOrderPartData = [
                'repair_order_id' => $repairOrderId,
                'part_id' => $partId,
                'quantity' => $quantity,
                'notes' => $techNotes
            ];

            $this->repairOrderPartRepo->create($repairOrderPartData);          
        }
    }
}