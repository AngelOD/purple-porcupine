<?php
namespace SW802F18\Helpers;

use DB;
use InfluxDB;
use App\Room;
use App\SensorCluster;
use Carbon\Carbon;

class ExamHelper
{
    private const ROOM_NAME = 'ExamTestRoom';
    private const CLUSTER_MAC = 'FFFFFFFF';

    /**
     *
     */
    public static function getRoom()
    {
        $room = Room::where('name', '=', self::ROOM_NAME)->with(['sensorClusters'])->first();

        if (!empty($room)) { return $room; }

        $room = Room::make();
        $room->internal_id = RoomHelper::getRandomRoomID();
        $room->name = self::ROOM_NAME;
        $room->alt_name = '';
        $room->save();

        $sc = SensorCluster::make();
        $sc->room()->associate($room);
        $sc->node_mac_address = self::CLUSTER_MAC;
        $sc->save();

        $room = Room::where('name', '=', self::ROOM_NAME)->with(['sensorClusters'])->first();

        return $room;
    }

    /**
     *
     */
    public static function setupGoodRoom($co2Status, $vocStatus, $isFuture = false, $minutes = 60)
    {
        $co2Status = min(2, max(0, $co2Status));
        $vocStatus = min(2, max(0, $vocStatus));

        $co2s = [500, 1300, 2500];
        $vocs = [10, 100, 200];

        return self::setupRoom([
            'temperature' => 2100,
            'humidity' => 37000,
            'co2' => $co2s[$co2Status],
            'tvoc' => $vocs[$vocStatus],
            'light' => 900,
            'uv' => 1,
            'sound_pressure' => 40,
        ], $isFuture, $minutes);
    }

    /**
     *
     */
    public static function setupBadRoom($temperatureStatus, $humidityStatus, $co2Status, $vocStatus, $isFuture = false, $minutes = 60)
    {
        $co2Status = min(2, max(0, $co2Status));
        $humidityStatus = min(2, max(0, $humidityStatus));
        $temperatureStatus = min(2, max(0, $temperatureStatus));
        $vocStatus = min(2, max(0, $vocStatus));

        $co2s = [500, 1300, 2500];
        $humids = [30, 37, 55];
        $temps = [19, 21, 24];
        $vocs = [10, 100, 200];

        return self::setupRoom([
            'temperature' => $temps[$temperatureStatus] * 100,
            'humidity' => $humids[$humidityStatus] * 1000,
            'co2' => $co2s[$co2Status],
            'tvoc' => $vocs[$vocStatus],
            'light' => 900,
            'uv' => 1,
            'sound_pressure' => 40,
        ], $isFuture, $minutes);
    }

    /**
     *
     */
    public static function setupHorribleRoom($temperatureStatus, $humidityStatus, $co2Status, $vocStatus, $isFuture = false, $minutes = 60)
    {
        $co2Status = min(2, max(0, $co2Status));
        $humidityStatus = min(2, max(0, $humidityStatus));
        $temperatureStatus = min(2, max(0, $temperatureStatus));
        $vocStatus = min(2, max(0, $vocStatus));

        $co2s = [500, 1300, 2500];
        $humids = [22, 37, 65];
        $temps = [16, 21, 27];
        $vocs = [10, 100, 200];

        return self::setupRoom([
            'temperature' => $temps[$temperatureStatus] * 100,
            'humidity' => $humids[$humidityStatus] * 1000,
            'co2' => $co2s[$co2status],
            'tvoc' => $vocs[$vocStatus],
            'light' => 900,
            'uv' => 1,
            'sound_pressure' => 40,
        ], $isFuture, $minutes);
    }

    /**
     *
     */
    public static function makeRoomGood($co2Status, $vocStatus, $minutes = 60)
    {
        return self::setupGoodRoom($co2Status, $vocStatus, true, $minutes);
    }

    /**
     *
     */
    public static function makeRoomBad($temperatureStatus, $humidityStatus, $co2Status, $vocStatus, $minutes = 60)
    {
        return self::setupBadRoom($temperatureStatus, $humidityStatus, $co2Status, $vocStatus, true, $minutes);
    }

    /**
     *
     */
    public static function makeRoomHorrible($temperatureStatus, $humidityStatus, $co2Status, $vocStatus, $minutes = 60)
    {
        return self::setupHorribleRoom($temperatureStatus, $humidityStatus, $co2Status, $vocStatus, true, $minutes);
    }

    /**
     *
     */
    protected static function setupRoom($data, $isFuture = false, $minutes = 60)
    {
        $room = ($isFuture ? self::getRoom() : self::clearRoomSensorData());
        $now = Carbon::now();
        $maxCount = $minutes;
        $origData = $data;
        $dbData = self::getLatestSensorData();
        $loopCount = 1;

        if ($isFuture) {
            $data = self::calculateNewData($dbData, $origData, $loopCount);
        }

        if (!$isFuture) { $now->addMinute(); }

        $points = [];
        for ($i = 0; $i < $maxCount; $i++) {
            if ($isFuture) {
                $now->addMinute();
            } else {
                $now->subMinute();
            }

            $points[] =
                new InfluxDB\Point(
                    'radio_datas',
                    null,
                    [
                        'node_mac_address' => self::CLUSTER_MAC,
                        'sequence_number' => count($points) + 1,
                    ],
                    [
                        'radio_bus_id'      => 1,
                        'channel'           => 2,
                        'packet_type'       => 2,
                        'timestamp_tz'      => $now->toIso8601String(),
                        'v_bat'             => 350,
                        'vcc'               => 350,
                        'temperature'       => $data['temperature'],
                        'humidity'          => $data['humidity'],
                        'pressure'          => 101250,
                        'co2'               => $data['co2'],
                        'tvoc'              => $data['tvoc'],
                        'light'             => $data['light'],
                        'uv'                => $data['uv'],
                        'sound_pressure'    => $data['sound_pressure'],
                        'port_input'        => 1,
                        'mag'               => '0 0 0',
                        'acc'               => '0 0 0',
                        'gyro'              => '0 0 0',
                    ],
                    $now->timestamp
                );

            if ($isFuture && $i > 0 && $i < 20 && $i % 5 === 0) {
                $loopCount++;
                $data = self::calculateNewData($dbData, $origData, $loopCount);
            }
        }

        $res = InfluxDB::writePoints($points, InfluxDB\Database::PRECISION_SECONDS);

        return $room;
    }

    /**
     *
     */
    protected static function clearRoomSensorData()
    {
        $room = self::getRoom();
        $sc = $room->sensorClusters[0];

        $q = 'DELETE FROM "radio_datas" WHERE "node_mac_address" = \'' . $sc->node_mac_address . '\'';
        InfluxDB::query($q);

        return $room;
    }

    /**
     *
     */
    protected static function getLatestSensorData()
    {
        $room = self::getRoom();
        $sc = $room->sensorClusters[0];

        $q = 'SELECT * FROM "radio_datas" '
            .'WHERE "node_mac_address" = \'' . $sc->node_mac_address . '\' '
            .'ORDER BY time DESC '
            .'LIMIT 1';
        $resultSet = InfluxDB::query($q);
        $data = $resultSet->getPoints();

        if (!empty($data)) {
            return $data[0];
        }

        return [];
    }

    /**
     *
     */
    protected static function calculateNewData($from, $to, $index, $count = 4)
    {
        $newData = [];

        foreach ($from as $key => $value) {
            if (!array_key_exists($key, $to)) { continue; }

            if ($index < $count) {
                $newData[$key] = (int)($value + floor((($to[$key] - $value) / $count) * $index));
            } else {
                $newData[$key] = $to[$key];
            }
        }

        return $newData;
    }
}