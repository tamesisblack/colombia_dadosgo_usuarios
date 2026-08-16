<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\FirestoreHelper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class FirebaseMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {      
        $locale = Session::get('locale', config('app.locale'));
        App::setLocale($locale);

        $maintenance_settings = FirestoreHelper::getDocument('settings/maintenance_mode_settings');

        if (!empty($maintenance_settings)) {
            if ($maintenance_settings['customerApp'] === true) {
                return response()->view('maintenance');
            }
        }

        return $next($request);
    }
}
