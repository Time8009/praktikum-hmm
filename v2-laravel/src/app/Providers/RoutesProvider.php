<?php

use App\Http\Controllers\ControllerAnswer;
use App\Http\Controllers\ControllerFormular;
use App\Http\Controllers\ControllerSubject;
use App\Http\Controllers\ControllerUser;
use App\Http\Controllers\ControllerFile;
use App\Http\Middleware\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ControllerUser::class, 'login']);
Route::post('/register', [ControllerUser::class, 'store']);

Route::middleware(JwtAuth::class)->group(function () {
    Route::get('/formular', [ControllerFormular::class, 'index']);
    Route::get('/formular/{id}', [ControllerFormular::class, 'show']);
    Route::post('/formular', [ControllerFormular::class, 'store']);
    Route::put('/formular/{id}', [ControllerFormular::class, 'update']);
    Route::delete('/formular/{id}', [ControllerFormular::class, 'delete']);

    Route::get('/formularall', function (Request $request, ControllerFormular $controller) {
        $request->merge(['all' => true]);
        return $controller->index($request);
    });
    Route::get('/formularall/{id}', function (Request $request, int $id, ControllerFormular $controller) {
        $request->merge(['all' => true]);
        return $controller->show($request, $id);
    });

    Route::get('/user', [ControllerUser::class, 'index']);
    Route::get('/user/{id}', [ControllerUser::class, 'show']);
    Route::put('/user/{id}', [ControllerUser::class, 'update']);
    Route::delete('/user/{id}', [ControllerUser::class, 'delete']);

    Route::get('/subject', [ControllerSubject::class, 'index']);
    Route::get('/subject/{id}', [ControllerSubject::class, 'show']);

    Route::get('/answer', [ControllerAnswer::class, 'index']);
    Route::get('/answer/{id}', [ControllerAnswer::class, 'show']);
    Route::post('/answer', [ControllerAnswer::class, 'store']);
    Route::put('/answer/{id}', [ControllerAnswer::class, 'update']);
    Route::delete('/answer/{id}', [ControllerAnswer::class, 'delete']);

    Route::get('/file', [ControllerFile::class, 'index']);
    Route::get('/file/{id}', [ControllerFile::class, 'show']);
    Route::get('/file/{id}/download', [ControllerFile::class, 'download']);
    Route::delete('/file/{id}', [ControllerFile::class, 'delete']);
});

Route::middleware(JwtAuth::class . ':A')->group(function () {
    Route::put('/tadmin/{id}', [ControllerUser::class, 'admin']);
    Route::put('/tuser/{id}', [ControllerUser::class, 'user']);

    Route::post('/subject', [ControllerSubject::class, 'store']);
    Route::put('/subject/{id}', [ControllerSubject::class, 'update']);
    Route::delete('/subject/{id}', [ControllerSubject::class, 'delete']);

    Route::get('/user/{id}/formular', [ControllerUser::class, 'formulars']);
    Route::get('/file/user/{id}', [ControllerFile::class, 'getByUser']);
});