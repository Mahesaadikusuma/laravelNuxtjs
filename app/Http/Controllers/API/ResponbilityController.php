<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateresponsibilityRequest;

use App\Http\Requests\UpdateresponsibilityRequest;
use App\Http\Controllers\Helpers\ResponseFormatter;


class ResponbilityController extends Controller
{

    // List Responsibility by Company
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $responsibilityQuery = responsibility::query();

        // Get Data Id
        if ($id) {
            // $responsibility = responsibility::whereHas('users', function($query) {
            //     $query->where('user_id', Auth::id());
            // })->with(['users'])->find($id);
                $responsibility = $responsibilityQuery->find($id);


            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'responsibility found');
            }

            return ResponseFormatter::error('responsibility Not Found', 404);
        }


        // Get multiple data
        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);


        if ($responsibilities) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'responsibility found'
        );
    }


    public function create(CreateResponsibilityRequest $request)
    {
        try {
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id
            ]);

            if (!$responsibility) {
                throw new Exception("Responsibility Not Found", 1);
            }

            return ResponseFormatter::success($responsibility, 'Created Responsibility', 200);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    

    // public function update(UpdateResponsibilityRequest $request, $id)
    // {
    //     try {
    //         $responsibility = Responsibility::findOrFail($id);

    //         if (!$responsibility) {
    //             throw new Exception("responsibility Not Found", 1);
                
    //         }
            
    //         $responsibility->update([
    //         'name' => $request->name,
    //         'company_id' => $request->company_id
    //     ]);

    //     // load company at responsibility
    //     $responsibility->load('company');

    //     return ResponseFormatter::success($responsibility, 'responsibility Updated', 200);

    //     } catch (Exception $e) {
    //         return ResponseFormatter::error($e->getMessage(),500);
    //     }
    // }

    public function destroy($id) 
    {
        try {

            // GET responsibility
            $responsibility = responsibility::find($id);


            // TODO: CHECK IF responsibility IS OWNED BY USER

            // CHECK IF USERS EXISTS
            if (!$responsibility) {
                throw new Exception('responsibility Not Found');
            }

            $responsibility->delete();

            return ResponseFormatter::success('responsibility Deleted', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

}
