<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\UserCar;
class UsersController extends Controller
{

    public function new_users(){
        $get_users = User::where('create_account_status', 0)->where('role_id','!=', 1)->orwhere('add_car_status',0)->where('role_id','!=', 1)->orwhere('sended_in_yandex_status',  0)->where('role_id','!=', 1)->get();
        $regions = Region::get();
        return view('admin.Users.UnconfimedUsers', compact('get_users','regions'));
    }


    public function all_users(Request $request){
        $get_users = User::where('create_account_status', 1)->where('add_car_status',1)->where('role_id','!=', 1)->where('sended_in_yandex_status',  1)->get();

        $regions = Region::get();
        return view('admin.Users.Users', compact('get_users','regions'));
    }

    public function single_page_user($id){
        $get = User::where('id', $id)->first();

        $get_car = UserCar::where('user_id', $id)->where('connected_status', 1)->latest()->first();
        return view('admin.Users.single', compact('get','get_car'));
    }
}
