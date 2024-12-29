<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Employee List';


        $employees = Employee::all();

        return view('employee.index', [
            'pageTitle' => $pageTitle,
            'employees' => $employees
        ]);
    }


    public function create()
    {
        $pageTitle = 'Create Employee';


        $positions = Position::all();

        return view('employee.create', compact('pageTitle', 'positions'));
    }


    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka',
            'email.unique' => 'Email ini sudah digunakan. Harap gunakan email lain.',
            'cv' => 'File CV Kosong'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:employees,email',
            'age' => 'required|numeric',
            'cv' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $file = $request->file('cv');
        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();


            $file->store('public/files');
        }


        $employee = new Employee;
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;

        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();

        return redirect()->route('employees.index');
    }


    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';


        $employee = Employee::find($id);

        return view('employee.show', compact('pageTitle', 'employee'));
    }


    public function edit(string $id)
    {
        $pageTitle = 'Edit Employee';


        $positions = Position::all();
        $employee = Employee::find($id);

        return view('employee.edit', compact(
            'pageTitle',
            'positions',
            'employee'
        ));
    }


    public function update(Request $request, string $id)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'required|email|unique:employees,email,' . $id,
            'numeric' => 'Isi :attribute dengan angka',
            'cv.required' => 'File CV harus diunggah jika belum ada.',
            'cv.mimes' => 'File harus dalam format: pdf, doc, docx.',
            'cv.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
            'position' => 'required|exists:positions,id',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::find($id);

        if (!$employee) {
            return redirect()->back()->with('error', 'Data employee tidak ditemukan.');
        }


        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;


        if ($request->hasFile('cv')) {

            if ($employee->encrypted_filename) {
                Storage::delete('public/files/' . $employee->encrypted_filename);
            }


            $path = $request->file('cv')->store('public/files');
            $employee->original_filename = $request->file('cv')->getClientOriginalName();
            $employee->encrypted_filename = basename($path);
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Data employee berhasil diperbarui.');
    }


    public function destroy(string $id)
    {

        $employee = Employee::find($id);


        if (!$employee) {
            return redirect()->route('employees.index')->with('error', 'Data employee tidak ditemukan.');
        }

        try {

            if ($employee->encrypted_filename) {
                $filePath = 'public/files/' . $employee->encrypted_filename;


                if (\Storage::exists($filePath)) {
                    \Storage::delete($filePath);
                }
            }


            $employee->delete();

            return redirect()->route('employees.index')->with('success', 'Data employee dan file berhasil dihapus.');
        } catch (\Exception $e) {

            return redirect()->route('employees.index')->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }


    public function downloadFile($employeeId)
    {
        $employee = Employee::find($employeeId);
        $encryptedFilename = 'public/files/' . $employee->encrypted_filename;
        $downloadFilename = Str::lower($employee->firstname . '_' . $employee->lastname . '_cv.pdf');
        if (Storage::exists($encryptedFilename)) {
            return Storage::download($encryptedFilename, $downloadFilename);
        }
    }

    public function removeFile($employeeId)
    {
        $employee = Employee::find($employeeId);

        if ($employee) {
            \Log::info('Data employee sebelum update:', $employee->toArray());

            if ($employee->encrypted_filename) {
                $encryptedFilename = 'public/files/' . $employee->encrypted_filename;

                if (\Storage::exists($encryptedFilename)) {
                    \Storage::delete($encryptedFilename);
                }

                $employee->update([
                    'original_filename' => null,
                    'encrypted_filename' => null
                ]);

                \Log::info('Data employee setelah update:', $employee->fresh()->toArray());
                return redirect()->back()->with('success', 'File CV berhasil dihapus');
            }

            return redirect()->back()->with('error', 'Tidak ada file CV yang tersimpan');
        }

        return redirect()->back()->with('error', 'Data employee tidak ditemukan');
    }
}
