<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TabController extends Controller
{
    public function saveActiveTab(Request $request)
    {
        $request->session()->put('active_tab', $request->activeTab);
        return response()->json(['status' => 'success']);
    }
}
