<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegionKey;
use App\Models\Region;
use App\Models\YandexWorkRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RegionKeycontroller extends Controller
{

    public function get_yandex_keys($data){

    }

    public function get_all_key (){
        $get = RegionKey::orderby('id', 'desc')->get();
        return view('admin.Keys.all', compact('get'));
    }

    public function create_key_page(){
        $get_region = Region::get();
        return view('admin.Keys.create', compact('get_region'));
    }

    public function create_key(Request  $request){
        $X_Park_ID =$request->X_Park_ID;
        $X_Client_ID = $request->X_Client_ID;
        $X_API_Key = $request->X_API_Key;
        $response = Http::withHeaders([
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key,
            'Accept-Language' => 'ru',
        ])->get('https://fleet-api.taxi.yandex.net/v1/parks/driver-work-rules', [
            'park_id' => $X_Park_ID,
        ])->json();
        if (isset($response['rules'])){
        }else{
            return  redirect()->back()->with('wrong_key','wrong_key');
        }


        if ($request->default == 'on'){
            RegionKey::where('default' , 1)->update([
               'default' => 0
            ]);
            $default = 1;
        }else{
            $default = 0;
        }

       $create =  RegionKey::create([
           'name' => $request->name,
           'region_id' => $request->region_id,
           'X_Park_ID' =>$request->X_Park_ID,
           'X_Client_ID' =>$request->X_Client_ID,
           'default' => $default,
           'X_API_Key' =>$request->X_API_Key,
        ]);
        $i  = 1;
        foreach ($response['rules'] as $res){
            $default = 0;
            if ($i == 1){
                $default = 1;
            }
            YandexWorkRule::updateorcreate([
                'name' => $res['name'],
                'key_id' => $create->id,
            ],
                [
                    'name' => $res['name'],
                    'is_enabled' => $res['is_enabled'],
                    'yandex_id' => $res['id'],
                    'default' => $default,
                    'key_id' =>$create->id
                ]);
            $i++;
        }
        return redirect()->route('single_page_key',$create->id)->with('created','created');
    }

    public function single_page_key($id){
        $get = RegionKey::where('id', $id)->first();
        $get_region = Region::get();
        return view('admin.Keys.single', compact('get','get_region'));
    }

    public function update_key(Request  $request){

        $X_Park_ID =$request->X_Park_ID;
        $X_Client_ID = $request->X_Client_ID;
        $X_API_Key = $request->X_API_Key;

        $response = Http::withHeaders([
            'X-Client-ID' => $X_Client_ID,
            'X-API-Key' => $X_API_Key,
            'Accept-Language' => 'ru',
        ])->get('https://fleet-api.taxi.yandex.net/v1/parks/driver-work-rules', [
            'park_id' => $X_Park_ID,
        ])->json();

        if (isset($response['rules'])){

        }else{
            return  redirect()->back()->with('wrong_key','wrong_key');
        }
        if ($request->default == 'on'){
            RegionKey::where('default' , 1)->update([
                'default' => 0
            ]);
            $default = 1;
        }else{
            $default = 0;
        }


        YandexWorkRule::where('key_id', $request->key_id)->update([
            'default' =>0
        ]);
        YandexWorkRule::where('id',$request->work_rule_id)->update([
           'default' =>1
        ]);
        $get = RegionKey::where('id', $request->key_id)->first();
        $get->update([
            'name' => $request->name,
            'region_id' => $request->region_id,
            'X_Park_ID' =>$request->X_Park_ID,
            'X_Client_ID' =>$request->X_Client_ID,
            'default' => $default,
            'X_API_Key' =>$request->X_API_Key,
        ]);
        foreach ($response['rules'] as $res){
            YandexWorkRule::updateorcreate([
                'name' => $res['name'],
                'key_id' => $request->key_id
            ],
                [
                    'name' => $res['name'],
                    'is_enabled' => $res['is_enabled'],
                    'yandex_id' => $res['id'],
                    'key_id' =>$request->key_id
                ]);
        }
        return redirect()->back()->with('created','created');

    }


    public function delete_key($id){
        $get =    RegionKey::where('id', $id)->first();
        if ($get->default == 1){
            RegionKey::where('id', '!=',$id)->first()->update([
               'default' => 1
            ]);
        }
        RegionKey::where('id', $id)->delete();
        return redirect()->route('get_all_key')->with('deleted','deleted');
    }
}
