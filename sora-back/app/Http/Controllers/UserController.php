<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;

class UserController extends Controller
{
    public function city(Request $request)
    {
        $city = City::where('name', $request->city_name)->firstOrFail();

        if (Auth::check()) {
            Auth::user()->update(['city_id' => $city->id]);
        }

        return response()->json([
            'auth_check' => Auth::check(),
            'city_id' => $city->id,
        ]);
    }
}
