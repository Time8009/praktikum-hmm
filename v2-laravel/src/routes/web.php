<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::get('/formular', [Controller::class, 'create'])->name('formular.create');
Route::post('/formular', [Controller::class, 'store'])->name('formular.store');

Route::get('/formular_viewer', [Controller::class, 'index'])->name('formular.index');
Route::post('/entries/delete', [Controller::class, 'destroy'])->name('formular.destroy');
Route::post('/entries/truncate', [Controller::class, 'truncate'])->name('formular.truncate');

Route::get('/formular/{id}/edit', [Controller::class, 'edit'])->name('formular.edit');
Route::put('/formular/{id}', [Controller::class, 'update'])->name('formular.update');