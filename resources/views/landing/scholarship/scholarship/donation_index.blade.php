@extends('layouts.landing_pages')

@section('content')
<section class="min-h-screen bg-slate-50 py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8 mb-6">
            <div class="inline-flex rounded-full bg-blue-50 px-4 py-1 text-sm font-semibold text-blue-700 mb-4">
                Donation History
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                ประวัติการบริจาค
            </h1>

            <p class="mt-2 text-slate-500">
                ผู้สนับสนุน:
                <strong>{{ $scholarship->fullname }}</strong>
            </p>

            <div class="mt-5 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('scholarship.donation.create', $scholarship->id) }}"
                   class="inline-flex justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    + บันทึกการบริจาคเพิ่ม
                </a>

                <a href="{{ route('scholarship.index') }}"
                   class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    กลับหน้ารายการผู้สนับสนุน
                </a>

                <a href="{{ url('/dashboard') }}"
                   class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    กลับหน้า Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white shadow-lg">
        <div class="text-sm font-semibold opacity-90">
            รวมเงินบริจาคทั้งหมด
        </div>

        <div class="mt-3 text-3xl font-extrabold">
            {{ number_format($totalDonationAmount ?? 0, 2) }}
        </div>

        <div class="mt-1 text-sm opacity-90">
            บาท
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">
            จำนวนรายการในหน้านี้
        </div>

        <div class="mt-3 text-3xl font-extrabold text-slate-800">
            {{ $donations->count() }}
        </div>

        <div class="mt-1 text-sm text-slate-400">
            รายการ
        </div>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">
            รายการบริจาคทั้งหมด
        </div>

        <div class="mt-3 text-3xl font-extrabold text-blue-700">
            {{ $donations->total() }}
        </div>

        <div class="mt-1 text-sm text-slate-400">
            รายการ
        </div>
    </div>
</div>

        <div class="rounded-3xl bg-white border border-slate-200 shadow-xl overflow-hidden">
            @if($donations->count())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-4 text-left text-sm font-bold text-slate-600">ลำดับ</th>
                                <th class="px-4 py-4 text-left text-sm font-bold text-slate-600">วันที่บริจาค</th>
                                <th class="px-4 py-4 text-left text-sm font-bold text-slate-600">ปี</th>
                                <th class="px-4 py-4 text-left text-sm font-bold text-slate-600">ประเภท</th>
                                <th class="px-4 py-4 text-right text-sm font-bold text-slate-600">จำนวนเงิน</th>
                                <th class="px-4 py-4 text-left text-sm font-bold text-slate-600">รายละเอียด</th>
                                <th class="px-4 py-4 text-left text-sm font-bold text-slate-600">ผู้บันทึก</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($donations as $index => $donation)
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="px-4 py-4 text-sm">
                                        {{ $donations->firstItem() + $index }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        {{ optional($donation->donation_date)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        {{ $donation->donation_date ? $donation->donation_date->year + 543 : '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        {{ $donation->donation_type ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm text-right font-bold text-emerald-700">
                                        {{ !is_null($donation->amount) ? number_format($donation->amount, 2) : '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        {{ $donation->description ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        {{ $donation->recorded_by ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-4 border-t">
                    {{ $donations->links() }}
                </div>
            @else
                <div class="p-10 text-center text-slate-500">
                    ยังไม่มีประวัติการบริจาคของผู้สนับสนุนรายนี้
                </div>
            @endif
        </div>

    </div>
</section>
@endsection