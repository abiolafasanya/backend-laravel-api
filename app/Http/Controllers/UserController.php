<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\UserPreferences;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function userPreferences(Request $request)
    {
        $validatedData = $request->validate([
            'sources' => 'nullable|array',
            'categories' => 'nullable|array',
            'authors' => 'nullable|array',
        ]);

        $user = $request->user();

        $data = [
            'preferred_sources' => $validatedData['sources'] ?? [],
            'preferred_categories' => $validatedData['categories'] ?? [],
            'preferred_authors' => $validatedData['authors'] ?? [],
        ];

        $preferences = UserPreferences::updateOrCreate(['user_id' => $user->id], $data);

        return response()->json([
            'message' => 'User preferences updated successfully.',
            'result' => $preferences,
        ], 200);
    }
}
