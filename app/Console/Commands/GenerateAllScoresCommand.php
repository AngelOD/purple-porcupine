<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Carbon\Carbon;
use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Contracts\Scoring as ScoringContract;
use SW802F18\Helpers\RoomHelper;
use SW802F18\Helpers\TimeHelper;
use App\Room;
use App\Score;

class GenerateAllScoresCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:all-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        DB::table('scores')->delete();
        DB::table('scores')->truncate();

        $interval       = ['minutes' => 5];
        $scd            = app()->makeWith(SensorClusterContract::class, ['skipInit' => true]);
        $rooms          = Room::with(['sensorClusters'])->get();
        $startTime      = Carbon::createFromTimestamp(0);
        $endTime        = Carbon::now();
        $scoring        = app()->make(ScoringContract::class);
        $intervalSecs   = TimeHelper::intervalToSeconds(['minutes' => 5]);

        foreach ($rooms as $room) {
            $this->info(sprintf('Generating all scores for %s...', $room->internal_id));

            $sensorData = $scd->getFullDataset(
                $room->sensorClusters->map(function ($e) { return $e->node_mac_address; })->toArray(),
                $startTime,
                $endTime,
                $interval
            );

            if (count($sensorData) === 0) {
                continue;
            }

            $scoreLists = [];

            foreach ($sensorData as $entry) {
                $curDate = $entry['timestamp']->copy();
                $index = $curDate->toDateString();

                if ($curDate->hour <= 8 || $curDate->hour >= 16) { continue; }

                if (!array_key_exists($index, $scoreLists)) {
                    $scoreLists[$index] = [
                        'count' => 0,
                        'total' => 0,
                        'iaq' => 0,
                        'visual' => 0,
                        'sound' => 0,
                        'temp_hum' => 0
                    ];
                }

                $scoring->updateAllClassifications(
                    $entry['uv'],
                    $entry['light'],
                    $entry['voc'],
                    $entry['temperature'],
                    $entry['co2'],
                    $entry['noise'],
                    $entry['humidity'],
                    $curDate
                );

                $scoreLists[$index]['count']++;
                $scoreLists[$index]['total'] += $scoring->totalScore(RoomHelper::CALCULATIONS_PER_DAY);
                $scoreLists[$index]['iaq'] += $scoring->iaqScore();
                $scoreLists[$index]['visual'] += $scoring->visualScore();
                $scoreLists[$index]['sound'] += $scoring->soundScore();
                $scoreLists[$index]['temp_hum'] += $scoring->tempHumScore();
            }

            // ========================================== //

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
            $score->end_time = TimeHelper::carbonToNanoTime($endTime);
            $score->interval = $intervalSecs;

            $room->scores()->save($score);

            $this->info(sprintf('%s: %f', $room->internal_id, $score->total_score));
        }
    }
}
