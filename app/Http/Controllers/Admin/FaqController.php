<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
class FaqController extends Controller
{

    public function all_faqs(){
        $get = Faq::get();
        return view('admin.FAQ.all', compact('get'));
    }


    public function create_faq_page(){

        return view('admin.FAQ.create');
    }

    public function create_faq(Request $request){


     $create =    Faq::create([
           'faq' => $request->faq,
           'replay' => $request->replay
        ]);



        return redirect()->route('single_page_faq', $create->id)->with('created','created');
    }

    public function single_page_faq($id){
        $get = Faq::where('id', $id)->first();
        return view('admin.FAQ.single', compact('get'));
    }

    public function delete_faq($id){
        Faq::where('id', $id)->delete();
        return redirect()->route('all_faqs');
    }

    public function update_faq(Request $request){
        Faq::where('id', $request->faq_id)->update([
           'faq'=> $request->faq,
           'replay' => $request->replay
        ]);


        return redirect()->back()->with('created','created');
    }

    public function get_faqs(){
        $get = Faq::get();


        return response()->json([
           'status' => true,
           'data' => $get
        ]);
    }


}
