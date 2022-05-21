<?php

use App\Mail\Logs;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function custom_shell_exec($cmd, &$stdout = null, &$stderr = null)
{
    $proc = proc_open($cmd, [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    return proc_close($proc);
}

function get_log($commands, $stdout, $stderr)
{
    $date = date('m/d/Y h:i:s a', time());
    $status = $stdout != null ? "OK" : "ERROR";
    $output = $stdout != null ? $stdout : $stderr;

    return "$date, $commands, $status, $output";
}


Route::get('/', function (Request $request) {
    $r = $request->input('r');
    $init = $request->input('initialValues');

    $stdout = '';
    $stderr = '';

    custom_shell_exec("octave-cli --eval \"m1 = 2500; m2 = 320;k1 = 80000; k2 = 500000;b1 = 350; b2 = 15020;pkg load control;A=[0 1 0 0;-(b1*b2)/(m1*m2) 0 ((b1/m1)*((b1/m1)+(b1/m2)+(b2/m2)))-(k1/m1) -(b1/m1);b2/m2 0 -((b1/m1)+(b1/m2)+(b2/m2)) 1;k2/m2 0 -((k1/m1)+(k1/m2)+(k2/m2)) 0];B=[0 0;1/m1 (b1*b2)/(m1*m2);0 -(b2/m2);(1/m1)+(1/m2) -(k2/m2)];C=[0 0 1 0]; D=[0 0];Aa = [[A,transpose([0 0 0 0])];[C, 0]];Ba = [B;[0 0]];Ca = [C,0]; Da = D;K = [0 2.3e6 5e8 0 8e6];sys = ss(Aa-Ba(:,1)*K,Ba,Ca,Da);t = 0:0.01:5;r =\"$r\";initX1=0; initX1d=0;initX2=0; initX2d=0;[y,t,x]=lsim(sys*[0;1],r*ones(size(t)),t,[\"$init[0]\";\"$init[1]\";\"$init[2]\";\"$init[3]\";0]);save x3.txt y; save x1.txt x(:,1); save t.txt t; disp([t, x(:,1), y]);\"", $stdout, $stderr);
    $log = get_log("m1 = 2500; m2 = 320;k1 = 80000; k2 = 500000;b1 = 350; b2 = 15020;pkg load control;A=[0 1 0 0;-(b1*b2)/(m1*m2) 0 ((b1/m1)*((b1/m1)+(b1/m2)+(b2/m2)))-(k1/m1) -(b1/m1);b2/m2 0 -((b1/m1)+(b1/m2)+(b2/m2)) 1;k2/m2 0 -((k1/m1)+(k1/m2)+(k2/m2)) 0];B=[0 0;1/m1 (b1*b2)/(m1*m2);0 -(b2/m2);(1/m1)+(1/m2) -(k2/m2)];C=[0 0 1 0]; D=[0 0];Aa = [[A,transpose([0 0 0 0])];[C, 0]];Ba = [B;[0 0]];Ca = [C,0]; Da = D;K = [0 2.3e6 5e8 0 8e6];sys = ss(Aa-Ba(:,1)*K,Ba,Ca,Da);t = 0:0.01:5;r =\"$r\";initX1=0; initX1d=0;initX2=0; initX2d=0;[y,t,x]=lsim(sys*[0;1],r*ones(size(t)),t,[\"$init[0]\";\"$init[1]\";\"$init[2]\";\"$init[3]\";0]);save x3.txt y; save x1.txt x(:,1); save t.txt t; disp([t, x(:,1), y]);", $stdout, $stderr);
    file_put_contents('logs.csv', $log . PHP_EOL, FILE_APPEND | LOCK_EX);

    return $stdout != null ? $stdout : $stderr;
});

Route::get('/calculation', function (Request $request) {
    $command = $request->input('command');


    $stdout = '';
    $stderr = '';


    custom_shell_exec("octave-cli --eval \"$command\"", $stdout, $stderr);
    $log = get_log($command, $stdout, $stderr);
    file_put_contents('logs.csv', $log . PHP_EOL, FILE_APPEND | LOCK_EX);

    return $stdout != null ? $stdout : $stderr;
});

Route::get('/email', function () {
    Mail::to('mecir.martin@gmail.com')->send(new Logs());
});
