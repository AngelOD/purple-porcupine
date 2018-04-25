<?php

use App\Room;
use App\SensorCluster;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    const CHARACTERS = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
    private $usedRoomIDs = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rooms = [
            [
                'id' => $this->getRandomRoomID(),
                'name' => '0.2.12',
                'altName' => '',
                'scs' => [
                    '00000012',
                ],
            ],
            [
                'id' => $this->getRandomRoomID(),
                'name' => '0.2.90',
                'altName' => '',
                'scs' => [
                    '000000CD',
                ],
            ],
            [
                'id' => $this->getRandomRoomID(),
                'name' => '0.1.95',
                'altName' => 'Auditorium',
                'scs' => [
                    '00000069',
                    '000000D0',
                    '000000F0',
                ],
            ],
            [
                'id' => $this->getRandomRoomID(),
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

    protected function getRandomRoomID()
    {
        $id = '';

        while (empty($id) || in_array($id, $this->usedRoomIDs)) {
            $id = '';

            for ($i = 0; $i < 4; $i++) {
                $id .= substr(self::CHARACTERS, rand(0, strlen(self::CHARACTERS)), 1);
            }
        }

        $this->usedRoomIDs[] = $id;
        return $id;
    }
}
