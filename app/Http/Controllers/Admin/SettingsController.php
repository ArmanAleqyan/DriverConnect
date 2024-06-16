<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialData;
use App\Models\WappiSettings;
use App\Models\RegionKey;
use App\Models\YandexScanning;
use App\Models\PaymentsData;
use App\Models\SmsSettings;
use App\Models\UserNotificationMessages;
class SettingsController extends Controller
{

    public function __construct()
    {
        $this->types = [
            'not_working' => 'Не Работает',
            'start_working' => 'Регистрацыя',
            'working' => 'Начал работать',
        ];
    }
    public function settings_page(){
        $get_whattsap_and_telegram = SocialData::first();
        $get_sending_messages = UserNotificationMessages::get();
        $get_keys = RegionKey::orderby('id', 'desc')->get();
        $get_sberbank__settings = PaymentsData::where('bank_name', 'sberbank')->first()??[];
        $get_sms__settings = SmsSettings::first()??[];
        $get_whatsapp__settings = WappiSettings::first()??[];
        $get_yandex_scanning_keys = YandexScanning::first()??[];
        $types = $this->types;

        return view('admin.Settings.all', compact('get_yandex_scanning_keys','get_whatsapp__settings','get_sms__settings','get_whattsap_and_telegram','get_sending_messages','types','get_keys','get_sberbank__settings'));
    }


    public function update_yandex_scanning(Request $request){
        
        YandexScanning::first()->update([
           'token' => $request->token,
           'folder_id' => $request->folder_id,
        ]);


        return redirect()->back()->with('success', 'Данные успешно обновленны');
    }

    public function update_sms_settings(Request $request){
        SmsSettings::first()->update([
           'login' => $request->login,
           'password' => $request->password,
        ]);


        return redirect()->back()->with('success', 'Обновления успешно завершено');
    }

    public function update_sberbank_data(Request $request){
        PaymentsData::where('bank_name', 'sberbank')->update([
            'scope' =>$request->scope,
            'client_id' =>$request->client_id,
            'client_login' =>$request->client_login,
            'client_password' =>$request->client_password,
            'client_secret' =>$request->client_secret,
        ]);


        return redirect()->back()->with('success', 'Обновления успешно завершено');
    }

    public function update_whattsap_and_telegram(Request $request){
        SocialData::first()->update([
           'whatsapp'  => $request->whatsapp,
           'phone'  => $request->phone,
           'email'  => $request->email,
           'telegram'  => $request->telegram,
        ]);

        return redirect()->back()->with('updated', 'updated');
    }
    public function update_whatsapp_settings(Request $request){
        WappiSettings::first()->update([
           'whatsapp_id'  => $request->whatsapp_id,
           'whatsapp_token'  => $request->whatsapp_token,
           'telegram_token'  => $request->telegram_token,
           'telegram_id'  => $request->telegram_id,
        ]);

        return redirect()->back()->with('success', 'Обновления успешно завершено');
    }

    public function update_company(Request $request){
        SocialData::first()->update([
           'company_work_status' =>  $request->company_work_status,
           'inn' =>  $request->inn,
           'company_name' =>  $request->company_name,
           'ogrn' =>  $request->ogrn,
           'ur_address' =>  $request->ur_address,
           'director' =>  $request->director,
        ]);

        return redirect()->back()->with('updated', 'updated');
    }

    public function update_bank(Request $request){
        SocialData::first()->update([
            'bic' => $request->bic,
            'bank' => $request->bank,
            'kor_schot' => $request->kor_schot,
        ]);
        return redirect()->back()->with('updated', 'updated');
    }



    public function all_news_letters(){

        return view('admin.Settings.all', compact('get_sending_messages','types'));
    }

    public function single_page_letters($id){
        $get = UserNotificationMessages::where('id', $id)->first();
        $types = $this->types;
        return view('admin.Settings.single', compact('get','types'));
    }
}
