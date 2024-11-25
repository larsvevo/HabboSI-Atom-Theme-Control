<?php

namespace Atom\Theme\Http\Controllers\Api;

use Atom\Core\Models\User;
use Atom\Theme\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, User $user): JsonResponse
    {
        if (! config('theme.api.user_endpoint_enabled')) {
            return response()->json(['error' => 'This endpoint is disabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        return UserResource::make($user)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
