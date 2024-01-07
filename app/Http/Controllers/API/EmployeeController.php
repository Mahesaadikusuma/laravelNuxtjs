<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\http\Controllers\Helpers\ResponseFormatter;

class EmployeeController extends Controller
{

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        // $name = $request->input('gender');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');

        $limit = $request->input('limit', 10);
        
        $EmployeesQuery = Employee::query();

        // Get Data Id
        if ($id) {
            // $Employee = Employee::whereHas('users', function($query) {
            //     $query->where('user_id', Auth::id());
            // })->with(['users'])->find($id);
                $Employees = $EmployeesQuery->with('team','role')->find($id);


            if ($Employees) {
                return ResponseFormatter::success($Employees, 'Employee found');
            }

            return ResponseFormatter::error('Employee Not Found', 404);
        }


        // Get multiple data
        // $companies = Employee::with('users'); 
        // berdasarkan id user Employee

        // $companies = Employee::with(['users'])->whereHas('users', function($query) {
        //     $query->where('user_id', Auth::id());
        // });
        
        $Employees = $EmployeesQuery;


        if ($name) {
            $EmployeesQuery->where('name', 'like', '%' . $name . '%');
        }

        if ($email) {
            $EmployeesQuery->where('email', $email);
        }

        if ($age) {
            $EmployeesQuery->where('age', $age);
        }

        if ($phone) {
            $EmployeesQuery->where('phone', 'like', '%' . $phone . '%');
        }

        if ($team_id) {
            $EmployeesQuery->where('team_id', $team_id);
        }

        if ($role_id) {
            $EmployeesQuery->where('role_id', $role_id);
        }

        return ResponseFormatter::success(
            $EmployeesQuery->paginate($limit),
            'Employees found'
        );
    }



    public function create(CreateEmployeeRequest $request) 
    {

        try {
            
        // Upload Foto 
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/photos');
        }


        // create Employee

        $Employees = Employee::create([

            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'age' => $request->age,
            'phone' => $request->phone,
            'photo' => $path,
            'team_id' => $request->team_id,
            'role_id' => $request->role_id,
            
        ]);


        if (!$Employees) {
            throw new Exception("Employees Not Found", 1);
        }

        return ResponseFormatter::success($Employees, 'Employee Created', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $Employees = Employee::findOrFail($id);

            if (!$Employees) {
                throw new Exception("Team Not Found", 1);
                
            }

            // upload File icon
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            $Employees->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $Employees->photo,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
        ]);

        

        return ResponseFormatter::success($Employees, 'Employees Updated', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(),500);
        }
    }

    public function destroy($id) 
    {
        try {

            // GET TEAMS
            $Employees = Employee::find($id);

            // TODO: CHECK IF TEAM IS OWNED BY USER

            // CHECK IF USERS EXISTS
            if (!$Employees) {
                throw new Exception('Employee Not Found');
            }

            $Employees->delete();

            return ResponseFormatter::success('Employee Deleted', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

}
