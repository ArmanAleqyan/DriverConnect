<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialData;
use App\Models\RegionKey;
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

        $types = $this->types;
        return view('admin.Settings.all', compact('get_whattsap_and_telegram','get_sending_messages','types','get_keys'));
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
