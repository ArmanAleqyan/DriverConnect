<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Jobs;
use App\Models\JobTracker;
use DataTables;
use Illuminate\Support\Facades\Http;

class JobsController extends Controller
{

    public function all_jobs(){
        return view('admin.Jobs.all');


    }


    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Jobs::with('user')->latest(); // Получение данных с отношением user

            return DataTables::eloquent($data)
                ->addColumn('payment_type', function($row) {
                    $paymentTypes = [
                        'cashless' => '<i class="fa fa-credit-card"></i>',
                        'cash' => '<i class="fa fa-money"></i>',
                        'corp' => '<i class="fa fa-building"></i>'
                    ];

             
                    return $paymentTypes[$row->payment_method] ?? 'Неизвестный';
                }) ->addColumn('status', function($row) {
                    $paymentTypes = [
                        'cancelled' => 'Отменённый',
                        'complete' => 'Завершённый',
                        'transporting' => 'Транспортировка',
                        'waiting' => 'Ожидание',
                        'driving' => 'На заказе'
                    ];


                    return $paymentTypes[$row->status] ?? 'Неизвестный';
                })
                ->addColumn('action', function($row) {
                    $route = route('single_page_job', $row->id);
                    $btn = '<a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="' . $route . '">
                                                        <i class="nav-icon fa fa-cogs"></i>
                                                    </a>';
                    return $btn;
                }) ->rawColumns(['payment_type', 'action'])
                ->toJson();
        }
    }

    public function single_page_job ($id){
        $get_job  =Jobs::where('id', $id)->first();

        if ($get_job->track_status == 1){
            $key = \App\Models\RegionKey::where('id', $get_job->user->park_id)->first();

            $X_Park_ID = $key->X_Park_ID;
            $X_Client_ID = $key->X_Client_ID;
            $X_API_Key = $key->X_API_Key;
            $url = "https://fleet-api.taxi.yandex.net/v1/parks/orders/track?park_id={$X_Park_ID}&order_id={$get_job->yandex_id}";

            $response = Http::withHeaders([
                'X-Client-ID' => $X_Client_ID,
                'X-API-Key' => $X_API_Key,
            ])->post($url)->json();




            if (isset($response['track'])){
                foreach ($response['track'] as $track){
                    JobTracker::updateOrcreate([
                        'job_id' => $get_job->id,
                        'user_id' => $get_job->user_id,
                        'tracked_at' => $track['tracked_at']??null
                    ],[
                        'job_id' => $get_job->id,
                        'user_id' => $get_job->user_id,
                        'tracked_at' => $track['tracked_at']??null,
                        'lat' => $track['location']['lat']??null,
                        'long' => $track['location']['lon']??null,
                        'speed' => $track['speed']??null,
                        'order_status' => $track['order_status']??null,
                        'direction' => $track['direction']??null,
                        'distance' => $track['distance']??null,
                    ]);
                }
                if ($get_job->status == 'complete' || $get_job->status == 'cancelled'){
                    $get_tracker_count = JobTracker::where('job_id', $get_job->id)->count();
                    if ($get_tracker_count  == count($response['track'])){
                        $get_job->update([
                           'track_status' => 0
                        ]);
                    }
                }

            }
        }
        $routes = $get_job->routes->map(function ($route) {
            return [
                'lat' => $route->lat,
                'long' => $route->lon,
            ];
        })->toArray();

        $start_routes = [
            [
                'lat' => $get_job['address_from_lat'],
                'long' => $get_job['address_from_long'],
            ]
        ];

        $tracker_routes = JobTracker::where('job_id', $get_job->id)->get(['lat', 'long'])->map(function ($tracker) {
            return [
                'lat' => $tracker->lat,
                'long' => $tracker->long,
            ];
        })->toArray();

// Объединение всех массивов, чтобы $start_routes был первым
        $all_routes = array_merge($start_routes, $tracker_routes,$routes );



        return view('admin.Jobs.single', compact('get_job','all_routes'));
    }
}
