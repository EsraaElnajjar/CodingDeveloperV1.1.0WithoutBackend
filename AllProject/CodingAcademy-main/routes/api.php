<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CustomerSawController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\GetController;
use App\Http\Controllers\TeachController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\JoinUsController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SawController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TrainingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class,'logout']);


Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('edit/{id}', [UserController::class, 'show']);
    Route::post('edit/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix'=> 'get','middleware'=>'auth:sanctum'], function () {
    // Route::get('/', [GetController::class, 'index']); 
    Route::post('/', [GetController::class, 'subscribe']); 
    Route::get('edit/{id}', [GetController::class, 'show']);
    Route::post('/edit/{id}', [GetController::class, 'updateSubscription']); 
    // Route::delete('/{id}', [GetController::class, 'destroy']);
    Route::post('/unsubscribe', [GetController::class, 'unsubscribe']); 
    Route::get('/user-courses/{user_id}', [GetController::class, 'getUserCourses']);
    Route::get('/course-users/{courses_id}', [GetController::class, 'getCourseUsers']); 
});

Route::group(['prefix'=> 'course','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [CourseController::class,'store']);
    Route::post('edit/{id}', [CourseController::class,'update']);
    Route::delete('/{id}', [CourseController::class,'destroy']); 
});
Route::get('course/', [CourseController::class,'index']);
Route::get('/course/edit/{id}', [CourseController::class,'show']);


Route::group(['prefix'=> 'teach','middleware'=>'auth:sanctum'], function () {
    Route::post('/add', [TeachController::class, 'addLecturerToCourse']);
    Route::post('/update/{id}', [TeachController::class, 'updateLecturerOrCourse']);
    Route::get('/course/{id}', [TeachController::class, 'getLecturersForCourse']);
    Route::delete('/remove/{id}', [TeachController::class, 'removeLecturerFromCourse']);
    Route::delete('/course/delete/{id}', [TeachController::class, 'deleteCourse']);
});

Route::group(['prefix'=> 'lecturer','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [LecturerController::class,'store']);
    Route::post('edit/{id}', [LecturerController::class,'update']);
    Route::delete('/{id}', [LecturerController::class,'destroy']); 
});
Route::get('lecturer/', [LecturerController::class,'index']);
Route::get('lecturer/edit/{id}', [LecturerController::class,'show']);


Route::group(['prefix'=> 'team','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [TeamController::class,'store']);
    Route::post('/edit/{id}', [TeamController::class,'update']);
    Route::delete('/{id}', [TeamController::class,'destroy']); 
});
Route::get('team/edit/{id}', [TeamController::class,'show']);
Route::get('team/', [TeamController::class,'index']);


Route::group(['prefix'=> 'image','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [ImageController::class, 'store']); 
    Route::post('edit/{id}', [ImageController::class, 'update']); 
    Route::delete('/{id}', [ImageController::class, 'destroy']); 
});
Route::get('image/edit/{id}', [ImageController::class,'show']);
Route::get('image/', [ImageController::class, 'index']); 


Route::group(['prefix'=> 'question','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [QuestionController::class, 'store']); 
    Route::post('edit/{id}', [QuestionController::class, 'update']); 
    Route::delete('/{id}', [QuestionController::class, 'destroy']); 
});
Route::get('question/edit/{id}', [QuestionController::class,'show']);
Route::get('question/', [QuestionController::class, 'index']); 


Route::group(['prefix'=> 'saws','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [SawController::class, 'store']); 
    Route::post('edit/{id}', [SawController::class, 'update']); 
    Route::delete('/{id}', [SawController::class, 'destroy']); 
});
Route::get('saws/edit/{id}', [SawController::class,'show']);
Route::get('saws/', [SawController::class, 'index']); 


Route::group(['prefix'=> 'CustomerSaws','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [CustomerSawController::class, 'store']); 
    Route::post('edit/{id}', [CustomerSawController::class, 'update']); 
    Route::delete('/{id}', [CustomerSawController::class, 'destroy']); 
});
Route::get('CustomerSaws/edit/{id}', [CustomerSawController::class,'show']);
Route::get('CustomerSaws/', [CustomerSawController::class, 'index']); 


Route::group(['prefix'=> 'training','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [TrainingController::class, 'store']); 
    Route::post('edit/{id}', [TrainingController::class, 'update']); 
    Route::delete('/{id}', [TrainingController::class, 'destroy']); 
});
Route::get('training/edit/{id}', [TrainingController::class,'show']);
Route::get('training/', [TrainingController::class, 'index']); 


Route::group(['prefix'=> 'services','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [ServiceController::class, 'store']); 
    Route::post('edit/{id}', [ServiceController::class, 'update']); 
    Route::delete('/{id}', [ServiceController::class, 'destroy']); 
});
Route::get('services/edit/{id}', [ServiceController::class,'show']);
Route::get('services/', [ServiceController::class, 'index']); 


Route::group(['prefix'=> 'JoinUs','middleware'=>'auth:sanctum'], function () {
    Route::post('/', [JoinUsController::class, 'store']); 
    Route::post('edit/{id}', [JoinUsController::class, 'update']); 
    Route::delete('/{id}', [JoinUsController::class, 'destroy']); 
});
Route::get('JoinUs/edit/{id}', [JoinUsController::class,'show']);
Route::get('JoinUs/', [JoinUsController::class, 'index']); 



Route::group(['prefix' => 'booking', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/', [BookingController::class, 'store']);
    Route::post('edit/{id}', [BookingController::class, 'update']);
    Route::delete('/{id}', [BookingController::class, 'destroy']);
});
Route::get('booking/', [BookingController::class, 'index']);
Route::get('booking/edit/{id}', [BookingController::class, 'show']);


Route::apiResource('ContactUs', ContactController::class);




