<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserProfileModules;
use Illuminate\Support\Facades\Auth;

class ModulePermissionMiddleware
{
    public function handle($request, Closure $next, ...$modules)
    {
        $user = Auth::user();

        $allowedModules = UserProfileModules::where('profile_id', $user->profile_id)
            ->whereIn('module', $modules)
            ->pluck('module')
            ->toArray();

        if (!empty($allowedModules)) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized.'], 401);
    }
}
