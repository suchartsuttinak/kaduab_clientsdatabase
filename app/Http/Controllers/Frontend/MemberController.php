<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Education;
use App\Models\Income;
use App\Models\Member;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function AddMember($client_id)
    {
        $client = Client::forUser(auth()->user())
            ->with('members')
            ->findOrFail($client_id);

        if ($client->members->isNotEmpty()) {
            return redirect()
                ->route('member.show', $client_id)
                ->with([
                    'message' => 'มีข้อมูลสมาชิกครอบครัวแล้ว กรุณาแก้ไขข้อมูล',
                    'alert-type' => 'info',
                ]);
        }

        $educations = Education::orderBy('education_name')->get();
        $occupations = Occupation::orderBy('occupation_name')->get();
        $incomes = Income::orderBy('id')->get();

        return view('frontend.client.member.member_create', compact(
            'client',
            'educations',
            'occupations',
            'incomes'
        ));
    }

    public function ShowMember($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $members = Member::with(['education', 'occupation', 'income'])
            ->where('client_id', $client->id)
            ->get();

        return view('frontend.client.member.member_show', compact('client', 'members'));
    }

    public function StoreMember(Request $request)
    {
        $this->normalizeMembers($request);
        $validated = $request->validate(
            $this->memberRules(),
            $this->memberMessages(),
            $this->memberAttributes()
        );

        // ป้องกันการส่ง client_id ของผู้รับบริการที่ผู้ใช้ไม่มีสิทธิ์เข้าถึง
        $client = Client::forUser(auth()->user())
            ->whereKey($validated['client_id'])
            ->firstOrFail();

        DB::transaction(function () use ($client, $validated): void {
            foreach ($validated['members'] as $memberData) {
                $client->members()->create($this->memberPayload($memberData));
            }

            $memberCount = count($validated['members']);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'member')
                ->delete();

            CaseActivity::record([
                'client_id' => $client->id,
                'module' => 'member',
                'type' => 'success',
                'title' => 'บันทึกสมาชิกในครอบครัว',
                'description' => 'เพิ่มข้อมูลสมาชิกในครอบครัว จำนวน ' . $memberCount . ' คน',
                'occurred_at' => now('Asia/Bangkok'),
                'icon' => 'bi-person-lines-fill',
                'url' => route('member.show', $client->id),
            ]);
        });

        return redirect()
            ->route('member.show', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลสมาชิกในครอบครัวเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function EditMember($id)
    {
        $client = Client::forUser(auth()->user())
            ->with('members')
            ->findOrFail($id);

        $educations = Education::orderBy('education_name')->get();
        $occupations = Occupation::orderBy('occupation_name')->get();
        $incomes = Income::orderBy('id')->get();

        return view('frontend.client.member.member_edit', compact(
            'client',
            'educations',
            'occupations',
            'incomes'
        ));
    }

    public function UpdateMember(Request $request, $id)
    {
        // ใช้ client_id จาก URL ที่ตรวจสอบสิทธิ์แล้วเท่านั้น
        $client = Client::forUser(auth()->user())->findOrFail($id);

        $request->merge([
            'client_id' => $client->id,
        ]);

        $this->normalizeMembers($request);
        $validated = $request->validate(
            $this->memberRules(),
            $this->memberMessages(),
            $this->memberAttributes()
        );

        DB::transaction(function () use ($client, $validated): void {
            // คงแนวทางเดิม: ลบรายการเดิมแล้วบันทึกรายการชุดใหม่
            $client->members()->delete();

            foreach ($validated['members'] as $memberData) {
                $client->members()->create($this->memberPayload($memberData));
            }

            $memberCount = count($validated['members']);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'member')
                ->delete();

            CaseActivity::record([
                'client_id' => $client->id,
                'module' => 'member',
                'type' => 'success',
                'title' => 'แก้ไขข้อมูลสมาชิกในครอบครัว',
                'description' => 'แก้ไขข้อมูลสมาชิกในครอบครัว จำนวน ' . $memberCount . ' คน',
                'occurred_at' => now('Asia/Bangkok'),
                'icon' => 'bi-person-lines-fill',
                'url' => route('member.show', $client->id),
            ]);
        });

        return redirect()
            ->route('member.show', $client->id)
            ->with([
                'message' => 'แก้ไขข้อมูลสมาชิกในครอบครัวเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * กฎตรวจสอบข้อมูลสมาชิกครอบครัว
     * ทุกช่องบังคับกรอก ยกเว้นหมายเหตุ
     */
    private function memberRules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists((new Client())->getTable(), 'id'),
            ],
            'members' => ['required', 'array', 'min:1'],
            'members.*.fullname' => ['required', 'string', 'max:255'],
            'members.*.member_age' => ['required', 'integer', 'min:0', 'max:150'],
            'members.*.education_id' => [
                'required',
                'integer',
                Rule::exists((new Education())->getTable(), 'id'),
            ],
            'members.*.relationship' => ['required', 'string', 'max:100'],
            'members.*.occupation_id' => [
                'required',
                'integer',
                Rule::exists((new Occupation())->getTable(), 'id'),
            ],
            'members.*.income_id' => [
                'required',
                'integer',
                Rule::exists((new Income())->getTable(), 'id'),
            ],
            'members.*.remark' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * ข้อความ Validation ภาษาไทย
     */
    private function memberMessages(): array
    {
        return [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ กรุณาเปิดแบบฟอร์มใหม่อีกครั้ง',
            'client_id.integer' => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists' => 'ไม่พบผู้รับบริการในระบบ',

            'members.required' => 'กรุณาเพิ่มข้อมูลสมาชิกในครอบครัวอย่างน้อย 1 คน',
            'members.array' => 'รูปแบบข้อมูลสมาชิกในครอบครัวไม่ถูกต้อง',
            'members.min' => 'กรุณาเพิ่มข้อมูลสมาชิกในครอบครัวอย่างน้อย 1 คน',

            'members.*.fullname.required' => 'กรุณากรอกชื่อ - นามสกุล',
            'members.*.fullname.string' => 'ชื่อ - นามสกุลต้องเป็นข้อความ',
            'members.*.fullname.max' => 'ชื่อ - นามสกุลต้องไม่เกิน 255 ตัวอักษร',

            'members.*.member_age.required' => 'กรุณากรอกอายุ',
            'members.*.member_age.integer' => 'อายุต้องเป็นจำนวนเต็ม',
            'members.*.member_age.min' => 'อายุต้องไม่น้อยกว่า 0 ปี',
            'members.*.member_age.max' => 'อายุต้องไม่เกิน 150 ปี',

            'members.*.education_id.required' => 'กรุณาเลือกระดับการศึกษา',
            'members.*.education_id.integer' => 'ระดับการศึกษาที่เลือกไม่ถูกต้อง',
            'members.*.education_id.exists' => 'ไม่พบระดับการศึกษาที่เลือกในระบบ',

            'members.*.relationship.required' => 'กรุณากรอกความสัมพันธ์กับผู้รับบริการ',
            'members.*.relationship.string' => 'ความสัมพันธ์ต้องเป็นข้อความ',
            'members.*.relationship.max' => 'ความสัมพันธ์ต้องไม่เกิน 100 ตัวอักษร',

            'members.*.occupation_id.required' => 'กรุณาเลือกอาชีพ',
            'members.*.occupation_id.integer' => 'อาชีพที่เลือกไม่ถูกต้อง',
            'members.*.occupation_id.exists' => 'ไม่พบอาชีพที่เลือกในระบบ',

            'members.*.income_id.required' => 'กรุณาเลือกรายได้เฉลี่ยต่อเดือน',
            'members.*.income_id.integer' => 'รายได้ที่เลือกไม่ถูกต้อง',
            'members.*.income_id.exists' => 'ไม่พบช่วงรายได้ที่เลือกในระบบ',

            'members.*.remark.string' => 'หมายเหตุต้องเป็นข้อความ',
            'members.*.remark.max' => 'หมายเหตุต้องไม่เกิน 255 ตัวอักษร',
        ];
    }

    private function memberAttributes(): array
    {
        return [
            'client_id' => 'ผู้รับบริการ',
            'members' => 'สมาชิกในครอบครัว',
            'members.*.fullname' => 'ชื่อ - นามสกุล',
            'members.*.member_age' => 'อายุ',
            'members.*.education_id' => 'ระดับการศึกษา',
            'members.*.relationship' => 'ความสัมพันธ์กับผู้รับบริการ',
            'members.*.occupation_id' => 'อาชีพ',
            'members.*.income_id' => 'รายได้เฉลี่ยต่อเดือน',
            'members.*.remark' => 'หมายเหตุ',
        ];
    }

    /**
     * ตัดช่องว่างและเปลี่ยนหมายเหตุว่างเป็น null ก่อนตรวจสอบ
     */
    private function normalizeMembers(Request $request): void
    {
        if (!is_array($request->input('members'))) {
            return;
        }

        $members = collect($request->input('members'))
            ->map(function ($member): array {
                $member = is_array($member) ? $member : [];

                return [
                    'fullname' => trim((string) ($member['fullname'] ?? '')),
                    'member_age' => $member['member_age'] ?? null,
                    'education_id' => $member['education_id'] ?? null,
                    'relationship' => trim((string) ($member['relationship'] ?? '')),
                    'occupation_id' => $member['occupation_id'] ?? null,
                    'income_id' => $member['income_id'] ?? null,
                    'remark' => filled($member['remark'] ?? null)
                        ? trim((string) $member['remark'])
                        : null,
                ];
            })
            ->values()
            ->all();

        $request->merge(['members' => $members]);
    }

    private function memberPayload(array $memberData): array
    {
        return [
            'fullname' => $memberData['fullname'],
            'member_age' => $memberData['member_age'],
            'education_id' => $memberData['education_id'],
            'relationship' => $memberData['relationship'],
            'occupation_id' => $memberData['occupation_id'],
            'income_id' => $memberData['income_id'],
            'remark' => $memberData['remark'] ?? null,
        ];
    }
}