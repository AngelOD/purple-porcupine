<?php

namespace App\Http\Controllers;

use App\Score;
use Illuminate\Http\Request;
use SW802F18\Helpers\ExamHelper;

class ExamController extends Controller
{
    /**
     *
     */
    public function index()
    {
        return view('exam.index');
    }

    /**
     *
     */
    public function update(Request $request)
    {
        $data = [
            'c' => $request->input('c', 0),
            'd' => $request->input('d', 30),
            'h' => $request->input('h', 1),
            's' => $request->input('s', 0),
            't' => $request->input('t', 1),
            'v' => $request->input('v', 0),
            'tc' => $request->input('tc', 0),
            'td' => $request->input('td', 60),
            'th' => $request->input('th', 1),
            'ts' => $request->input('ts', -1),
            'tt' => $request->input('tt', 1),
            'tv' => $request->input('tv', 0),
        ];

        switch ($data['s']) {
            case 0:
                ExamHelper::setupGoodRoom($data['c'], $data['v'], false, $data['d']);
                break;

            case 1:
                ExamHelper::setupBadRoom($data['t'], $data['h'], $data['c'], $data['v'], false, $data['d']);
                break;

            case 2:
                ExamHelper::setupHorribleRoom($data['t'], $data['h'], $data['c'], $data['v'], false, $data['d']);
                break;
        }

        if ($data['ts'] >= 0) {
            switch ($data['ts']) {
                case 0:
                    ExamHelper::makeRoomGood($data['tc'], $data['tv'], $data['td']);
                    break;

                case 1:
                    ExamHelper::makeRoomBad($data['tt'], $data['th'], $data['tc'], $data['tv'], $data['td']);
                    break;

                case 2:
                    ExamHelper::makeRoomHorrible($data['tt'], $data['th'], $data['tc'], $data['tv'], $data['td']);
                    break;
            }
        }

        // Setup scores
        ExamHelper::setupRoomScores();

        return redirect('/exam')->with('status', 'Method called.');
    }
}
