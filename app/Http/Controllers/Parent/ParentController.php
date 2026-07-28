<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\UpdateParentRequest;
use App\Models\ParentProfile;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index(Request $request, School $school)
    {
        $parents = User::role('Parent')
            ->where('school_id', session('active_school'))
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhereHas('parentProfile', function ($q) use ($request) {
                        $q->where('alt_phone', 'like', "%{$request->search}%");
                    });
            })
            ->with(['parentProfile', 'children.studentProfile.section.classLevel'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('parent.index', compact('parents'));
    }

    public function create()
    {
        return view('parent.create');
    }

    public function store(StoreParentRequest $request, School $school)
    {
        $parent = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => session('active_school'),
        ]);

        $parent->assignRole('Parent');

        ParentProfile::create([
            'user_id' => $parent->id,
            'alt_phone' => $request->alt_phone,
            'occupation' => $request->occupation,
            'address' => $request->address,
        ]);

        return redirect()->route('parents.show', $parent->id)
            ->with('success', 'Parent account created successfully! You can now link children.');
    }

    public function show(School $school, User $parent)
    {
        $parent->load([
            'parentProfile',
            'children.studentProfile.section.classLevel',
            'children.invoices' => function ($q) {
                $q->withSum('payments', 'amount');
            },
        ]);

        // Available students to link (exclude already linked to this parent)
        $linkedChildIds = $parent->children->pluck('id')->toArray();

        return view('parent.show', compact('parent', 'linkedChildIds'));
    }

    public function edit(School $school, User $parent)
    {
        $parent->load('parentProfile');

        $nameParts = explode(' ', trim($parent->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName  = $nameParts[1] ?? '';

        return view('parent.edit', compact('parent', 'firstName', 'lastName'));
    }

    public function update(UpdateParentRequest $request,School $school, User $parent)
    {
        //dd($request->validated(), $parent->id, $parent->parentProfile);
        $parent->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        $parent->parentProfile()->updateOrCreate(
            ['user_id' => $parent->id],
            [
                'alt_phone' => $request->alt_phone,
                'occupation' => $request->occupation,
                'address' => $request->address,
            ]
        );

        return redirect()->route('parents.show', $parent->id)
            ->with('success', 'Parent details updated successfully!');
    }

    // AJAX search for students to link
    public function searchStudents(Request $request, School $school)
    {
        $query = $request->get('q');

        $students = User::role('Student')
            ->where('school_id', session('active_school'))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhereHas('studentProfile', function ($q) use ($query) {
                        $q->where('admission_number', 'like', "%{$query}%");
                    });
            })
            ->with('studentProfile.section.classLevel')
            ->limit(10)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'admission_number' => $student->studentProfile->admission_number ?? 'N/A',
                    'class' => ($student->studentProfile->section->classLevel->name ?? '') .
                        ' - ' . ($student->studentProfile->section->name ?? ''),
                ];
            });

        return response()->json($students);
    }

    public function linkChild(Request $request, School $school, User $parent)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'relationship' => 'required|in:Father,Mother,Guardian',
        ]);

        // Prevent duplicate link
        if ($parent->children()->where('child_id', $request->student_id)->exists()) {
            return back()->with('error', 'This student is already linked to this parent.');
        }

        $parent->children()->attach($request->student_id, [
            'relationship' => $request->relationship,
        ]);

        return back()->with('success', 'Child linked successfully!');
    }

    public function destroy(School $school, User $parent)
    {
        $parent->children()->detach();
        $parent->parentProfile()->delete();
        $parent->delete();

        return redirect()->route('parents.index')
            ->with('success', 'Parent deleted successfully.');
    }

    public function unlinkChild(School $school, User $parent, User $student)
    {
        $parent->children()->detach($student->id);

        return back()->with('success', "{$student->name} has been unlinked from this parent.");
    }
}
