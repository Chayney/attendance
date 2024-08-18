<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Time;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // 勤務開始
        $user = Auth::user();

        $today = Carbon::now()->toDateString();

        // ログインユーザの本日の勤怠打刻記録を取得する
        $time = Time::where('user_id', $user->id)->where('date', $today)->first();
       
        // 本日の出勤を確認する
        if (empty($time)) {
            // 出勤していない場合
            $time = true;

        } else {
            // 出勤している場合
            $time = false;
        }
        
        // 勤務終了
        $timeend = Time::where('user_id', $user->id)->where('date', $today)->first();
        
        if (!empty($timeend)) {
            // 休憩終了を打刻していない最新の記録を取得する
            $reststop = Rest::where('time_id', $timeend->id)->where('end_rest', null)->first();
            $todaystart = new Carbon($timeend->start_work);
            $todayend = new Carbon($timeend->end_work);

            if ($todaystart == $todayend && empty($reststop)) {
                $timeend = true;
    
            } else {
                $timeend = false;
            }
        }
           
        // 休憩開始
        $timeworking = Time::where('user_id', $user->id)->where('date', $today)->first();
        
        if (!empty($timeworking)) {
            // 休憩終了を押していない最新の記録を取得する
            $resting = Rest::where('time_id', $timeworking->id)->where('end_rest', null)->first();
        
            $todaystart = new Carbon($timeworking->start_work);
            $todayend = new Carbon($timeworking->end_work);

            // 本日出勤しているユーザが休憩開始を打刻しているか確認をする
            if ($todaystart == $todayend && empty($resting)) {
                // 休憩開始を打刻していない場合もしくは休憩終了を打刻した場合(退勤打刻していない間は再度打刻が可能)
                $timeworking = true;

            } else {
                $timeworking = false;
            }
        }
        
        // 休憩終了
        $stampworking = Time::where('user_id', $user->id)->where('date', $today)->first();
        
        if (!empty($stampworking)) {
            $restend = Rest::where('time_id', $stampworking->id)->where('end_rest', null)->first();
            
            $todaystart = new Carbon($stampworking->start_work);
            $todayend = new Carbon($stampworking->end_work);

            if (!empty($restend) && $todaystart == $todayend) { 
                $restend = true;
    
            } else if (!empty($todaystart)) {
                $restend = false;
            }
            
        } else {
            $restend = Rest::where('time_id', $user->id)->where('end_rest', null)->first();
              
            $restend = false;
    
        }
        
        return view('index', compact('time', 'timeend', 'timeworking', 'restend'));
            
    }

    public function workin(Request $request)
    {
        $user = Auth::user();
        
        $today = Carbon::now()->toDateString();

        $time = Time::where('user_id', $user->id)->where('date', $today)->first();
    
        if (empty($time)) {
            $time = Time::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'start_work' => Carbon::now(),
                'end_work' => Carbon::now()
            ]);

            return redirect('/');

        }
          
    }

    public function breakin(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();
        
        Rest::create([
            'time_id' => $time->id,
            'start_rest' => Carbon::now()
        ]);
    
        return redirect('/');

    }

    public function breakout(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();

        $rest = Rest::where('time_id', $time->id)->latest()->first();
        
        $restIn = new Carbon($rest->start_rest);
        $restOut = new Carbon($rest->end_rest);

        // 合算した休憩時間を整形する
        $breakTime = $restIn->diffInSeconds($restOut);
        $breakTimeSeconds = floor($breakTime % 60);
        $breakTimeMinutes = floor(($breakTime % 3600) / 60);
        $breakTimeHours = floor($breakTime / 3600);
        $restTime = $breakTimeHours . ':' . $breakTimeMinutes . ':' . $breakTimeSeconds;
        
        $rest->update([
            'end_rest' => Carbon::now(),
            'resttime' => $restTime
        ]);

        // 1回1回の休憩時間の合計を算出する
        $resttime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(resttime))) as totalresttime')
            ->where('time_id', $time->id)->first();

        // 合計値が加算されるたびに休憩時間を更新する
        $time->update([
            'breaktime' => $resttime->totalresttime
        ]);

        return redirect('/');

    }

    public function workout(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();
        
        $rest = Rest::where('time_id', $time->id)->latest()->first();

        $now = new Carbon();
        $startWork = new Carbon($time->start_work);

        // 出勤中に日を跨いだ時の処理
        if (!($startWork == $time->end_work && $startWork->isSameDay($now))) {
            $timework = Time::where('user_id', $user->id)->latest()->first();

            $rest = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(resttime))) as totalresttime')
            ->where('time_id', $timework->id)->first();
            $resttime = $rest->totalresttime;

            $endWork = Carbon::parse($timework->end_work);
            $endwork = $endWork->endOfDay();

            if (empty($resttime)) {
                $stayTime = $startWork->diffInSeconds($endwork);
                $workingTimeSeconds = floor($stayTime % 60);
                $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                $workingTimeHours = floor($stayTime / 3600);
                $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;

                $timework->update([
                    'end_work' => $endwork,
                    'worktime' => $workTime,
                    'breaktime' => '00:00:00'
                ]);


                $today = new Carbon('today');
                $stayTime = $today->diffInSeconds($now);
                $workingTimeSeconds = floor($stayTime % 60);
                $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                $workingTimeHours = floor($stayTime / 3600);
                $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;

                $timework = Time::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now(),
                    'start_work' => $now->startOfDay(),
                    'end_work' => Carbon::now(),
                    'worktime' => $workTime,
                    'breaktime' => '00:00:00'
                ]);

                return redirect('/');
            }
            
            $carbontime = Carbon::createFromFormat('H:i:s', $resttime);
            $seconds = $carbontime->hour * 3600 + $carbontime->minute * 60 + $carbontime->second;

            $stayingTime = $startWork->diffInSeconds($endwork); 
            $stayTime = $stayingTime - $seconds; 
            $workingTimeSeconds = floor($stayTime % 60);
            $workingTimeMinutes = floor(($stayTime % 3600) / 60);
            $workingTimeHours = floor($stayTime / 3600);
            $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;
           
            $timework->update([
                'end_work' => $endwork,
                'worktime' => $workTime
            ]);

            $today = new Carbon('today');
            $stayingTime = $today->diffInSeconds($now);  
            $workingTimeSeconds = floor($stayingTime % 60);
            $workingTimeMinutes = floor(($stayingTime % 3600) / 60);
            $workingTimeHours = floor($stayingTime / 3600);
            $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;
            
            $timework = Time::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'start_work' => $now->startOfDay(),
                'end_work' => Carbon::now(),
                'breaktime' => '00:00:00',
                'worktime' => $workTime
            ]);
           
            return redirect('/');
        }
           
        $rest = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(resttime))) as totalresttime')
            ->where('time_id', $time->id)->first();
        $resttime = $rest->totalresttime;
  
        // 勤務時間を算出し整形する(休憩時間がない場合)
        if (empty($resttime)) {
            $stayTime = $startWork->diffInSeconds($now);
            $workingTimeSeconds = floor($stayTime % 60);
            $workingTimeMinutes = floor(($stayTime % 3600) / 60);
            $workingTimeHours = floor($stayTime / 3600);
            $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;
            
            $time->update([
                'end_work' => Carbon::now(),
                'worktime' => $workTime,
                'breaktime' => '00:00:00'
            ]);
            
            return redirect('/');

        } 
        
        // 勤務時間と休憩時間の差分を計算し実働時間を求めるため、合計された休憩時間を取得しその時間を秒数に変換する
        $carbontime = Carbon::createFromFormat('H:i:s', $resttime);
        $seconds = $carbontime->hour * 3600 + $carbontime->minute * 60 + $carbontime->second;

        // 勤務時間を算出し整形する(実働時間)
        $stayingTime = $startWork->diffInSeconds($now); //休憩時間を含めた1日の勤務時間
        $stayTime = $stayingTime - $seconds; //休憩時間を除いた実働時間
        $workingTimeSeconds = floor($stayTime % 60);
        $workingTimeMinutes = floor(($stayTime % 3600) / 60);
        $workingTimeHours = floor($stayTime / 3600);
        $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;
        
        $time->update([
            'end_work' => Carbon::now(),
            'worktime' => $workTime
        ]);
        
        return redirect('/');
            
    }

    // 日付別勤怠一覧
    public function attend(Request $request)
    {
        $user = Auth::user();

        $time = Time::where('user_id', $user->id)->latest()->first();

        if (empty($request->start_work)) {
            $yesterday = Carbon::yesterday();
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
        } 

        $today = new Carbon($request->start_work);
        $yesterday = (new Carbon($request->start_work))->subDay();
        $tomorrow = (new Carbon($request->start_work))->addDay();
        
        $times = Time::whereDate('start_work', $today->format('Y-m-d'))->paginate(5);

        return view('attendance', compact('times', 'today', 'yesterday', 'tomorrow'));

    }

    // ユーザー一覧
    public function userlist()
    {
        $users = User::select('id', 'name', 'email')->paginate(5);

        return view('userlist', compact('users'));
    }

    // ユーザー別勤怠一覧
    public function userattend()
    {
        $user = Auth::user();

        $times = Time::where('user_id', $user->id)->paginate(5);

        return view('userattend', compact('times'));
    }

}
