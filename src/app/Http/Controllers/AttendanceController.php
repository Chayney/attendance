<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Time;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function workin(Request $request)
    {
        $user = Auth::user();
        // ログインユーザの最新情報を取得
        $oldTime = Time::where('user_id', $user->id)->latest()->first();

        // 登録初回ユーザ限定処理
        if (empty($oldTime)){
            $time = Time::create([
                'user_id' => $user->id,
                'start_work' => Carbon::now() //現在時刻
            ]);
            return redirect('/')->with('message', '出勤打刻しました');
        }

        
        // 退勤前に出勤を再度打刻できない
        if ($oldTime) {
            $oldTimeStartWork = new Carbon($oldTime->start_work); //出勤打刻を押した時間
            $oldDay = $oldTimeStartWork->startOfDay(); //出勤打刻を00:00:00で代入する   
            $today = Carbon::today(); //当日00:00:00
            if (($oldDay == $today) && (empty($oldTime->end_work))) { // 当日の出勤が打刻されているかつ退勤が打刻されていない場合
                return redirect()->back()->with('message', 'すでに出勤が打刻されています');
            }
        }
        

        // 退勤後に出勤を再度打刻できない
        if ($oldTime) {
            $oldTimeEndWork = new Carbon($oldTime->end_work); // 退勤打刻を押した時間
            $oldDay = $oldTimeEndWork->startOfDay(); //退勤打刻を00:00:00で代入する
            if (($oldDay == $today) && (!empty($oldTime->end_work))) {
                return redirect()->back()->with('message', '本日の勤務は終了しました');
            }
        }

 

        $time = Time::create([
            'user_id' => $user->id,
            'start_work' => Carbon::now() //現在時刻
        ]);

        return redirect('/')->with('message', '出勤打刻しました');
       
    }

    public function workout(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();

        // 退勤後に退勤を再度打刻できない
        $oldTimeEndWork = new Carbon($time->end_work); // 退勤打刻を押した時間
        $oldDay = $oldTimeEndWork->startOfDay(); //退勤打刻を00:00:00で代入する

        $today = Carbon::today(); //当日00:00:00

        if (($oldDay == $today) && (!empty($time->end_work))) {
            return redirect()->back()->with('message', 'すでに退勤が打刻されています');
        }

        // 出勤時刻、休憩開始時刻、休憩終了時刻を出す
        $now = new Carbon();
        $startWork = new Carbon($time->start_work);
        $breakIn = new Carbon($time->break_in);
        $breakOut = new Carbon($time->break_out);

        // 稼働時間(秒数)
        $stayTime = $startWork->diffInSeconds($now);
        $breakTime = $breakIn->diffInSeconds($breakOut); //休憩時間
        $workingSecond = $stayTime - $breakTime;

        $time->update([
            'end_work' => Carbon::now(),
            'worktime' => $workingSecond,
            'breaktime' => $breakTime
        ]);

        // dd($time);

        return redirect('/')->with('message', '退勤打刻しました');
  
    }

    public function breakin(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();
        
        $time->update([
            'break_in' => Carbon::now()
        ]);

        return redirect('/')->with('message', '休憩開始しました');
    }

    public function breakout(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();
        
        $time->update([
            'break_out' => Carbon::now(),
        ]);

        return redirect('/')->with('message', '休憩終了しました');
    }

    public function attend()
    {
        $attends = Time::with('time')->get();
        $users = User::all();
        return view('attendance', compact('attends', 'users'));
    }

}
