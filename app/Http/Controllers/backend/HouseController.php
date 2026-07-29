<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\House;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HouseController extends Controller
{
    /**
     * แสดงรายการบ้านพัก
     */
    public function HouseShow()
    {
        $house = House::latest()->get();

        return view('backend.house.house_show', compact('house'));
    }

    /**
     * บันทึกข้อมูลบ้านพัก
     */
    public function HouseStore(Request $request)
    {
        /*
         * ตัดช่องว่างด้านหน้าและด้านหลัง
         * หากไม่กรอก house_alias ให้เปลี่ยนเป็น NULL
         */
        $houseName = trim((string) $request->input('house_name'));
        $houseAlias = trim((string) $request->input('house_alias'));

        $request->merge([
            'house_name'  => $houseName,
            'house_alias' => $houseAlias !== '' ? $houseAlias : null,
        ]);

        $validator = Validator::make(
            $request->all(),
            [
                'house_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('houses', 'house_name'),
                ],

                'house_alias' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('houses', 'house_alias'),
                ],
            ],
            [
                'house_name.required' => 'กรุณากรอกชื่อบ้านพัก',
                'house_name.string'   => 'ชื่อบ้านพักต้องเป็นข้อความ',
                'house_name.max'      => 'ชื่อบ้านพักต้องมีความยาวไม่เกิน 255 ตัวอักษร',
                'house_name.unique'   => 'ชื่อบ้านพักนี้มีอยู่แล้วในระบบ',

                'house_alias.string' => 'ชื่อเรียกบ้านพักต้องเป็นข้อความ',
                'house_alias.max'    => 'ชื่อเรียกบ้านพักต้องมีความยาวไม่เกิน 255 ตัวอักษร',
                'house_alias.unique' => 'ชื่อเรียกบ้านพักนี้มีอยู่แล้วในระบบ',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_house_modal', 'add');
        }

        House::create([
            'house_name'  => $request->house_name,
            'house_alias' => $request->house_alias,
        ]);

        return redirect()
            ->back()
            ->with('success', 'บันทึกข้อมูลบ้านพักเรียบร้อยแล้ว');
    }

    /**
     * เรียกข้อมูลบ้านพักเพื่อแสดงใน Modal แก้ไข
     */
    public function EditHouse($id)
    {
        $house = House::findOrFail($id);

        return response()->json([
            'id'          => $house->id,
            'house_name'  => $house->house_name,
            'house_alias' => $house->house_alias,
        ]);
    }

    /**
     * อัปเดตข้อมูลบ้านพัก
     */
    public function UpdateHouse(Request $request)
    {
        /*
         * ตัดช่องว่างด้านหน้าและด้านหลัง
         * หากไม่กรอก house_alias ให้เปลี่ยนเป็น NULL
         */
        $houseName = trim((string) $request->input('house_name'));
        $houseAlias = trim((string) $request->input('house_alias'));

        $request->merge([
            'house_name'  => $houseName,
            'house_alias' => $houseAlias !== '' ? $houseAlias : null,
        ]);

        $houseId = $request->input('house_id');

        $validator = Validator::make(
            $request->all(),
            [
                'house_id' => [
                    'required',
                    'integer',
                    'exists:houses,id',
                ],

                'house_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('houses', 'house_name')
                        ->ignore($houseId),
                ],

                'house_alias' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('houses', 'house_alias')
                        ->ignore($houseId),
                ],
            ],
            [
                'house_id.required' => 'ไม่พบข้อมูลบ้านพักที่ต้องการแก้ไข',
                'house_id.integer'  => 'รหัสบ้านพักไม่ถูกต้อง',
                'house_id.exists'   => 'ไม่พบข้อมูลบ้านพักในระบบ',

                'house_name.required' => 'กรุณากรอกชื่อบ้านพัก',
                'house_name.string'   => 'ชื่อบ้านพักต้องเป็นข้อความ',
                'house_name.max'      => 'ชื่อบ้านพักต้องมีความยาวไม่เกิน 255 ตัวอักษร',
                'house_name.unique'   => 'ชื่อบ้านพักนี้มีอยู่แล้วในระบบ',

                'house_alias.string' => 'ชื่อเรียกบ้านพักต้องเป็นข้อความ',
                'house_alias.max'    => 'ชื่อเรียกบ้านพักต้องมีความยาวไม่เกิน 255 ตัวอักษร',
                'house_alias.unique' => 'ชื่อเรียกบ้านพักนี้มีอยู่แล้วในระบบ',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_house_modal', 'edit');
        }

        $house = House::findOrFail($houseId);

        $house->update([
            'house_name'  => $request->house_name,
            'house_alias' => $request->house_alias,
        ]);

        return redirect()
            ->back()
            ->with('success', 'แก้ไขข้อมูลบ้านพักเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลบ้านพัก
     */
    public function DeleteHouse($id)
    {
        $house = House::findOrFail($id);

        try {
            $house->delete();

            return redirect()
                ->back()
                ->with('success', 'ลบข้อมูลบ้านพักเรียบร้อยแล้ว');
        } catch (QueryException $exception) {
            /*
             * กรณีบ้านพักถูกอ้างอิงในข้อมูลผู้รับบริการ
             * หรือตารางอื่น ระบบจะไม่อนุญาตให้ลบ
             */
            return redirect()
                ->back()
                ->with(
                    'error',
                    'ไม่สามารถลบบ้านพักนี้ได้ เนื่องจากมีข้อมูลอื่นเชื่อมโยงกับบ้านพักนี้อยู่'
                );
        }
    }
}