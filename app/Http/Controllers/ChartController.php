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

        $lastDay = Lava::DataTable();

        $lastDay->addDateTimeColumn('DateTime')
                ->addNumberColumn('CO2')
                ->addNumberColumn('Humidity')
                ->addNumberColumn('Temperature')
                ->addNumberColumn('VOC');

        foreach ($sensorData as $entry) {
            $lastDay->addRow([
                $entry['timestamp']->toDateTimeString(),
                $entry['co2'],
                $entry['humidity'],
                $entry['temperature'],
                $entry['voc'],
            ]);
        }

        Lava::LineChart('LastDay', $lastDay, [
            'title' => 'Last day for ' . $room->name . (!empty($room->alt_name) ? ' (' . $room->alt_name . ')' : '')
        ]);

        return view('charts.test');
    }
}
