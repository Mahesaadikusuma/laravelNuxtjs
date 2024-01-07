<?php

namespace App\Http\Controllers\API;

use Exception;
use app\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Controllers\Helpers\ResponseFormatter;

class CompanyController extends Controller
{
    // List Company by User
    
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('id');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        });

        // Get Data Id
        if ($id) {
            // $company = Company::whereHas('users', function($query) {
            //     $query->where('user_id', Auth::id());
            // })->with(['users'])->find($id);
                $company = $companyQuery->find($id);


            if ($company) {
                return ResponseFormatter::success($company, 'company found');
            }

            return ResponseFormatter::error('Company Not Found', 404);
        }


        // Get multiple data
        // $companies = Company::with('users'); 
        // berdasarkan id user company

        // $companies = Company::with(['users'])->whereHas('users', function($query) {
        //     $query->where('user_id', Auth::id());
        // });
        
        $companies = $companyQuery;


        if ($companies) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {

            // Upload Foto
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // create company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            if (!$company) {
                throw new Exception("Company Not Created", 1);
            }

            // attach company User
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // load user at company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id) 
    {
        // return $id;
        // dd($request->all()); 
        try {
            $company = Company::find($id);

            if (!$company) {
                throw new Exception("Company Not Created");
            }

            // Upload File
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo,
            ]);

            return ResponseFormatter::success($company, 'Company Update', 200);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
