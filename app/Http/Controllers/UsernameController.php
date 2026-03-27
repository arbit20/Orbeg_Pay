<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsernameController extends Controller
{
    private const ADJECTIVES = [
        'golden', 'silver', 'brave', 'swift', 'noble', 'bold', 'keen',
        'iron', 'steel', 'bright', 'dark', 'grand', 'prime', 'elite',
        'rapid', 'sharp', 'solid', 'wise', 'epic', 'true', 'pure',
        'wild', 'cool', 'fast', 'mega', 'ultra', 'super', 'alpha',
    ];

    private const NOUNS = [
        'trader', 'falcon', 'eagle', 'hawk', 'tiger', 'wolf', 'bear',
        'lion', 'shark', 'fox', 'vault', 'forge', 'crown', 'blade',
        'storm', 'flame', 'stone', 'ridge', 'peak', 'reef', 'crest',
        'star', 'orbit', 'nexus', 'pulse', 'flux', 'core', 'echo',
    ];

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'min:4', 'max:50'],
        ]);

        $username = trim($request->input('username'));
        $userId = $request->input('user_id');

        $query = User::withTrashed()->where('username', $username);

        if ($userId) {
            $query->where('id', '!=', $userId);
        }

        return response()->json([
            'available' => ! $query->exists(),
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $suggestions = [];
        $maxAttempts = 50;
        $attempts = 0;

        while (count($suggestions) < 5 && $attempts < $maxAttempts) {
            $attempts++;
            $username = $this->generateRandomUsername();

            if (! User::withTrashed()->where('username', $username)->exists()
                && ! in_array($username, $suggestions, true)
            ) {
                $suggestions[] = $username;
            }
        }

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    private function generateRandomUsername(): string
    {
        $adjective = self::ADJECTIVES[array_rand(self::ADJECTIVES)];
        $noun = self::NOUNS[array_rand(self::NOUNS)];
        $number = random_int(10, 999);

        return $adjective . '_' . $noun . $number;
    }
}
