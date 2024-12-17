<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;

class EmployeeControllers extends Controller
{
    public function AddEmployeeRecord(Request $request){

        //validate the request
        $validator = Validator::make($request->all(), [
            'employee_id'      => [
                'required',
                Rule::unique('employee_records', 'EMPLOYEEID'), // Unique employee_id
            ],
            'first_name'       => 'required|string',
            'last_name'        => 'required|string',
            'middle_name'      => 'required|string',
            'contact_number'   => [
                'required',
                'regex:/^09[0-9]{9}$/', // Philippine phone format (11 digits, starts with 09)
                Rule::unique('employee_records', 'CONTACTNUMBER'), // Unique contact_number
            ],
            'address'          => 'required|string',
            'email'            => [
                'required',
                'email', // Valid email format
                Rule::unique('employee_records', 'EMAIL'), // Unique email
            ],
            'sss'              => 'required|numeric',
            'tin'              => 'required|numeric',
            'philhealth'       => 'required|numeric',
            'pagibig'          => 'required|numeric',
            'date_hired'       => 'required|date',
            'position'         => 'required|string',
            'department'       => 'required|string',
            'approver'         => 'required|string',
            'approver_id'      => 'required|string',
            'image_link'       => 'required|string',
            'employee_status'  => 'required|string',
            'display_record'   => 'required|string',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
            Log::error('Validation error: ' . json_encode($validator->errors()));
        }
        DB::beginTransaction();
        $employee = new EmployeeRecord;
        $employee->EMPLOYEEID = $request->employee_id;
        $employee->FIRSTNAME= $request->first_name;
        $employee->LASTNAME= $request->last_name;
        $employee->MIDDLENAME= $request->middle_name;
        $employee->CONTACTNUMBER= $request->contact_number;
        $employee->ADDRESS= $request->address;
        $employee->email = $request->email;
        $employee->SSS = $request->sss;
        $employee->TIN = $request->tin;
        $employee->PHILHEALTH = $request->philhealth;
        $employee->PAGIBIG = $request->pagibig;
        $employee->DATEHIRED = $request->date_hired;
        $employee->POSITION = $request->position;
        $employee->DEPARTMENT = $request->department;
        $employee->APPROVER = $request->approver;
        $employee->APPROVERID = $request->approver_id;
        $employee->IMAGELINK = $request->image_link;
        $employee->EMPLOYEESTATUS = $request->employee_status;
        $employee->DISPLAYRECORD = $request->display_record;
        $employee->save();

        $np = $request->employee_id;

        //hash the employee id
        $hashp = hash('sha256', $np);
        //add a log in credentials
        $login = new LogInCredentials;
        $login->EMPLOYEEID = $request->employee_id;
        $login->USERNAME = $request->email;
        $login->PASSWORD = $hashp;
        $login->USERTYPE = $request->role;
        $login->save();

        //get the leave  credits
        $leave = LeaveType::all();
        foreach($leave as $l){
            $leave_credits = new LeaveCredits;
            $leave_credits->EMPLOYEEID = $request->employee_id;
            $leave_credits->LEAVETYPE = $l->id;
            $leave_credits->LEAVEBALANCE= $l->CRED;
            $leave_credits->save();
        }
        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port       = env('MAIL_PORT');

            // Sender and recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($request->email, $request->first_name . ' ' . $request->last_name);
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to the company!';
            //generate an  email including the username and password of the employee
            $mail->Body = 'Hello ' . $request->first_name . ' ' . $request->last_name . ',<br><br>
            Welcome to the company! Here are your login credentials:<br><br>
            Username: ' . $request->email . '<br>
            Password: ' . $np . '<br><br>
            Please keep your login credentials confidential.<br><br>
            Thank you!<br><br>
            Best regards,<br>
            RCC Colab Solutions Inc.';


            if($mail->send()){
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee record added successfully'
                ], 201); // 201 Created
            }else{
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send email'
                ], 500); // 500 Internal Server Error
            }
        }Catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500); // 500 Internal Server Error
            Log::error('Error: ' . $e->getMessage());
            DB::rollBack();

        }
    }
    public function UpdateEmployeeRecord(Request $request){
        $id = $request->id;
        $validator = Validator::make($request->all(), [
            'employee_id' => [
            'required',
            Rule::unique('employee_records', 'EMPLOYEEID')->ignore($id, 'id'), // Ignore current record by ID
            ],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'required|string',
            'contact_number' => [
                'required',
                'regex:/^09[0-9]{9}$/', // Philippine phone format (11 digits, starts with 09)
                Rule::unique('employee_records', 'CONTACTNUMBER')->ignore($id, 'id'), // Ignore current record
            ],
            'address' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('employee_records', 'EMAIL')->ignore($id, 'id'), // Ignore current record
            ],
            'sss'              => 'required|numeric',
            'tin'              => 'required|numeric',
            'philhealth'       => 'required|numeric',
            'pagibig'          => 'required|numeric',
            'date_hired'       => 'required|date',
            'position'         => 'required|string',
            'department'       => 'required|string',
            'approver'         => 'required|string',
            'approver_id'      => 'required|string',
            'image_link'       => 'required|string',
            'employee_status'  => 'required|string',
            'display_record'   => 'required|string',
            'role'             => 'required|string',
            'id'               => 'required|numeric',   
            
        ]);
        //save the updated record
        $employee = EmployeeRecord::find($id);
        $employee->EMPLOYEEID = $request->employee_id;
        $employee->FIRSTNAME= $request->first_name;
        $employee->LASTNAME= $request->last_name;
        $employee->MIDDLENAME= $request->middle_name;
        $employee->CONTACTNUMBER= $request->contact_number;
        $employee->ADDRESS= $request->address;
        $employee->email = $request->email;
        $employee->SSS = $request->sss;
        $employee->TIN = $request->tin;
        $employee->PHILHEALTH = $request->philhealth;
        $employee->PAGIBIG = $request->pagibig;
        $employee->DATEHIRED = $request->date_hired;
        $employee->POSITION = $request->position;
        $employee->DEPARTMENT = $request->department;
        $employee->APPROVER = $request->approver;
        $employee->APPROVERID = $request->approver_id;
        $employee->IMAGELINK = $request->image_link;
        $employee->EMPLOYEESTATUS = $request->employee_status;
        $employee->DISPLAYRECORD = $request->display_record;
        $employee->save();

        //update the login credentials
        $login = LogInCredentials::where('EMPLOYEEID', $request->employee_id)->first();
        $login->EMPLOYEEID = $request->employee_id;
        $login->USERNAME = $request->email;
        $login->USERTYPE = $request->role;
        $np = $request->employee_id;
        //hash the employee id
        $hashp = hash('sha256', $np);
        $login->PASSWORD = $hashp;
        $login->save();
        $mail = new PHPMailer(true);
        try{
            //send an email to the employee
           
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port       = env('MAIL_PORT');

            // Sender and recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($request->email, $request->first_name . ' ' . $request->last_name);
            $mail->isHTML(true);
            $mail->Subject = 'Employee record updated';
            //generate an  email including the username and password of the employee
            $mail->Body = 'Hello ' . $request->first_name . ' ' . $request->last_name . ',<br><br>
            Your employee record has been updated. Here are your login credentials:<br><br>
            Username: ' . $request->email . '<br>
            Password: ' . $np . '<br><br>
            Please keep your login credentials confidential.<br><br>
            Thank you!<br><br>
            Best regards,<br>
            RCC Colab Solutions Inc.';

            if($mail->send()){
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee record updated successfully'
                ], 200); // 200 OK
            }else{
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send email'
                ], 500); // 500 Internal Server Error
            }

        }Catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500); // 500 Internal Server Error
            Log::error('Error: ' . $e->getMessage());
            DB::rollBack();

        }


        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
            Log::error('Validation error: ' . json_encode($validator->errors()));
        }
    }
    public function DeleteEmployeeRecord(Request $request){
        //begin the transaction
        DB::beginTransaction();
        $id = $request->id;
        $employee = EmployeeRecord::find($id);
        //get the employee EMPLOYEEID
        $empid = $employee->EMPLOYEEID;
        //get the email
        $email = $employee->EMAIL;
       
        //delete the leave credits
        $leave = LeaveCredits::where('EMPLOYEEID', $empid)->delete();
        //send an email to the employee
        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port       = env('MAIL_PORT');

            // Sender and recipient
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($email, $employee->FIRSTNAME . ' ' . $employee->LASTNAME);
            $mail->isHTML(true);
            $mail->Subject = 'Employee record deleted';
            //generate an  email including the username and password of the employee
            $mail->Body = 'Hello ' . $employee->FIRSTNAME . ' ' . $employee->LASTNAME . ',<br><br>
            Your employee record has been deleted. If you did not request this, please contact the HR department immediately.<br><br>
            Thank you!<br><br>
            Best regards,<br>
            RCC Colab Solutions Inc.';

            if($mail->send()){
                 //delete the employee record
                $employee->delete();
                //delete the login credentials
                $login = LogInCredentials::where('EMPLOYEEID', $empid)->first();
                $login->delete();
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee record deleted successfully'
                ], 200); // 200 OK
            }else{
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send email'
                ], 500); // 500 Internal Server Error
            }
        }Catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500); // 500 Internal Server Error
            Log::error('Error: ' . $e->getMessage());
            DB::rollBack();
        }
    }
    public function GetEmployeeRecord(){
        $employee = EmployeeRecord::all();
        return response()->json($employee);
    }
}
