<?php

namespace App\Notifications;

use App\Models\MaintenanceSchedule;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $maintenanceSchedule;
    protected $reminderType;
    
    public function __construct(MaintenanceSchedule $maintenanceSchedule, $reminderType = 'upcoming')
    {
        $this->maintenanceSchedule = $maintenanceSchedule;
        $this->reminderType = $reminderType;
    }


    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        // if ($notifiable->phone) {
        //     $channels[] = 'sms';
        // }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $schedule = $this->maintenanceSchedule;
        $vehicle = $schedule->vehicle;
        $service = $schedule->service;

        $subject = $this->getEmailSubject();
        $daysUntil = $schedule->getDaysUntilMaintenance();

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Xin chào {$notifiable->name}!")
            ->line($this->getMainMessage($daysUntil))
            ->line("**Thông tin xe:**")
            ->line("• Biển số: {$vehicle->license_plate}")
            ->line("• Xe: {$vehicle->brand} {$vehicle->model} ({$vehicle->year_manufactory})")
            ->line("**Thông tin bảo trì:**")
            ->line("• Dịch vụ: {$service->service_name}")
            ->line("• Ngày bảo trì: " . Carbon::parse($schedule->next_maintenance_date)->format('d/m/Y'))
            ->line("• Mức độ ưu tiên: " . ucfirst($schedule->priority))
            ->when($schedule->notes, function ($message) use ($schedule) {
                return $message->line("• Ghi chú: {$schedule->notes}");
            })
            ->action('Xem chi tiết', url("/maintenance-schedules/{$schedule->schedule_id}"))
            ->line('Vui lòng sắp xếp lịch bảo trì để đảm bảo xe hoạt động tốt nhất.');
    }

    public function toSms($notifiable)
    {
        $schedule = $this->maintenanceSchedule;
        $vehicle = $schedule->vehicle;
        $daysUntil = $schedule->getDaysUntilMaintenance();
        
        return [
            'to' => $notifiable->phone,
            'message' => $this->getSmsMessage($vehicle, $schedule, $daysUntil)
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    private function getEmailSubject()
    {
        switch ($this->reminderType) {
            case 'overdue':
                return '⚠️ Lịch bảo trì đã quá hạn';
            case 'urgent':
                return '🚨 Nhắc nhở bảo trì khẩn cấp';
            case 'upcoming':
            default:
                return '🔧 Nhắc nhở lịch bảo trì sắp tới';
        }
    }

    private function getMainMessage($daysUntil)
    {
        if ($daysUntil < 0) {
            return "Lịch bảo trì của xe đã quá hạn " . abs($daysUntil) . " ngày.";
        } elseif ($daysUntil == 0) {
            return "Hôm nay là ngày bảo trì xe của bạn.";
        } else {
            return "Xe của bạn cần bảo trì trong {$daysUntil} ngày tới.";
        }
    }

    private function getSmsMessage($vehicle, $schedule, $daysUntil)
    {
        $timeMsg = $daysUntil < 0 ? "quá hạn " . abs($daysUntil) . " ngày" : 
                  ($daysUntil == 0 ? "hôm nay" : "trong {$daysUntil} ngày");
        
        return "Nhắc lịch bảo trì: Xe {$vehicle->license_plate} cần bảo trì {$timeMsg}. " .
               "Dịch vụ: {$schedule->service->service_name}. " .
               "Ngày: {$schedule->next_maintenance_date->format('d/m/Y')}";
    }
}
