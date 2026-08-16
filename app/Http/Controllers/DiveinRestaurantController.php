<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorUsers;
use Illuminate\Support\Facades\Auth;
use Session;

class DiveinRestaurantController extends Controller
{
    public function __construct()
    {
    	if(!isset($_COOKIE['address_name'])) {
    		\Redirect::to('set-location')->send();
		}
    }
	
    public function index()
    {
        return view ('dinein.index');
    }

    public function dyiningrestaurant(){
        
        return view('dinein.dinerestaurant');
    }
    
}
