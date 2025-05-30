<?php

namespace App\Charts;

use App\Models\Service;
use ConsoleTVs\Charts\Classes\Highcharts\Chart;

class ServiceUsageChart extends Chart
{  
    public function __construct()
    {
        parent::__construct();

        $serviceUsage = Service::where('is_delete', 0)
            ->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->get();
        
        $serviceNames = [];
        $usageCounts = [];

        foreach ($serviceUsage as $service) {
            $serviceNames[] = $service->service_name;
            $usageCounts[] = $service->appointments_count;
        }

        $this->title('Thống kê Số lần Dịch vụ được Sử dụng');
        $this->labels($serviceNames);
        $this->dataset('Số lần sử dụng', 'bar', $usageCounts);
        
        
        $this->options([
            'plotOptions' => [
                'column' => [
                    'colorByPoint' => true,
                    'dataLabels' => [
                        'enabled' => true,
                        'format' => '{y}'
                    ]
                ]
            ],
            'colors' => [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF',
                '#4BC0C0', '#FF6384'
            ],
            'xAxis' => [
                'title' => [
                    'text' => 'Dịch vụ'
                ]
            ],
            'yAxis' => [
                'title' => [
                    'text' => 'Số lần sử dụng'
                ],
                'min' => 0
            ],
            'tooltip' => [
                'pointFormat' => '<b>{point.y}</b> lần sử dụng'
            ]
        ]);
        
    }
}
