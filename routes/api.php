<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/user', function (Request $request) {
    /**
     * @var User $user
     */
    $user = $request->user();

    return response()->json([
        'message' => 'User get successfully',
        'data' => UserResource::make($user->load('role'))->resolve($request),
    ]);
})->middleware('auth:sanctum');
