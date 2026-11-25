<?php

use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/vacancies');

Route::patch('vacancies/{vacancy}/toggle-pause', [VacancyController::class, 'togglePause'])
    ->name('vacancies.toggle-pause');
Route::resource('vacancies', VacancyController::class);
