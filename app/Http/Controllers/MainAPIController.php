<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DepartmentList;
use App\Models\EmployeeRecord;
use App\Models\HolidayCalendar;
use App\Models\LeaveCredits;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LogInCredentials;
use App\Models\TimeSheet;


//use validation
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


//use the log
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Catch_;

class MainAPIController extends Controller
{
    public function dashboard(){

        $data = [
            'total_employees' => EmployeeRecord::count(),
        ];
        //log the data
        Log::info('Dashboard data: ' . json_encode($data));

        return response()->json($data);
    }

  
   
}
