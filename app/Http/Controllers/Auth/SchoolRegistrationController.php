<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterSchoolRequest;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolRegistrationController extends Controller
{
    public function showForm()
    {
        return view('auth.register-school');
    }

    public function store(RegisterSchoolRequest $request)
    {
        DB::beginTransaction();

        try {
            $school = School::create([
                'name' => $request->school_name,
                'email' => $request->school_email,
                'phone_number' => $request->school_phone,
                'address' => $request->school_address,
                'principal_name' => $request->principal_name,
                'slug' => Str::slug($request->school_name),
                'is_active' => false,       // inactive until payment
                'approval_status' => 'pending',   // pending until payment
            ]);

            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'school_id' => $school->id,
            ]);

            $admin->assignRole('SchoolAdmin');

            DB::commit();

            // Auto login after registration
            Auth::login($admin);

            // Redirect to thank-you screen
            return redirect()->route('school.register')->with('registered', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}
