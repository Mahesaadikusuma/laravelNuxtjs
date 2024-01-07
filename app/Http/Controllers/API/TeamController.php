<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamsRequest;
use App\Http\Requests\UpdateTeamsRequest;
use App\http\Controllers\Helpers\ResponseFormatter;

class TeamController extends Controller
{

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $TeamQuery = Team::query();

        // Get Data Id
        if ($id) {
            // $Team = Team::whereHas('users', function($query) {
            //     $query->where('user_id', Auth::id());
            // })->with(['users'])->find($id);
                $Team = $TeamQuery->find($id);


            if ($Team) {
                return ResponseFormatter::success($Team, 'Team found');
            }

            return ResponseFormatter::error('Team Not Found', 404);
        }


        // Get multiple data
        // $companies = Team::with('users'); 
        // berdasarkan id user Team

        // $companies = Team::with(['users'])->whereHas('users', function($query) {
        //     $query->where('user_id', Auth::id());
        // });
        
        $Teams = $TeamQuery->where('company_id', $request->company_id);


        if ($name) {
            $Teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $Teams->paginate($limit),
            'Teams found'
        );
    }



    public function create(CreateTeamsRequest $request) 
    {

        try {
            
        // Upload Foto 
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('public/icon');
        }


        // create Team

        $Teams = Team::create([
            'name' => $request->name,
            'icon' => $path,
            'company_id' => $request->company_id
        ]);


        if (!$Teams) {
            throw new Exception("Teams Not Found", 1);
        }

        return ResponseFormatter::success($Teams, 'Team Created', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }

    public function update(UpdateTeamsRequest $request, $id)
    {
        try {
            $Teams = Team::findOrFail($id);

            if (!$Teams) {
                throw new Exception("Team Not Found", 1);
                
            }

            // upload File icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icon');
            }

            $Teams->update([
            'name' => $request->name,
            'icon' => isset($path) ? $path : $Teams->icon,
            'company_id' => $request->company_id
        ]);

        // load company at teams
        $Teams->load('company');

        return ResponseFormatter::success($Teams, 'Team Updated', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(),500);
        }
    }

    public function destroy($id) 
    {
        try {

            // GET TEAMS
            $Teams = Team::find($id);


            // TODO: CHECK IF TEAM IS OWNED BY USER

            // CHECK IF USERS EXISTS
            if (!$Teams) {
                throw new Exception('Team Not Found');
            }

            $Teams->delete();

            return ResponseFormatter::success('Team Deleted', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

}
