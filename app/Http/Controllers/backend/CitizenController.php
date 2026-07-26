<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // ✅ ต้องมีบรรทัดนี้
use App\Models\Citizen;

class CitizenController extends Controller
{
    public function ShowCitizens()
{
    $citizens = Citizen::latest()->get();
    return view('backend.citizens.citizen_show', compact('citizens'));
}
    public function StoreCitizen(Request $request){
        // ตรวจสอบชื่อห้ามซ้ำ
        $validator = Validator::make($request->all(), [
            'citizen_name' => 'required|unique:citizens,citizen_name'
        ], [
            'citizen_name.required' => 'กรุณากรอกชื่อผู้อยู่อาศัย',
            'citizen_name.unique' => 'ชื่อผู้อยู่อาศัยนี้มีอยู่แล้วในระบบ'
        ]);

        // ถ้ามี error → กลับไปพร้อม error message
        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }
        // ถ้าไม่ซ้ำ → บันทึกข้อมูล
        Citizen::create([
            'citizen_name' => $request->citizen_name
        ]);

        $notification = array(
            'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
        }
        //End Method

        public function EditCitizen($id)
        {
            $citizen = Citizen::find($id);
            return response()->json($citizen);
        }
        //End Method

         public function UpdateCitizen(Request $request)
    {
        $citizen_id = $request->citizen_id; // ใช้ชื่อให้ตรงกับ hidden input

        $validator = Validator::make($request->all(), [
            'citizen_name' => 'required|unique:citizens,citizen_name,' . $citizen_id,
        ], [
            'citizen_name.required' => 'กรุณากรอกชื่อผู้อยู่อาศัย',
            'citizen_name.unique' => 'ชื่อผู้อยู่อาศัยนี้มีอยู่แล้วในระบบ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        Citizen::findOrFail($citizen_id)->update([
            'citizen_name' => $request->citizen_name,
        ]);

        $notification = [
            'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
      //End Method
        public function DeleteCitizen($id)
            {
                $citizen = Citizen::find($id);
                $citizen->delete();

        return redirect()->back();
            }
            //End Method
}





