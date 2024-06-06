<?php

namespace App\Http\Controllers\Parsing;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCar;
use App\Models\Jobs;
use App\Models\JobEvent;
use App\Models\JobRoutePoints;
use App\Models\CarCategory;
use App\Models\JobTranzaksion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\RegionKey;
class OrdersController extends Controller
{

    public function get_hour_status_transporting(){
        $get  =  Jobs::wheredate('created_at', '!=', \Carbon\Carbon::now()->format('Y-m-d'))->where('status','transporting')->get();
        $get_key = RegionKey::get();
        $get_job_category = CarCategory::get();


  

        foreach ($get as $noget){
            $key = $get_key->where('id', $noget->user->park_id)->first();

            $X_Park_ID = $key->X_Park_ID;
            $X_Client_ID = $key->X_Client_ID;
            $X_API_Key = $key->X_API_Key;

            $response = Http::withHeaders([
                'X-Client-ID' => $X_Client_ID,
                'X-Api-Key' => $X_API_Key,
            ])->post('https://fleet-api.taxi.yandex.net/v1/parks/orders/list', [
                'limit' => 500,
                'query' => [
                    'park' => [
                        'driver_profile' => [
                            'id' => $noget->user->contractor_profile_id,
                        ],
                        'id' => $X_Park_ID,
                        'order' => [
                            'booked_at' => [
                                'from' =>Carbon::parse($noget->booked_at) ->startofday(),
                                'to' => Carbon::parse($noget->booked_at)  ->endofday(),
                            ],
                            "ids" => [
                "$noget->yandex_id"
            ],
                        ],
                    ],
                ],
            ])->json();

            $user_id = $noget->user->id;


            if ($response['orders']  != []){

                foreach ($response['orders'] as $orders ){
                    $ended_at =null;


                    if (isset($orders['ended_at'])){

                        $ended_at = Carbon::parse($orders['ended_at']);
                    }
                    $get_car = null;
                    if (isset($orders['car'])){
                        $get_car = UserCar::where('yandex_car_id', $orders['car']['id'])->first();
                    }
                    $order_time_interval_from =  Carbon::parse($orders['order_time_interval']['from']) ??null;
                    $order_time_interval_to =  Carbon::parse($orders['order_time_interval']['to']) ??null;




                    $create_job_data =   Jobs::updateorcreate(['yandex_id' => $orders['id'],   'user_id' =>$user_id,'short_id' => $orders['short_id']],[
                        'yandex_id' => $orders['id'],

                        'short_id' => $orders['short_id'],
                        'user_id' =>$user_id,
                        'car_id' =>$get_car->id??null,
                        'status' => $orders['status'],
                        'created_at' => Carbon::parse($orders['created_at']),
                        'booked_at' => Carbon::parse($orders['booked_at']),
                        'provider' => $orders['provider'],
                        'mileage' => $orders['mileage']??null,
                        'ended_at' =>$ended_at,
                        'order_time_interval_from' =>$order_time_interval_from,
                        'order_time_interval_to' =>$order_time_interval_to,
                        'payment_method' => $orders['payment_method']??null,
                        'price' => $orders['price']??null,
                        'address_from' => $orders['address_from']['address']??null,
                        'address_from_lat' => $orders['address_from']['lat']??null,
                        'address_from_long' => $orders['address_from']['lon']??null,
                        'amenities' => json_encode($orders['amenities']) ,
                        'car_category_id' =>$get_job_category->where('name',$orders['category']??null)->first()->id??null,
                    ]);



                    if (isset($orders['route_points'])){
                        JobRoutePoints::where('job_id', $create_job_data->id)->delete();
                        foreach ($orders['route_points'] as $route_point){
                            JobRoutePoints::create([
                                'job_id' => $create_job_data->id,
                                'user_id' => $user_id,
                                'address' => $route_point['address'],
                                'lat' => $route_point['lat'],
                                'lon' => $route_point['lon'],
                            ]);
                        }
                    }

                    if (isset($orders['events'])){
                        foreach ($orders['events'] as $event) {
                            JobEvent::updateorcreate([
                                'job_id' => $create_job_data->id,
                                'order_status' => $event['order_status'],
                            ],[
                                'job_id' => $create_job_data->id,
                                'order_status' => $event['order_status'],
                                'event_at' => Carbon::parse($event['event_at']) ,
                                'user_id' => $user_id
                            ]);
                        }

                    }

                    $response_tranzakcion = Http::withHeaders([
                        'X-Client-ID' => $X_Client_ID,
                        'X-Api-Key' => $X_API_Key,
                    ])->post('https://fleet-api.taxi.yandex.net/v2/parks/orders/transactions/list', [
                        'limit' => 500,
                        'query' => [
                            'park' => [
                                'id' => $X_Park_ID,
                                'order' => [
                                    'ids' =>[$orders['id']]
                                ],
                            ],
                        ],
                    ])->json();



                    if ($response_tranzakcion['transactions'] != []){
                        foreach ($response_tranzakcion['transactions'] as $tranz){
                            $create_tranzakcion[] =  JobTranzaksion::updateorcreate([
                                'yandex_id' => $tranz['id']
                            ],[
                                'yandex_id' => $tranz['id'],
                                'user_id' => $user_id,
                                'job_id' => $create_job_data->id,
                                'event_at' => $tranz['event_at'],
                                'category_id' => $tranz['category_id'],
                                'category_name' => $tranz['category_name'],
                                'group_id' => $tranz['group_id'],
                                'amount' => $tranz['amount'],
                                'currency_code' => $tranz['currency_code'],
                                'description' => $tranz['description'],
                                'order_id' => $tranz['order_id'],
                                'created_by' => json_encode($tranz['created_by']) ,


                            ]);
                        }
                        if ( $orders['price'] > 0 ){
                            $collect = collect($create_tranzakcion);
                            $job_price = $collect->filter(function ($item) {
                                return strpos($item['amount'], '-') === false;
                            })->sum('amount');
                            $job_fee = $collect->filter(function ($item) {
                                return strpos($item['amount'], '-') !== false;
                            })->sum('amount');
                            $job_price_minus_fee = $job_price - abs($job_fee) ;
                            if (!isset($orders['mileage'])){
                                $orders['mileage'] = 0;
                            }
                            $meters =  $orders['mileage'];
                            $kilometers = $meters / 1000;
                            $kilometersFormatted = number_format($kilometers, 2, ',', '');
                            $differenceInMinutes = $order_time_interval_from->diffInMinutes($order_time_interval_to, false);
                            $price_in_minute = $job_price / $differenceInMinutes;
                            $price_in_minute  = number_format($price_in_minute,2 ) ;
                            if ($orders['mileage'] != 0){
                                $price_in_km =  $job_price / floatval(str_replace(',', '.', $kilometersFormatted));
                                $price_in_km = number_format($price_in_km,2);
                            }
                            $diff = $order_time_interval_from->diff($order_time_interval_to);
                            $hours = $diff->h;
                            $minutes = $diff->i;
                            $seconds = $diff->s;
                            $timeDifferenceFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                            $create_job_data->update([
                                'job_fee' =>number_format(abs($job_fee )) ,
                                'job_price_with_bonus' =>number_format(abs($job_price ),2) ,
                                'job_price_minus_fee' =>number_format(abs($job_price_minus_fee),2) ,
                                'price_in_minute' => $price_in_minute??null,
                                'price_in_km' => $price_in_km??null,
                                'work_km' => $kilometersFormatted??null,
                                'timeDifferenceFormatted' => $timeDifferenceFormatted??null,
                            ]);
                            $create_tranzakcion = null;
                        }


                    }
                }

            }


        }
    }

    public function get_order(){

        $get_job_category = CarCategory::get();

        $get_keys = RegionKey::get();

        foreach ($get_keys as $key) {
            $X_Park_ID = $key->X_Park_ID;
            $X_Client_ID = $key->X_Client_ID;
            $X_API_Key = $key->X_API_Key;

            $get_users = User::where('contractor_profile_id', '!=', null)->where('work_status', 'working')->where('park_id', $key->id)->get();



            foreach ($get_users  as $users){
                $response = Http::withHeaders([
                    'X-Client-ID' => $X_Client_ID,
                    'X-Api-Key' => $X_API_Key,
                ])->post('https://fleet-api.taxi.yandex.net/v1/parks/orders/list', [
                    'limit' => 500,
                    'query' => [
                        'park' => [
                    'driver_profile' => [
                        'id' => $users->contractor_profile_id,
                    ],
                            'id' => $X_Park_ID,
                            'order' => [
                                'booked_at' => [
                                    'from' => Carbon::now()->startOfday(),
                                    'to' => Carbon::now()->endOfDay(),
                                ],
                            ],
                        ],
                    ],
                ])->json();


                if ($response['orders']  != []){


                    foreach ($response['orders'] as $orders ){
//                        dd($orders);
                        $ended_at =null;


                        if (isset($orders['ended_at'])){

                            $ended_at = Carbon::parse($orders['ended_at']);
                        }
                        $get_car = null;
                        if (isset($orders['car'])){
                            $get_car = UserCar::where('yandex_car_id', $orders['car']['id'])->first();
                        }
                        $order_time_interval_from =  Carbon::parse($orders['order_time_interval']['from']) ??null;
                        $order_time_interval_to =  Carbon::parse($orders['order_time_interval']['to']) ??null;




                        $create_job_data =   Jobs::updateorcreate(['yandex_id' => $orders['id'],   'user_id' =>$users->id,'short_id' => $orders['short_id']],[
                                'yandex_id' => $orders['id'],

                                'short_id' => $orders['short_id'],
                                'user_id' =>$users->id,
                                'car_id' =>$get_car->id??null,
                                'status' => $orders['status'],
                                'created_at' => Carbon::parse($orders['created_at']),
                                'booked_at' => Carbon::parse($orders['booked_at']),
                                'provider' => $orders['provider'],
                                'mileage' => $orders['mileage']??null,
                                'ended_at' =>$ended_at,
                                'order_time_interval_from' =>$order_time_interval_from,
                                'order_time_interval_to' =>$order_time_interval_to,
                                'payment_method' => $orders['payment_method']??null,
                                'price' => $orders['price']??null,
                                'address_from' => $orders['address_from']['address']??null,
                                'address_from_lat' => $orders['address_from']['lat']??null,
                                'address_from_long' => $orders['address_from']['lon']??null,
                                'amenities' => json_encode($orders['amenities']) ,
                                'car_category_id' =>$get_job_category->where('name',$orders['category']??null)->first()->id??null,
                            ]);



                        if (isset($orders['route_points'])){
                            JobRoutePoints::where('job_id', $create_job_data->id)->delete();
                            foreach ($orders['route_points'] as $route_point){
                                JobRoutePoints::create([
                                    'job_id' => $create_job_data->id,
                                    'user_id' => $users->id,
                                    'address' => $route_point['address'],
                                    'lat' => $route_point['lat'],
                                    'lon' => $route_point['lon'],
                                ]);
                            }
                        }

                        if (isset($orders['events'])){
                            foreach ($orders['events'] as $event) {
                                JobEvent::updateorcreate([
                                    'job_id' => $create_job_data->id,
                                    'order_status' => $event['order_status'],
                                ],[
                                    'job_id' => $create_job_data->id,
                                    'order_status' => $event['order_status'],
                                    'event_at' => Carbon::parse($event['event_at']) ,
                                    'user_id' => $users->id
                                ]);
                            }

                        }

                        $response_tranzakcion = Http::withHeaders([
                            'X-Client-ID' => $X_Client_ID,
                            'X-Api-Key' => $X_API_Key,
                        ])->post('https://fleet-api.taxi.yandex.net/v2/parks/orders/transactions/list', [
                            'limit' => 500,
                            'query' => [
                                'park' => [
                                    'id' => $X_Park_ID,
                                    'order' => [
                                        'ids' =>[$orders['id']]
                                    ],
                                ],
                            ],
                        ])->json();



                        if ($response_tranzakcion['transactions'] != []){
                            foreach ($response_tranzakcion['transactions'] as $tranz){
                              $create_tranzakcion[] =  JobTranzaksion::updateorcreate([
                                    'yandex_id' => $tranz['id']
                                ],[
                                    'yandex_id' => $tranz['id'],
                                    'user_id' => $users->id,
                                    'job_id' => $create_job_data->id,
                                    'event_at' => $tranz['event_at'],
                                    'category_id' => $tranz['category_id'],
                                    'category_name' => $tranz['category_name'],
                                    'group_id' => $tranz['group_id'],
                                    'amount' => $tranz['amount'],
                                    'currency_code' => $tranz['currency_code'],
                                    'description' => $tranz['description'],
                                    'order_id' => $tranz['order_id'],
                                    'created_by' => json_encode($tranz['created_by']) ,


                                ]);
                            }
                            if ( $orders['price'] > 0 ){
                                $collect = collect($create_tranzakcion);
                                $job_price = $collect->filter(function ($item) {
                                    return strpos($item['amount'], '-') === false;
                                })->sum('amount');
                                $job_fee = $collect->filter(function ($item) {
                                    return strpos($item['amount'], '-') !== false;
                                })->sum('amount');
                                $job_price_minus_fee = $job_price - abs($job_fee) ;
                                    if (!isset($orders['mileage'])){
                                        $orders['mileage'] = 0;
                                    }
                                    $meters =  $orders['mileage'];
                                    $kilometers = $meters / 1000;
                                    $kilometersFormatted = number_format($kilometers, 2, ',', '');
                                    $differenceInMinutes = $order_time_interval_from->diffInMinutes($order_time_interval_to, false);
                                    $price_in_minute = $job_price / $differenceInMinutes;
                                    $price_in_minute  = number_format($price_in_minute,2 ) ;
                                    if ($orders['mileage'] != 0){
                                        $price_in_km =  $job_price / floatval(str_replace(',', '.', $kilometersFormatted));
                                        $price_in_km = number_format($price_in_km,2);
                                    }
                                    $diff = $order_time_interval_from->diff($order_time_interval_to);
                                    $hours = $diff->h;
                                    $minutes = $diff->i;
                                    $seconds = $diff->s;
                                    $timeDifferenceFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                $create_job_data->update([
                                    'job_fee' =>number_format(abs($job_fee )) ,
                                    'job_price_with_bonus' =>number_format(abs($job_price ),2) ,
                                    'job_price_minus_fee' =>number_format(abs($job_price_minus_fee),2) ,
                                    'price_in_minute' => $price_in_minute??null,
                                    'price_in_km' => $price_in_km??null,
                                    'work_km' => $kilometersFormatted??null,
                                    'timeDifferenceFormatted' => $timeDifferenceFormatted??null,
                                ]);
                                $create_tranzakcion = null;
                            }


                        }
                    }

                }
            }










        }
    }



}
