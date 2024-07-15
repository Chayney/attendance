<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Time;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $today = new Carbon('today'); //本日00:00:00を取得する
        $time = Time::where('user_id', $user->id)->latest()->first(); //DBに記録されているログインユーザの最新情報を取得

        // 一番最初はtimesテーブルに情報が1つもない
        if (empty($time)) {
            $time = Time::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'start_work' => Carbon::now() //現在時刻
            ]);

            return redirect('/')->with('message', '初めての出勤頑張りましょう');
        }

        $todayStartWork = new Carbon($time->start_work); //ログインユーザの出勤時刻を取得
      
        $startWork = $todayStartWork->startOfDay(); //取得した出勤時刻をcarbonで生成した日時と一致させるため
        
        // 1日1回の出勤打刻とする
        // 本日の出勤打刻が無い場合はログインユーザのidと出勤打刻時間を新しくDBに登録する
        if ($today == $startWork) {
            return redirect()->back()->with('message', '出勤打刻済みです');
        } else {
            $time = Time::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'start_work' => Carbon::now()
            ]);
    
            return redirect('/')->with('message', '出勤を打刻しました');
        }

    }

    public function breakin(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();

        // 本日の出勤開始打刻を押さないと休憩開始出来ない
        if ($time) {
            $yesterday = new Carbon('yesterday'); //昨日00:00:00を取得する
            $oldbreakIn = new Carbon($time->break_in);
            $oldday = $oldbreakIn->startOfDay(); //carbonで生成した日時に合わせる
            if ($oldday <= $yesterday) {
                return redirect('/')->with('message', '本日はまだ出勤打刻されていません');
            }
        }

        // DBにまだ登録されていないかまたは出勤打刻がされていなければ休憩開始打刻できない
        if (empty($time) || (empty($time->start_work))) {
            return redirect('/')->with('message', '出勤が打刻されていません');
        }

        // 勤務終了後は休憩開始の打刻ができない
        $today = new Carbon('today');
        $endWork = new Carbon($time->end_work);
        $endWorkToday = $endWork->startOfDay();
        if (($today == $endWorkToday) && (!empty($time->end_work))){
            return redirect('/')->with('message', '本日の勤務はもう終了しました');
        }

        $time->update([
            'break_in' => Carbon::now(),
        ]);

        return redirect('/')->with('message', '休憩開始しました');
    }

    public function breakout(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();

        // 本日の出勤開始打刻を押さないと休憩終了出来ない
        if ($time) {
            $yesterday = new Carbon('yesterday');
            $oldbreakOut = new Carbon($time->break_out); 
            $oldday = $oldbreakOut->startOfDay();
            if ($oldday <= $yesterday) {
                return redirect('/')->with('message', '本日の出勤はまだ打刻されていません');
            }
        }

        // DBにまだ登録されていないかまたは出勤打刻がされていない、または休憩開始打刻がされていなければ休憩終了打刻できない
        if (empty($time) || (empty($time->start_work)) || (empty($time->break_in))) {
            return redirect('/')->with('message', '休憩開始が打刻されていません');
        }

        // 勤務終了後は休憩終了の打刻ができない
        $today = new Carbon('today');
        $endWork = new Carbon($time->end_work);
        $endWorkToday = $endWork->startOfDay();
        if (($today == $endWorkToday) && (!empty($time->end_work))){
            return redirect('/')->with('message', '本日の勤務はもう終了しましたので打刻できません');
        }
        
        $time->update([
            'break_out' => Carbon::now(),
        ]);

        return redirect('/')->with('message', '休憩終了しました');
    }

    public function workout(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();

        // DBにまだ登録されていないかまたは出勤が打刻されていなければ退勤打刻が出来ない
        if (empty($time) || (empty($time->start_work))) {
            return redirect('/')->with('message', '出勤が打刻されていません');
        } else if ((!empty($time->break_in)) && (empty($time->break_out))) {    //休憩開始が打刻されているかつ休憩終了が打刻されていなければ退勤打刻できない
            return redirect('/')->with('message', '休憩終了が打刻されていません');
        }

        // 本日の出勤開始打刻を押さないと休憩終了出来ない
        if ($time) {
            $yesterday = new Carbon('yesterday');
            $oldendWork = new Carbon($time->end_work);
            $oldday = $oldendWork->startOfDay();
            if ($oldday <= $yesterday) {
                return redirect('/')->with('message', '本日はまだ出勤の打刻がされていません');
            }
        }

        // 1日に1回の退勤打刻制限をする
        $today = new Carbon('today'); //本日00:00:00を取得する
        $todayEndWork = new Carbon($time->end_work); //ログインユーザの退勤時刻を取得
        $endWork = $todayEndWork->startOfDay(); //退勤時刻をcarbonで生成した日時と一致させるため

        if (($today == $endWork) && (!empty($time->end_work))) {
            return redirect()->back()->with('message', '退勤打刻済みです');
        } 

        // 現在時刻、出勤時刻、休憩開始時刻、休憩終了時刻を出力する
        $now = new Carbon();
        $startWork = new Carbon($time->start_work);
        $breakIn = new Carbon($time->break_in);
        $breakOut = new Carbon($time->break_out);
        

        // 合算した休憩時間を整形する
        $breakTime = $breakIn->diffInSeconds($breakOut);
        $breakTimeSeconds = floor($breakTime % 60);
        $breakTimeMinutes = floor($breakTime / 60);
        $breakTimeHours = floor($breakTimeMinutes / 60);
        $restTime = $breakTimeHours . ':' . $breakTimeMinutes . ':' . $breakTimeSeconds;
        
        // 合算した勤務時間を整形する(実働時間)
        $stayTime = $startWork->diffInSeconds($now); //休憩時間を含めた1日の勤務時間
        $workingTime = $stayTime - $breakTime; //休憩時間を除いた実働時間
        $workingTimeSeconds = floor($workingTime % 60);
        $workingTimeMinutes = floor($workingTime / 60);
        $workingTimeHours = floor($workingTimeMinutes / 60);
        $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;

        $time->update([
            'end_work' => Carbon::now(),
            'worktime' => $workTime,
            'breaktime' => $restTime
        ]);

        return redirect('/')->with('message', '退勤打刻しました');
  
    }

    public function attend()
    {
        $user = Auth::user();
        $today = new Carbon();
        $times = Time::whereDate('end_work', $today)->paginate(5);
        return view('attendance', compact('times'));
    }

    public function before()
    {
        $user = Auth::user();
        $today = new Carbon();
        $beforeday = $today->subDay();
        $times = Time::whereDate('end_work', $beforeday)->paginate(5);
        return view('attendance', compact('times'));
    }

    public function after()
    {
        $user = Auth::user();
        $beforeday = new Carbon('-1 day');
        $afterday = $beforeday->addDay();
        $times = Time::whereDate('end_work', $afterday)->paginate(5);
        return view('attendance', compact('times'));
    }

}
