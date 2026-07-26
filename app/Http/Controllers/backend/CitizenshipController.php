<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Citizenship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitizenshipController extends Controller
{
    public function ShowCitizenships()
    {
        $citizenships = Citizenship::latest()->get();

        return view('backend.citizenships.citizenship_show', compact('citizenships'));
    }

    public function StoreCitizenship(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'citizenship_name' => 'required|string|max:255|unique:citizenships,citizenship_name',
        ], [
            'citizenship_name.required' => 'กรุณากรอกชื่อสัญชาติ',
            'citizenship_name.unique'   => 'ชื่อสัญชาตินี้มีอยู่แล้วในระบบ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Citizenship::create([
            'citizenship_name' => $request->citizenship_name,
        ]);

        return redirect()->back()->with([
            'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
    }

    public function EditCitizenship($id)
    {
        $citizenship = Citizenship::findOrFail($id);

        return response()->json($citizenship);
    }

    public function UpdateCitizenship(Request $request)
    {
        $citizenship_id = $request->citizenship_id;

        $validator = Validator::make($request->all(), [
            'citizenship_id'   => 'required|exists:citizenships,id',
            'citizenship_name' => 'required|string|max:255|unique:citizenships,citizenship_name,' . $citizenship_id,
        ], [
            'citizenship_id.required'   => 'ไม่พบรหัสข้อมูลที่ต้องการแก้ไข',
            'citizenship_id.exists'     => 'ไม่พบข้อมูลสัญชาตินี้ในระบบ',
            'citizenship_name.required' => 'กรุณากรอกชื่อสัญชาติ',
            'citizenship_name.unique'   => 'ชื่อสัญชาตินี้มีอยู่แล้วในระบบ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Citizenship::findOrFail($citizenship_id)->update([
            'citizenship_name' => $request->citizenship_name,
        ]);

        return redirect()->back()->with([
            'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteCitizenship($id)
    {
        $citizenship = Citizenship::findOrFail($id);
        $citizenship->delete();

        return redirect()->back()->with([
            'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
    }
}