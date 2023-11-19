<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Appointment;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\UserInfo;
use App\Repositories\AdminRepository;
use App\Repositories\UserDocRepository;
use App\Repositories\AppointmentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;





final class AdminService
{

    protected $AdminRepository;
    protected $UserDocRepository;
    protected $AppointmentRepository;

    public function __construct(AdminRepository $AdminRepository, UserDocRepository $UserDocRepository, AppointmentRepository $AppointmentRepository)
    {
        $this->AdminRepository = $AdminRepository;
        $this->UserDocRepository = $UserDocRepository;
        $this->AppointmentRepository = $AppointmentRepository;
    }


    public function retrieveStudentList($filters)
    {
        $perPage = 10;
        $query = User::query();
    
        // Apply search filters
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'LIKE', "%$searchTerm%")
                    ->orWhere('name', 'LIKE', "%$searchTerm%")
                    ->orWhere('email', 'LIKE', "%$searchTerm%");
            });
        }
    
        // Apply role filter
        $query->where(function ($q) {
            $q->whereNull('isAdmin')
                ->orWhere('isAdmin', '!=', '1');
        });
    
        $data = $query->latest()->paginate($perPage);
    
        return $data;
    }
    
    public function allretrieveStudentlist()
    {
        $allStudents = User::whereNull('isAdmin')
            ->orWhere('isAdmin', '!=', '1')
            ->get(['id', 'name','email']);

        return  $allStudents;
    }

    public function retrieveStudentData($id)
    {
        $user_id = $id;
        $Result = $this->AdminRepository->userdetails($user_id);
        return $Result;
    }
    public function searchStudents($searchField)
    {
        $Result = $this->AdminRepository->searchstudent($searchField);
        return $Result;
    }

    public function retrieveAppointments($filters)
    {

        
        $latestAppointments = $this->AdminRepository->appointments($filters);
        return $latestAppointments;
    }
    public function allRetrieveAppointments()
    {
        $latestAppointments = $this->AdminRepository->allRetrieveAppointments();
        return $latestAppointments;
    }
    public function singleRetrieveAppointment($id)
    {
        $latestAppointments = $this->AdminRepository->singleRetrieveAppointments($id);
        return $latestAppointments;
    }
    function searchappointment($searchfiled)
    {
        $ResAppointments = $this->AdminRepository->searchappointment($searchfiled);
        return $ResAppointments;
    }
    public function createAppointment($appontdeatils, $id)
    {
        $appontdeatils['user_id'] = $id;
        $userAppoint = Appointment::create(['user_id' => $id] + $appontdeatils);
        return $userAppoint;
    }

    public function editAppointment($appointment_id)
    {
        $userAppoint = Appointment::where(['id' => $appointment_id])->get();
        return $userAppoint;
    }
    public function updateAppointment($appontdeatils, $appointment_id)
    {
        $updateAppoint = Appointment::where('id', $appointment_id)->update($appontdeatils);
        return $updateAppoint;
    }
    public function cancelAppointment($id)
    {
        $status = $this->AppointmentRepository->cancelAppiont($id);
        return $status;
    }
    public function comfirmAppointment($appointment_id)
    {
        $status = $this->AppointmentRepository->comfirmAppointment($appointment_id);
        return $status;
    }
    public function progressBar()
    {
        $currentDate = Carbon::now();
        $currentMonthStartDate1 = $currentDate->copy()->startOfMonth();
        $currentMonthEndDate1 = $currentDate->copy()->endOfMonth();
        $previousMonthStartDate1 = $currentDate->subMonth()->startOfMonth();
        $previousMonthEndDate1 = $currentDate->subMonth()->endOfMonth();

        $currentMonthStartDate = $currentMonthStartDate1->toDateTimeString();
        $currentMonthEndDate = $currentMonthEndDate1->toDateTimeString();
        $previousMonthStartDate = $previousMonthStartDate1->toDateTimeString();
        $previousMonthEndDate = $previousMonthEndDate1->toDateTimeString();

        $newUsersThisMonth = User::whereBetween('created_at', [$currentMonthStartDate, $currentMonthEndDate])->count();
        $newUsersPreviousMonth = User::whereBetween('created_at', [$previousMonthStartDate, $previousMonthEndDate])->count();
        $newAppointmentThisMonth = Appointment::whereBetween('created_at', [$currentMonthStartDate, $currentMonthEndDate])->count();
        $PreviousMonthAppointment = Appointment::whereBetween('created_at', [$previousMonthStartDate, $previousMonthEndDate])->count();

        $totalUsers = User::count();
        $totalAppointment = Appointment::count();
        $userPercentageChange = ($newUsersThisMonth - $newUsersPreviousMonth) / ($newUsersThisMonth ?: 1) * 100;
        $appointPercentageChange = ($newAppointmentThisMonth - $PreviousMonthAppointment) / ($PreviousMonthAppointment ?: 1) * 100;



        return [
            'new_users_this_month' => $newUsersThisMonth,
            'new_users_previous_month' => $newUsersPreviousMonth,
            'user_percentage_change' => $userPercentageChange,

            'new_appoinment_this_month' => $newAppointmentThisMonth,
            'new_appoinment_previous_month' => $PreviousMonthAppointment,
            'appoinment_percentage_change' => $appointPercentageChange,
        ];
    }
}
