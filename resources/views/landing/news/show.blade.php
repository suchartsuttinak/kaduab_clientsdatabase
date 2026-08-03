@extends('layouts.landing_pages')

@section('content')
<section class="py-16 bg-gradient-to-b from-gray-50 to-gray-100">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            @if(!empty($news->image))
                @php
                    if (str_starts_with($news->image, 'upload/')) {
                        $imageUrl = asset($news->image);
                    } elseif (str_starts_with($news->image, 'storage/')) {
                        $imageUrl = asset($news->image);
                    } else {
                        $imageUrl = asset('storage/' . $news->image);
                    }
                @endphp

                {{-- =====================================================
                     PATCH:
                     Lazy Load + Async Decode
                     ช่วยให้หน้ารายละเอียดข่าวโหลดเร็วขึ้น
                ====================================================== --}}
                <img src="{{ $imageUrl }}"
                     alt="{{ $news->title }}"
                     loading="lazy"
                     decoding="async"
                     class="w-full h-96 object-cover">
            @endif

            <div class="p-8">
                <h1 class="text-3xl font-bold text-primary mb-6">
                    {{ $news->title }}
                </h1>

                <p class="text-gray-700 leading-relaxed mb-8 whitespace-pre-line break-words">
                    {{ $news->description }}
                </p>

                <div class="flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
                    <a href="{{ route('news.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-secondary text-white rounded-lg shadow hover:bg-secondary-dark transition">
                        ← กลับไปหน้าข่าวทั้งหมด
                    </a>

                    <span class="text-sm text-gray-500">
                        เผยแพร่เมื่อ {{ optional($news->created_at)->format('d/m/Y H:i') }}
                    </span>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection