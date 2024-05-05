<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\RegionKey;
class RegionController extends Controller
{

    public function get_all_regions(){
        $get = Region::get();

        return view('admin.Regions.all', compact('get'));
    }

    public function single_page_region($id){
        $get = Region::where('id', $id)->first();
        $get_keys = RegionKey::get();

        return view('admin.Regions.single', compact('get','get_keys'));
    }

    public function update_region(Request  $request){

        Region::where('id', $request->region_id)->update([
           'key_id' => $request->key_id,
           'name' => $request->name
        ]);


        return redirect()->back()->with('created','created');
    }


}
