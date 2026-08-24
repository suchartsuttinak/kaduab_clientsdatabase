<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    // แสดงข้อมูลทั้งหมด
    public function ProjectShow()
    {
        $project = Project::latest()->get();

        return view('backend.project.project_show', compact('project'));
    }

    // บันทึกข้อมูลใหม่
    public function ProjectStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_name' => 'required|string|max:255|unique:projects,project_name',
        ], [
            'project_name.required' => 'กรุณากรอกชื่อโครงการ',
            'project_name.string'   => 'ชื่อโครงการไม่ถูกต้อง',
            'project_name.max'      => 'ชื่อโครงการต้องไม่เกิน 255 ตัวอักษร',
            'project_name.unique'   => 'ชื่อโครงการนี้มีอยู่แล้วในระบบ',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        Project::create([
            'project_name' => trim($request->project_name),
        ]);

        return redirect()
            ->back()
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    // ดึงข้อมูลเพื่อแก้ไข
    public function EditProject($id)
    {
        $project = Project::findOrFail($id);

        return response()->json($project);
    }

    // อัปเดตข้อมูล
    public function UpdateProject(Request $request)
    {
        $projectId = $request->project_id;

        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',

            'project_name' =>
                'required|string|max:255|unique:projects,project_name,' . $projectId,
        ], [
            'project_id.required'   => 'ไม่พบข้อมูลโครงการที่ต้องการแก้ไข',
            'project_id.exists'     => 'ไม่พบข้อมูลโครงการในระบบ',

            'project_name.required' => 'กรุณากรอกชื่อโครงการ',
            'project_name.string'   => 'ชื่อโครงการไม่ถูกต้อง',
            'project_name.max'      => 'ชื่อโครงการต้องไม่เกิน 255 ตัวอักษร',
            'project_name.unique'   => 'ชื่อโครงการนี้มีอยู่แล้วในระบบ',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        Project::findOrFail($projectId)->update([
            'project_name' => trim($request->project_name),
        ]);

        return redirect()
            ->back()
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    // ลบข้อมูล
    public function DeleteProject($id)
    {
        $project = Project::findOrFail($id);

        $project->delete();

        return redirect()
            ->back()
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}