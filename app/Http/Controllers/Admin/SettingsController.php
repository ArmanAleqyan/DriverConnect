<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialData;
class SettingsController extends Controller
{

    public function settings_page(){
        $get_whattsap_and_telegram = SocialData::first();
        return view('admin.Settings.all', compact('get_whattsap_and_telegram'));
    }


    public function update_whattsap_and_telegram(Request $request){
        SocialData::first()->update([
           'whatsapp'  => $request->whatsapp,
           'telegram'  => $request->telegram,
        ]);

        return redirect()->back()->with('updated', 'updated');
    }
}
