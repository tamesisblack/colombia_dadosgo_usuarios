<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorUsers;
use Illuminate\Support\Facades\Auth;
use Session;

class AllRestaurantsController extends Controller
{
    public function __construct()
    {
    	if(!isset($_COOKIE['address_name'])) {
    		\Redirect::to('set-location')->send();
		}
    }
	
    public function index()
    {
        return view('allrestaurants.index');
    }

    public function dineinRestaurants(){
    	return view ('dinein.index');
    }
    
    public function RestaurantsbyCategory($id)
    {
        return view('allrestaurants.bycategory',['id' => $id]);
    }
    
}
