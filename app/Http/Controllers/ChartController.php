<?php

namespace App\Http\Controllers;

use Lava;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Room;
use SW802F18\Contracts\SensorCluster;

class ChartController extends Controller
{
    public function test($roomID)
    {
        $room = Room::where('internal_id', '=', $roomID)->first();
        if (empty($room)) { return response('Error!', 400); }

        $scs = $room->sensorClusters;
        if (empty($scs)) { return response('Error!', 400); }

        $scd = app()->makeWith(SensorCluster::class, ['skipInit' => true]);

        $endTime = Carbon::now();
        $startTime = $endTime->copy()->subDay();
        $sensorData = $scd->getFullDataset(
            $scs->map(function ($elem) { return $elem->node_mac_address; }),
            $startTime,
            $endTime,
            ['minutes' => 10]
        );

        $lastDayCO2 = Lava::DataTable();
        $lastDayCO2->addDateTimeColumn('DateTime')
                ->addNumberColumn('CO2');

        $lastDayRest = Lava::DataTable();
        $lastDayRest->addDateTimeColumn('DateTime')
                ->addNumberColumn('Humidity')
                ->addNumberColumn('Temperature')
                ->addNumberColumn('VOC');

        foreach ($sensorData as $entry) {
            $lastDayCO2->addRow([
                $entry['timestamp']->toDateTimeString(),
                $entry['co2'],
            ]);

            $lastDayRest->addRow([
                $entry['timestamp']->toDateTimeString(),
                $entry['humidity'],
                $entry['temperature'],
                $entry['voc'],
            ]);
        }

        Lava::LineChart('LastDayCO2', $lastDayCO2, [
            'title' => 'Last day for ' . $room->name . (!empty($room->alt_name) ? ' (' . $room->alt_name . ')' : ''),
            'legend' => [
                'position' => 'bottom'
            ],
            'trendlines' => [
                [
                    'type' => 'linear',
                    'visibleInLegend' => true,
                ]
            ],
            'hAxis' => [
                'viewWindow' => [
                    'min' => sprintf(
                        "Date(%d, %d, %d, %d, %d, %d)",
                        $startTime->year, $startTime->month - 1, $startTime->day,
                        $startTime->hour, $startTime->minute, $startTime->second
                    ),
                    'max' => sprintf(
                        "Date(%d, %d, %d, %d, %d, %d)",
                        $endTime->year, $endTime->month - 1, $endTime->day,
                        $endTime->hour, $endTime->minute, $endTime->second
                    ),
                ],
            ],
        ]);

        Lava::LineChart('LastDayRest', $lastDayRest, [
            'title' => 'Last day for ' . $room->name . (!empty($room->alt_name) ? ' (' . $room->alt_name . ')' : ''),
            'legend' => [
                'position' => 'bottom'
            ],
            'hAxis' => [
                'viewWindow' => [
                    'min' => sprintf(
                        "Date(%d, %d, %d, %d, %d, %d)",
                        $startTime->year, $startTime->month - 1, $startTime->day,
                        $startTime->hour, $startTime->minute, $startTime->second
                    ),
                    'max' => sprintf(
                        "Date(%d, %d, %d, %d, %d, %d)",
                        $endTime->year, $endTime->month - 1, $endTime->day,
                        $endTime->hour, $endTime->minute, $endTime->second
                    ),
                ],
            ],
        ]);

        return view('charts.test');
    }
}
