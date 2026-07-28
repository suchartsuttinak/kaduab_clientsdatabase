@extends('layouts.landing_pages')

@section('content')
<section class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100 py-8 sm:py-10 lg:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-6 sm:mb-8">
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="px-6 py-6 sm:px-8 sm:py-8 flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                            เกี่ยวกับเรา
                        </h1>
                        <p class="text-slate-500 text-sm mt-1">
                            จัดการข้อมูลองค์กร
                        </p>
                    </div>

                    <a href="{{ route('client.show') }}"
                       class="px-4 py-2 rounded-xl border bg-white hover:bg-slate-50">
                        ← กลับหน้า Client
                    </a>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <div class="mb-8 bg-white rounded-3xl border shadow p-6">
            <form action="{{ route('landing.about.store') }}" method="POST" class="space-y-4">
                @csrf

                <select name="type" class="w-full border rounded-xl px-4 py-3">
                    <option value="history">ประความเป็นมา</option>
                    <option value="objective">วัตถุประสงค์</option>
                    <option value="mission">พันธกิจ</option>
                </select>

                <textarea name="content" rows="4"
                          class="w-full border rounded-xl px-4 py-3"
                          placeholder="รายละเอียด..."></textarea>

                <div class="text-right">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-xl">
                        บันทึก
                    </button>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-3xl border shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50 font-semibold">
                ข้อมูลล่าสุด
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">ประเภท</th>
                            <th class="px-6 py-3 text-left">รายละเอียด</th>
                            <th class="px-6 py-3 text-left">วันที่</th>
                            <th class="px-6 py-3 text-left">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($aboutData as $data)
                        <tr>

                            <td class="px-6 py-4">
                                {{ $data->type }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $data->content }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $data->created_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- ✅ ปุ่มลบ --}}
                            <td class="px-6 py-4">
                                <form action="{{ route('landing.about.delete', $data->id) }}"
                                      method="POST"
                                      class="delete-form">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                            class="btn-delete px-3 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200">
                                        ลบ
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-400">
                                ไม่มีข้อมูล
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

{{-- ✅ SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ปุ่มลบ
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {

            const form = this.closest('form');

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ลบแล้วกู้คืนไม่ได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#ef4444'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });
    });

    // success
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ',
        text: "{{ session('success') }}"
    });
    @endif

});
</script>

@endsection