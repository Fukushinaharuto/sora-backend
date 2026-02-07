<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpRequest;
use App\Http\Requests\HelpStoreRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HelpAssignment;

class HelpController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->city_id;
        $datas = HelpRequest::query()
            ->with('user')
            ->withCount([
                'helpAssignments as helpers_count' => function ($query) {
                    $query->where('status', 'in_progress');
                }
            ])
            ->where('city_id', $cityId)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->get();

        $response = $datas->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->user->name,
                'create_at' => $item->created_at->toISOString(),
                'status' => $item->status,
                'helpers_count' => $item->helpers_count,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'address' => $item->address,
            ];
        });

        return response()->json($response);
    }

    public function store(HelpStoreRequest $request)
    {
        $validatedData = $request->validated();
        $userId = Auth::id();

        $existingRequest = HelpRequest::where('user_id', $userId)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'messages' => 'すでに進行中の申請があります。'
            ], 422);
        }

        HelpRequest::create([
            'user_id' => Auth::id(),
            'city_id' => $validatedData['city_id'],
            'message' => $validatedData['message'],
            'address' => $validatedData['address'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
        ]);

        return response()->json(['message' => 'お助け申請ができました。'], 201);
    }

    public function markHelped()
    {
        $userId = Auth::id();

        try {
            DB::transaction(function () use ($userId) {
                $request = HelpRequest::where('user_id', $userId)
                    ->where('status', 'in_progress')
                    ->first();

                if (!$request) {
                    throw new \Exception('お助け申請が見つかりません。', 404);
                }

                $request->update(['status' => 'completed']);

                $request->helpAssignments()->update(['status' => 'completed']);
            });

            return response()->json(['message' => 'お助け完了をマークしました。'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'お助け完了の更新に失敗しました。',
            ], 500);
        }
    }

    public function assign(Request $request)
    {
        $userId = Auth::id();
        try {
            DB::transaction(function () use ($request, $userId) {
                $helpRequest = HelpRequest::where('id', $request->help_request_id)
                    ->where('status', 'waiting')
                    ->first();

                if (!$helpRequest) {
                    throw new \Exception('お助け申請が見つかりません。', 404);
                }
                $existingAssignment = HelpAssignment::where('user_id', $userId)
                    ->where('status', 'in_progress')
                    ->exists();

                if ($existingAssignment) {
                    throw new \Exception('すでに進行中のお助け申請があります。', 403);
                }

                $inProgressAssignments = $helpRequest->helpAssignments()
                    ->where('status', 'in_progress')
                    ->exists();

                if ($inProgressAssignments) {
                    throw new \Exception('この申請は既に他の人が助けています。', 403);
                }

                $helpRequest->update(['status' => 'in_progress']);

                $helpRequest->helpAssignments()->create([
                    'user_id' => $userId,
                    'status' => 'in_progress',
                ]);
            });

            return response()->json([
                'message' => 'お助けに参加しました。'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'お助け参加に失敗しました。',
            ], 500);
        }
    }
}
