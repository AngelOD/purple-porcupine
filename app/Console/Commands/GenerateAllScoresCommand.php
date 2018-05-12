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
    protected $description = 'Resets all scores and recalculates all of them.';

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
        if (!$this->confirm('WARNING! This will remove and recalculate all scores. Are you sure?')) {
            $this->info('Cancelling execution.');
            return 0;
        }

        $this->info('Removing all scores from DB.');
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
                $this->info('No sensor data found.');
                continue;
            }

            $now = Carbon::now('Europe/Copenhagen');
            $skipped = 0;
            $scoreLists = [];

            foreach ($sensorData as $entry) {
                $curDate = $entry['timestamp']->copy();
                $hour = $curDate->hour;
                $index = sprintf('%s-%d', $curDate->toDateString(), $hour + 1);

                if (!$curDate->isWeekday() || $hour < 8 || $hour >= 16) {
                    $skipped++;
                    continue;
                }
                if ($curDate->isToday() && $hour === $now->hour) { continue; }

                if (!array_key_exists($index, $scoreLists)) {
                    $scoreLists[$index] = [
                        'date' => $curDate->copy()->setTimezone('Europe/Copenhagen')->setTime($hour + 1, 0, 0),
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

            $this->info(
                sprintf(
                    'Found %d sets of data. Skipped %d entries. Storing scores in DB...',
                    count($scoreLists),
                    $skipped
                )
            );

            $pgBar = $this->output->createProgressBar(count($scoreLists));

            foreach ($scoreLists as $scoreEntry) {
                $entryCount = $scoreEntry['count'];
                $entryEndTime = $scoreEntry['date'];

                $score = Score::make();
                $score->total_score = $scoreEntry['total'] / $entryCount;
                $score->iaq_score = $scoreEntry['iaq'] / $entryCount;
                $score->visual_score = $scoreEntry['visual'] / $entryCount;
                $score->sound_score = $scoreEntry['sound'] / $entryCount;
                $score->temp_hum_score = $scoreEntry['temp_hum'] / $entryCount;
                $score->end_time = TimeHelper::carbonToNanoTime($entryEndTime);
                $score->interval = $intervalSecs;

                $room->scores()->save($score);

                $pgBar->advance();
            }

            $pgBar->finish();
            $this->info('');
            $this->info('');
        }
    }
}
