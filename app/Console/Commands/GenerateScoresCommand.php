<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Contracts\Scoring as ScoringContract;
use SW802F18\Helpers\RoomHelper;
use SW802F18\Helpers\TimeHelper;
use App\Room;
use App\Score;

class GenerateScoresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates latest set of scores for all rooms.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $interval       = ['minutes' => 5];
        $scd            = app()->make(SensorClusterContract::class);
        $rooms          = Room::with('sensorClusters')->get();
        $startTime      = Carbon::now()->startOfDay();
        $endTime        = Carbon::now();
        $scoring        = app()->make(ScoringContract::class);
        $intervalSecs   = TimeHelper::intervalToSeconds(['minutes' => 5]);

        $this->info('Generating scores for...');

        foreach ($rooms as $room) {
            $sensorData = $scd->getFullDataset(
                $room->sensorClusters->map(function ($e) { return $e->node_mac_address; })->toArray(),
                $startTime,
                $endTime,
                $interval
            );

            if (count($sensorData) === 0) {
                continue;
            }

            $scores = [
                'count' => 0,
                'total' => 0,
                'iaq' => 0,
                'visual' => 0,
                'sound' => 0,
                'temp_hum' => 0
            ];

            foreach ($sensorData as $entry) {
                $scoring->updateAllClassifications(
                    $entry['uv'],
                    $entry['light'],
                    $entry['voc'],
                    $entry['temperature'],
                    $entry['co2'],
                    $entry['noise'],
                    $entry['humidity'],
                    $endTime
                );

                $scores['count']++;
                $scores['total'] += $scoring->totalScore(RoomHelper::CALCULATIONS_PER_DAY);
                $scores['iaq'] += $scoring->iaqScore();
                $scores['visual'] += $scoring->visualScore();
                $scores['sound'] += $scoring->soundScore();
                $scores['temp_hum'] += $scoring->tempHumScore();
            }

            $entryCount = $scores['count'];
            $score = Score::make();
            $score->total_score = $scores['total'] / $entryCount;
            $score->iaq_score = $scores['iaq'] / $entryCount;
            $score->visual_score = $scores['visual'] / $entryCount;
            $score->sound_score = $scores['sound'] / $entryCount;
            $score->temp_hum_score = $scores['temp_hum'] / $entryCount;
            $score->end_time = TimeHelper::carbonToNanoTime($endTime->copy()->setTime($endTime->hour, 0, 0));
            $score->interval = $intervalSecs;

            $room->scores()->save($score);

            $this->info(sprintf('%s: %f', $room->internal_id, $score->total_score));
        }
    }
}
