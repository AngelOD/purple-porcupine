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

        $sensorData = [];
        $endTime = Carbon::now();
        $startTime = $endTime->copy()->subDay();

        foreach ($scs as $sc) {
            $mac = $sc->node_mac_address;
            $sensorData[$mac] = RoomHelper::getFullDataset($mac, $startTime, $endTime, ['minutes' => 10]);
        }

        $lastDay = Lava::DataTable();

        $lastDay->addDateTimeColumn('DateTime')
                ->addNumberColumn('CO2')
                ->addNumberColumn('Humidity')
                ->addNumberColumn('Temperature')
                ->addNumberColumn('VOC');

        while ($curTime->gte($startTime)) {
            $room->sensorDataEndTime = $curTime;
            $data = $room->averageSensorData;

            if (!empty($data)) {
                $lastDay->addRow([
                    $curTime->toDateTimeString(),
                    $data['co2'],
                    $data['humidity'],
                    $data['temperature'],
                    $data['voc'],
                ]);
            }

            $curTime->subMinutes(10);
        }

        Lava::LineChart('LastDay', $lastDay, [
            'title' => 'Last day for ' . $room->name . (!empty($room->alt_name) ? ' (' . $room->alt_name . ')' : '')
        ]);

        return view('charts.test');
    }
}
