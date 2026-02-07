<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpRequest;

class HelpController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->city_id;
        $datas = HelpRequest::with('user')
            ->withCount('helpAssignments as helpers_count')
            ->where('city_id', $cityId)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->get();

        $response = $datas->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->user->name,
                'create_at' => $item->created_at,
                'status' => $item->status,
                'helpers_count' => $item->helpers_count,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'address' => $item->address,
            ];
        });

        return response()->json($response);
    }
}
