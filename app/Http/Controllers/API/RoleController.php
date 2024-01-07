<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateroleRequest;
use App\Http\Requests\UpdateroleRequest;


class RoleController extends Controller
{

    // List Role by Company
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('id');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);


        $roleQuery = role::query();

        // Get Data Id
        if ($id) {
            // $role = role::whereHas('users', function($query) {
            //     $query->where('user_id', Auth::id());
            // })->with(['users'])->find($id);
                $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'role found');
            }

            return ResponseFormatter::error('role Not Found', 404);
        }


        // Get multiple data
        // $companies = role::with('users'); 
        // berdasarkan id user role

        // $companies = role::with(['users'])->whereHas('users', function($query) {
        //     $query->where('user_id', Auth::id());
        // });
        
        $role = $roleQuery->where('company_id', $request->company_id);


        if ($name) {
            $role->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $role->with('responsibilities');
        }

        return ResponseFormatter::success(
            $role->paginate($limit),
            'role found'
        );
    }


    public function create(CreateRoleRequest $request)
    {
        try {
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);

            if (!$role) {
                throw new Exception("Role Not Found", 1);
            }

            return ResponseFormatter::success($role, 'Created Role', 200);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            if (!$role) {
                throw new Exception("role Not Found", 1);
                
            }
            
            $role->update([
            'name' => $request->name,
            'company_id' => $request->company_id
            ]);

        // load company at role
        $role->load('company');

        return ResponseFormatter::success($role, 'role Updated', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(),500);
        }
    }

    public function destroy($id) 
    {
        try {

            // GET role
            $role = role::find($id);


            // TODO: CHECK IF role IS OWNED BY USER

            // CHECK IF USERS EXISTS
            if (!$role) {
                throw new Exception('role Not Found');
            }

            $role->delete();

            return ResponseFormatter::success('role Deleted', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

}
