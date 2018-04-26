<?php

use App\Room;
use App\SensorCluster;
use Illuminate\Database\Seeder;
use SW802F18\Helpers\RoomHelper;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rooms = [
            [
                'id' => RoomHelper::getRandomRoomID(),
                'name' => '0.2.12',
                'altName' => '',
                'scs' => [
                    '00000012',
                ],
            ],
            [
                'id' => RoomHelper::getRandomRoomID(),
                'name' => '0.2.90',
                'altName' => '',
                'scs' => [
                    '000000CD',
                ],
            ],
            [
                'id' => RoomHelper::getRandomRoomID(),
                'name' => '0.1.95',
                'altName' => 'Auditorium',
                'scs' => [
                    '00000069',
                    '000000D0',
                    '000000F0',
                ],
            ],
            [
                'id' => RoomHelper::getRandomRoomID(),
                'name' => '2.2.56',
                'altName' => '',
                'scs' => [
                    '000000F2',
                ],
            ],
        ];

        if (DB::table('rooms')->count() > 0) {
            $this->command->info('Data exists. Skipping seeder.');
            return;
        }

        foreach ($rooms as $room) {
            $newRoom = Room::make();
            $newRoom->internal_id = $room['id'];
            $newRoom->name = $room['name'];
            $newRoom->alt_name = $room['altName'];

            $newRoom->save();

            foreach ($room['scs'] as $sc) {
                $newCluster = SensorCluster::make();
                $newCluster->room()->associate($newRoom);
                $newCluster->node_mac_address = $sc;

                $newCluster->save();
            }

            $newRoom->save();
        }
    }
}
