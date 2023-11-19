<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmissionApplicationController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\ZoomController;
use Illuminate\Support\Facades\Auth;


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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('/email/verify/{id}/{otp}', [AuthController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resend'])
    ->name('verification.resend');


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

    // user-info

    Route::post('user-info/store', [UserInfoController::class, 'storeInfo'])->middleware('auth:sanctum');
    Route::get('user-info/retrieve', [UserInfoController::class, 'retrieveInfo'])->middleware('auth:sanctum');
    Route::get('user-info/remove-profile', [UserInfoController::class, 'removeImage'])->middleware('auth:sanctum');


    //user-Doc

    Route::post('user-doc/upload', [UserDocumentController::class, 'uploadDoc'])->middleware('auth:sanctum');
    Route::get('user-doc/retrieve', [UserDocumentController::class, 'retrieveDoc'])->middleware('auth:sanctum');
    Route::delete('user-doc/{id}', [UserDocumentController::class, 'DeleteDoc'])->middleware('auth:sanctum');

    //user-appointments

    Route::post('user-appoint/requestapp', [AppointmentController::class, 'requestAppointment'])->middleware('auth:sanctum');
    Route::get('user-appoint/viewapp', [AppointmentController::class, 'viewAppointment'])->middleware('auth:sanctum');
    Route::delete('user-appoint/{id}', [AppointmentController::class, 'cancelAppointment'])->middleware('auth:sanctum');
    Route::get('user/appointments/today', [AppointmentController::class, 'getTodayAppointments'])->middleware('auth:sanctum');

    // display-offer-letter
    Route::get('user/get-admission-details/{id}', [AdmissionApplicationController::class, 'getStudentAdmissionDetails']);
    Route::get('user/process-admission-doc/{id}', [AdmissionApplicationController::class, 'getProcessAdmissionDoc']);

    Route::get('user/display-offer-letter/{applicationId}', [AdmissionApplicationController::class, 'displayOfferLetter']);

    // application chat

    Route::get('/get-messages', [ChatController::class, 'getMessages']);
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/chat-history', [ChatController::class, 'chatHistory']);

    // notifcation get user
    Route::get('user/notication/', [UserInfoController::class, 'notication']);
    Route::post('user/read-notifaction', [UserInfoController::class, 'readNotifaction']);

    

});


// Admin-only routes
Route::group(['middleware' => ['auth:sanctum', 'Admin']], function () {

    Route::get('admin/get-messages', [ChatController::class, 'getMessages']);
    Route::post('admin/send-message', [ChatController::class, 'sendMessage']);
    Route::get('admin/recent-chat', [ChatController::class, 'recentChat']);
    Route::get('admin/chat-history', [ChatController::class, 'chatHistory']);
    Route::get('admin/chat/{chatId}/messages', [ChatController::class,'chatMessages']);




    Route::get('admin/student-list', [AdminController::class, 'retrieveStudentlist']);
    Route::get('admin/all-student-list', [AdminController::class, 'allretrieveStudentlist']);

    Route::get('admin/student-deatils/{id}', [AdminController::class, 'retrieveStudentdetails']);
    Route::post('admin/student-search', [AdminController::class, 'searchStudents']);
    // Appointment
    Route::get('admin/appointment-list', [AdminController::class, 'retrieveAppointments']);
    Route::get('admin/all-appointment-list', [AdminController::class, 'allRetrieveAppointments']);
    Route::get('admin/single-appointment/{appointment_id}', [AdminController::class, 'singleRetrieveAppointments']);



    Route::post('admin/appointment-search', [AdminController::class, 'searchappointment']);
    Route::post('admin/appointment-create', [AdminController::class, 'createAppointment']);
    Route::post('admin/appointment-edit/{appointment_id}', [AdminController::class, 'editAppointment']);
    Route::post('admin/appointment-update/{appointment_id}', [AdminController::class, 'updateAppointment']);
    Route::delete('admin/appointment-cancel/{id}', [AdminController::class, 'cancelAppointment']);
    Route::post('admin/appointment-comfirm/{appointment_id}', [AdminController::class, 'comfirmAppointment']);

    // admin_Student_creation 
    Route::post('admin/create-user', [AuthController::class, 'register']);
    Route::post('admin/store-info/{user_id}', [UserInfoController::class, 'adminStoreInfo']);
    Route::delete('admin/delete/{user_id}', [UserInfoController::class, 'deleteuser']);


    // AdmissionApplication
    Route::post('admin/apply-for-admission', [AdmissionApplicationController::class, 'applyForAdmission']);
    Route::post('admin/update-admission', [AdmissionApplicationController::class, 'updateApplyForAdmission']);
    Route::delete('admin/deleteAppication/{Application_no}', [AdmissionApplicationController::class, 'deleteAppication']);

    Route::get('admin/get-admission-details', [AdmissionApplicationController::class, 'getAdmissionDetails']);
    Route::post('admin/process-admission/{applicationId}', [AdmissionApplicationController::class, 'processAdmission']);
    Route::post('admin/process-admission-related-doc/{applicationId}', [AdmissionApplicationController::class, 'processAdmissionRelatedDoc']);
    Route::get('admin/display-offer-letter/{applicationId}', [AdmissionApplicationController::class, 'displayOfferLetter']);

    //zoom meeting
    Route::get('create-meeting/{appointment_id}', [ZoomController::class, 'createZoomMeeting']);
    Route::get('lanchMeeting/{appointment_id}', [ZoomController::class, 'lanchZoomMeeting']);

    // progress report bar
    Route::get('admin/progress-bar', [AdminController::class, 'progressBar']);
});
