@props(['target', 'total' => 0, 'pageLength' => 10])
@php
    $stableTotal = max(0, (int) $total);
    $stableLength = in_array((int) $pageLength, [10, 25, 50, 100], true) ? (int) $pageLength : 10;
    $stableEnd = min($stableLength, $stableTotal);
    $stablePages = max(1, (int) ceil($stableTotal / $stableLength));
@endphp
<div class="kst-footer" data-kst-footer="{{ $target }}" data-permission-keep>
    <div class="kst-info" data-kst-info data-permission-keep>
        @if($stableTotal > 0)
            แสดง 1 ถึง {{ $stableEnd }} จากทั้งหมด {{ number_format($stableTotal) }} รายการ
        @else
            แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ
        @endif
    </div>
    <div class="kst-paging" data-permission-keep>
        <button type="button" class="kst-page-btn" data-kst-prev data-permission-keep disabled>ก่อนหน้า</button>
        <span class="kst-page-label" data-kst-page-label data-permission-keep>หน้า 1 / {{ $stablePages }}</span>
        <button type="button" class="kst-page-btn" data-kst-next data-permission-keep @disabled($stablePages <= 1)>ถัดไป</button>
    </div>
</div>
