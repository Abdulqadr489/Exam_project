<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exams\ExamController;
use App\Http\Controllers\Exams\ExamSectionController;
use App\Http\Controllers\Exams\ExamTypeController;
use App\Http\Controllers\Questions\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::apiResource('exam_types', ExamTypeController::class);
    Route::post('/exam_type/restore/{id}', [ExamTypeController::class, 'restore']);

    Route::apiResource('exams', ExamController::class);
    Route::post('/exams/restore/{id}', [ExamController::class, 'restore']);

    Route::apiResource('exam_sections', ExamSectionController::class);
    Route::post('/exam_sections/restore/{id}', [ExamSectionController::class, 'restore']);

    Route::apiResource('exam-sections.questions', QuestionController::class);
    // Optional: nested restore route
    Route::post('/exam-sections/{exam_section}/questions/restore/{id}', [QuestionController::class, 'restore']);

Route::post('/logout', [AuthController::class, 'logout']);
//});
