<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/vacancies');

Route::patch('vacancies/{vacancy}/toggle-pause', [VacancyController::class, 'togglePause'])
    ->name('vacancies.toggle-pause');
Route::resource('vacancies', VacancyController::class);

Route::resource('candidates', CandidateController::class);

Route::post('candidates/{candidate}/applications', [ApplicationController::class, 'store'])
    ->name('candidates.applications.store');
Route::delete('candidates/{candidate}/applications/{vacancy}', [ApplicationController::class, 'destroy'])
    ->name('candidates.applications.destroy');
