<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        "middleware" => "api",
        "prefix" => "v1",
    ],
    function ($router) {
        Route::post("/user/login", [
            AuthenticateController::class,
            "authenticate",
        ]);
        Route::post("/user/create", [
            AuthenticateController::class,
            "register",
        ]);
        Route::get("user/logout", [AuthenticateController::class, "logout"]);
    }
);

Route::group(["middleware" => ["jwt.verify"], "prefix" => "v1"], function () {
    Route::get("user", [AuthenticateController::class, "getUser"]);
    Route::delete("user", [AuthenticateController::class, "destroy"]);
});
