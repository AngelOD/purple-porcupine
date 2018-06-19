<?php

namespace App\Http\Controllers;

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
            's' => $request->input('s', 0),
            'h' => $request->input('h', 1),
            't' => $request->input('t', 1),
            'ts' => $request->input('ts', -1),
            'th' => $request->input('th', 1),
            'tt' => $request->input('tt', 1),
        ];

        switch ($data['s']) {
            case 0:
                ExamHelper::setupGoodRoom();
                break;

            case 1:
                ExamHelper::setupBadRoom($data['t'], $data['h']);
                break;

            case 2:
                ExamHelper::setupHorribleRoom($data['t'], $data['h']);
                break;
        }

        if ($data['ts'] >= 0) {
            switch ($data['ts']) {
                case 0:
                    ExamHelper::makeRoomGood();
                    break;

                case 1:
                    ExamHelper::makeRoomBad($data['tt'], $data['th']);
                    break;

                case 2:
                    ExamHelper::makeRoomHorrible($data['tt'], $data['th']);
                    break;
            }
        }

        return redirect('/exam')->with('status', 'Method called.');
    }
}
