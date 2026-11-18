<?php
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/courses');
Route::resource('courses', CourseController::class)->except(['create', 'edit']);
Route::resource('participants', ParticipantController::class)->except(['create', 'show']);
Route::post('courses/{course}/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
Route::patch('enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
