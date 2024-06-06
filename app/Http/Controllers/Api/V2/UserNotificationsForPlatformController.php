<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotificationPivot;
use App\Models\UserNotificationMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserNotificationsForPlatformController extends Controller
{



    public function get_user_for_notification(){
        $get_messages = UserNotificationMessages::get();


        $get_new_registred_users = User::where('user_register_information_message', 0)->where('phone', '!=', null)->where('contractor_profile_id', '!=', null)->get(['phone', 'name', 'surname','user_register_information_message']);


        foreach ($get_new_registred_users as $user ){
            $message =  $get_messages->where('type', 'start_working')->first()->message;
            $name = $user->name;
            $surname = $user->surname;
            $message = str_replace(['{name}', '{surname}'], [$name, $surname], $message);
            $data['phone'] = $user->phone;
            $data['message'] = $message;
              $send =    $this->send_message($data);
              if ($send == true){
                  $user->update([
                     'user_register_information_message' =>1
                  ]);
              }
        }

        $get_notifications = UserNotificationMessages::where('type', 'not_working')->get();






        foreach ($get_notifications as $notification) {
            $notification_id = $notification->id;
            $get_not_working_users =  User::doesntHave('jobs')
                ->whereDoesntHave('notifications', function($query) use ($notification_id) {
                    $query->where('notification_id', $notification_id);
                })
                ->where('phone', '!=', null)->where('contractor_profile_id', '!=', null)->get(['phone', 'id','name', 'surname','user_register_information_message','created_at']);


            $message = $notification->message;
            foreach ($get_not_working_users as $user){
                $get_validation =UserNotificationPivot::where('user_id', $user->id)->where('notification_id', $notification->id)->first();
                if ($get_validation  == null){
                    $date = Carbon::now()->subdays($notification->day);
                    if ($date <= $user->created_at){
                        $name = $user->name;
                        $surname = $user->surname;
                        $message = str_replace(['{name}', '{surname}'], [$name, $surname], $message);
                        $data['phone'] = $user->phone;
                        $data['message'] = $message;
                        $send =    $this->send_message($data);
                        if ($send == true){
                            UserNotificationPivot::create([
                                'user_id' => $user->id,
                                'notification_id' => $notification->id
                            ]);
                        }
                    }
                }
            }
        }

        $get_notifications = UserNotificationMessages::where('type', 'working')->get();






        foreach ($get_notifications as $notification) {
            $notification_id = $notification->id;
            $get_not_working_users =  User::has('jobs')->
                whereDoesntHave('notifications', function($query) use ($notification_id) {
                    $query->where('notification_id', $notification_id);
                })
                ->where('phone', '!=', null)->where('contractor_profile_id', '!=', null)->get(['phone', 'id','name', 'surname','user_register_information_message','created_at']);

            $message = $notification->message;
            foreach ($get_not_working_users as $user){
                if ($user->jobs->first()->created_at < Carbon::now()->subday($notification->day) ){



                        $name = $user->name;
                        $surname = $user->surname;
                        $message = str_replace(['{name}', '{surname}'], [$name, $surname], $message);
                        $data['phone'] = $user->phone;
                        $data['message'] = $message;
                        $send =    $this->send_message($data);
                        if ($send == true){
                            UserNotificationPivot::create([
                                'user_id' => $user->id,
                                'notification_id' => $notification->id
                            ]);


                }

                }
            }
        }


    }


    public function send_message($data){
        // Параметры запроса
        $profileId = env('WAPPI_ID');
        $token =env('WAPPI_TOKEN');

        $recipient = $data['phone'].'12';
        $messageBody = $data['message'];

        $url = "https://wappi.pro/api/sync/message/send?profile_id={$profileId}";

        // Данные запроса в формате JSON
        $messageJson = [
            'recipient' => $recipient,
            'body' => $messageBody
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ])->post($url, $messageJson)->json();

        if (isset($response['status']) && $response['status'] == 'done'){
            return true;
        }else{

            return false;
        }




    }
}
