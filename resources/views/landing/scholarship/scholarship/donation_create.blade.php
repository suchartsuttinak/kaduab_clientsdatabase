@extends('layouts.landing_pages')

@section('content')
<section class="min-h-screen bg-slate-50 py-8 sm:py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <div class="inline-flex rounded-full bg-blue-50 px-4 py-1 text-sm font-semibold text-blue-700 mb-4">
                Donation Record
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                บันทึกการบริจาค
            </h1>

            <p class="mt-2 text-slate-500">
                ผู้สนับสนุน: <strong>{{ $scholarship->fullname }}</strong>
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                <div class="font-semibold">กรุณาตรวจสอบข้อมูล</div>
                <ul class="mt-2 list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('scholarship.donation.store', $scholarship->id) }}"
              method="POST"
              class="rounded-3xl bg-white border border-slate-200 shadow-xl p-6 sm:p-8">
            @csrf

            <div class="space-y-6">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        วันที่บริจาค <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="donation_date"
                           value="{{ old('donation_date', now('Asia/Bangkok')->toDateString()) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                           required
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        ประเภทการบริจาค <span class="text-red-500">*</span>
                    </label>
                    <select name="donation_type"
                            required
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none">
                        <option value="">-- เลือกประเภท --</option>
                        <option value="เงินสด" @selected(old('donation_type') === 'เงินสด')>เงินสด</option>
                        <option value="โอนเงิน" @selected(old('donation_type') === 'โอนเงิน')>โอนเงิน</option>
                        <option value="ทุนการศึกษา" @selected(old('donation_type') === 'ทุนการศึกษา')>ทุนการศึกษา</option>
                        <option value="ชุดนักเรียน" @selected(old('donation_type') === 'ชุดนักเรียน')>ชุดนักเรียน</option>
                        <option value="อุปกรณ์การเรียน" @selected(old('donation_type') === 'อุปกรณ์การเรียน')>อุปกรณ์การเรียน</option>
                        <option value="อื่น ๆ" @selected(old('donation_type') === 'อื่น ๆ')>อื่น ๆ</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        จำนวนเงิน
                    </label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           max="999999999.99"
                           name="amount"
                           value="{{ old('amount') }}"
                           class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                           placeholder="กรอกจำนวนเงิน ถ้ามี">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        รายละเอียดเพิ่มเติม
                    </label>
                    <textarea name="description"
                              rows="5"
                              maxlength="5000"
                              class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                              placeholder="เช่น จำนวนชุดนักเรียน จำนวนอุปกรณ์ หรือหมายเหตุอื่น ๆ">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row gap-3 sm:justify-between">
                <a href="{{ route('scholarship.index') }}"
                   class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    กลับหน้ารายการ
                </a>

                <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-blue-700">
                    บันทึกการบริจาค
                </button>
            </div>
        </form>

    </div>
</section>
@endsection