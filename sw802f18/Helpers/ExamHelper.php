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
    public static function setupGoodRoom($isFuture = false)
    {
        return self::setupRoom([
            'temperature' => 2100,
            'humidity' => 37000,
            'co2' => 500,
            'tvoc' => 10,
            'light' => 900,
            'uv' => 1,
            'sound_pressure' => 40,
        ], $isFuture);
    }

    /**
     *
     */
    public static function setupBadRoom($temperatureStatus, $humidityStatus, $vocStatus, $isFuture = false)
    {
        $temperatureStatus = min(2, max(0, $temperatureStatus));
        $humidityStatus = min(2, max(0, $humidityStatus));
        $vocStatus = min(2, max(0, $vocStatus));

        $temps = [19, 21, 24];
        $humids = [30, 37, 55];
        $vocs = [10, 100, 200];

        return self::setupRoom([
            'temperature' => $temps[$temperatureStatus] * 100,
            'humidity' => $humids[$humidityStatus] * 1000,
            'co2' => 1300,
            'tvoc' => $vocs[$vocStatus],
            'light' => 900,
            'uv' => 1,
            'sound_pressure' => 40,
        ], $isFuture);
    }

    /**
     *
     */
    public static function setupHorribleRoom($temperatureStatus, $humidityStatus, $vocStatus, $isFuture = false)
    {
        $temperatureStatus = min(2, max(0, $temperatureStatus));
        $humidityStatus = min(2, max(0, $humidityStatus));
        $vocStatus = min(2, max(0, $vocStatus));

        $temps = [16, 21, 27];
        $humids = [22, 37, 65];
        $vocs = [10, 100, 200];

        return self::setupRoom([
            'temperature' => $temps[$temperatureStatus] * 100,
            'humidity' => $humids[$humidityStatus] * 1000,
            'co2' => 2500,
            'tvoc' => $vocs[$vocStatus],
            'light' => 900,
            'uv' => 1,
            'sound_pressure' => 40,
        ], $isFuture);
    }

    /**
     *
     */
    public static function makeRoomGood()
    {
        return self::setupGoodRoom(true);
    }

    /**
     *
     */
    public static function makeRoomBad($temperatureStatus, $humidityStatus, $vocStatus)
    {
        return self::setupBadRoom($temperatureStatus, $humidityStatus, $vocStatus, true);
    }

    /**
     *
     */
    public static function makeRoomHorrible($temperatureStatus, $humidityStatus, $vocStatus)
    {
        return self::setupHorribleRoom($temperatureStatus, $humidityStatus, $vocStatus, true);
    }

    /**
     *
     */
    protected static function setupRoom($data, $isFuture = false)
    {
        $room = ($isFuture ? self::getRoom() : self::clearRoomSensorData());
        $now = Carbon::now();
        $maxCount = ($isFuture ? 30 : 60);
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

            if ($to[$key] !== $value) {
                $newData[$key] = (int)($value + floor((($to[$key] - $value) / $count) * $index));
            } else {
                $newData[$key] = $value;
            }
        }

        return $newData;
    }
}