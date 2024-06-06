<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserNotificationMessages;
class AllNewsLettersController extends Controller
{

    public function __construct()
    {
        $this->types = [
          'not_working' => 'Не Работает',
            'start_working' => 'Регистрацыя',
            'working' => 'Начал работать',
        ];
    }

    public function all_news_letters(){
        $get = UserNotificationMessages::get();


        $types = $this->types;
        return view('admin.NewsLetters.all', compact('get','types'));
    }

    public function single_page_letters($id){
        $types = $this->types;
        $get = UserNotificationMessages::where('id',$id)->first();
        return view('admin.NewsLetters.single', compact('get','types'));
    }

    public function update_letters(Request $request){
        UserNotificationMessages::where('id', $request->letters_id)->update([
           'type' => $request->type,
           'day' => $request->day,
           'message' => $request->message,
        ]);


        return redirect()->back()->with('created','created');
    }

    public function delete_letters($id){
        UserNotificationMessages::where('id', $id)->delete();


        return redirect()->route('settings_page');
    }

    public function create_letters_page(){


        $types = $this->types;
        unset($types['start_working']);


        return view('admin.NewsLetters.create', compact('types'));
    }

    public function create_letters(Request $request){
       $create =  UserNotificationMessages::create([
           'type'=> $request->type,
           'day'=> $request->day,
           'message'=> $request->message,
        ]);


        return redirect()->route('single_page_letters',$create->id)->with('created','created');
    }
}
