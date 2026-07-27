<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Division;
use App\Models\Department;
use App\Models\InternshipType;
use App\Models\EducationLevel;
use App\Models\University;

class MasterDataController extends Controller
{
    public function index()
    {
        $divisions = Division::with('departments')->orderBy('name')->get();
        $internshipTypes = InternshipType::orderBy('name')->get();
        $educationLevels = EducationLevel::orderBy('name')->get();
        $universities = University::orderBy('name')->get();
        return view('admin.master-data', compact(
            'divisions', 'internshipTypes', 'educationLevels',
            'universities'
        ));
    }

    public function store(Request $request, $type)
    {
        $request->validate(['name' => 'required|string|max:255']);

        switch ($type) {
            case 'division':
                Division::create(['name' => $request->name]);
                break;
            case 'department':
                $request->validate(['division_id' => 'required|exists:divisions,id']);
                Department::create(['name' => $request->name, 'division_id' => $request->division_id]);
                break;
            case 'internship-type':
                InternshipType::create(['name' => $request->name]);
                break;
            case 'education-level':
                EducationLevel::create(['name' => $request->name]);
                break;
            case 'university':
                University::create(['name' => $request->name]);
                break;

            default:
                return back()->with('error', 'Tipe data tidak valid.');
        }

        return back()->with('success', 'Data berhasil ditambahkan.');
    }

    public function destroy($type, $id)
    {
        switch ($type) {
            case 'division':
                Division::findOrFail($id)->delete();
                break;
            case 'department':
                Department::findOrFail($id)->delete();
                break;
            case 'internship-type':
                InternshipType::findOrFail($id)->delete();
                break;
            case 'education-level':
                EducationLevel::findOrFail($id)->delete();
                break;
            case 'university':
                University::findOrFail($id)->delete();
                break;

            default:
                return back()->with('error', 'Tipe data tidak valid.');
        }

        return back()->with('success', 'Data berhasil dihapus.');
    }
}
