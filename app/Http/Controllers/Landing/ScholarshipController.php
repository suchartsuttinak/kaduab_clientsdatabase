<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;            // ✅ เพิ่มตัวนี้
use App\Models\ScholarshipDonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
  

        public function index(Request $request)
        {
            /*
            |--------------------------------------------------------------------------
            | เมื่อ Admin เปิดดูหน้ารายการผู้สนับสนุนทุน
            | ให้ถือว่าอ่านแจ้งเตือนแล้ว
            |--------------------------------------------------------------------------
            */
            if (auth()->check() && auth()->user()->role === 'admin') {
                Scholarship::where('is_read', false)->update([
                    'is_read' => true,
                    'read_at' => now('Asia/Bangkok'),
                ]);
            }

            $startDate = $request->start_date;
            $endDate   = $request->end_date;

            $query = ScholarshipDonation::query();

            // filter ตามช่วงวันที่
            if ($startDate && $endDate) {
                $query->whereBetween('donation_date', [$startDate, $endDate]);
            }

            // ดึง id ผู้สนับสนุน
            $scholarshipIds = $query->pluck('scholarship_id')->unique();

            $scholarships = Scholarship::when($startDate && $endDate, function ($q) use ($scholarshipIds) {
                    $q->whereIn('id', $scholarshipIds);
                })
                ->latest()
                ->paginate(20);

            // รวมเงินตามช่วงวันที่
            $totalDonationAmount = (clone $query)->sum('amount');

            // summary ตามปี
            $donationYearSummary = ScholarshipDonation::selectRaw('YEAR(donation_date) as year, SUM(amount) as total_amount')
                ->groupBy(DB::raw('YEAR(donation_date)'))
                ->orderByDesc('year')
                ->get();

            return view('landing.scholarship.scholarship.index', compact(
                'scholarships',
                'totalDonationAmount',
                'donationYearSummary',
                'startDate',
                'endDate'
            ));
        }

    public function create()
    {
        return view('landing.scholarship.scholarship.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'support_types' => 'required|array|min:1',
            'support_types.*' => 'string|max:100',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'detail' => 'nullable|string',
        ]);

        Scholarship::create([
            'fullname' => $request->fullname,
            'support_types' => $request->support_types,
            'phone' => $request->phone,
            'email' => $request->email,
            'detail' => $request->detail,
             // แจ้งเตือนใหม่ (ยังไม่ได้เปิดอ่าน)
            'is_read' => false,
        ]);

        return redirect()
            ->route('scholarship.create')
            ->with('success', 'ส่งข้อมูลการสนับสนุนเรียบร้อยแล้ว เจ้าหน้าที่จะติดต่อกลับภายหลัง');
    }

    public function createDonation(Scholarship $scholarship)
    {
        return view('landing.scholarship.scholarship.donation_create', compact('scholarship'));
    }

    public function storeDonation(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'donation_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'donation_type' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        ScholarshipDonation::create([
            'scholarship_id' => $scholarship->id,
            'donation_date' => $request->donation_date,
            'amount' => $request->amount,
            'donation_type' => $request->donation_type,
            'description' => $request->description,
            'recorded_by' => auth()->user()->name ?? null,
        ]);

        return redirect()
            ->route('scholarship.donation.index', $scholarship->id)
            ->with('success', 'บันทึกข้อมูลการบริจาคเรียบร้อยแล้ว');
    }

    public function donationIndex($id)
    {
        $scholarship = Scholarship::findOrFail($id);

        $donations = $scholarship->donations()
            ->latest('donation_date')
            ->paginate(20);

        // ✅ รวมยอดเงินบริจาคทั้งหมดของผู้สนับสนุนรายนี้
        $totalDonationAmount = $scholarship->donations()
            ->sum('amount');

        // ✅ สรุปยอดบริจาคแยกตามปี เช่น 2566 / 2567 / 2568
        $donationYearSummary = $scholarship->donations()
            ->selectRaw('YEAR(donation_date) as year, SUM(amount) as total_amount, COUNT(*) as total_items')
            ->whereNotNull('donation_date')
            ->groupBy(DB::raw('YEAR(donation_date)'))
            ->orderByDesc('year')
            ->get();

        return view('landing.scholarship.scholarship.donation_index', compact(
            'scholarship',
            'donations',
            'totalDonationAmount',
            'donationYearSummary'
        ));
    }
}