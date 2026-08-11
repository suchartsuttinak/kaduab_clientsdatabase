@props(['target', 'pageLength' => 10])
@php
    $stableLength = in_array((int) $pageLength, [10, 25, 50, 100], true) ? (int) $pageLength : 10;
@endphp
<div class="kst-toolbar" data-kst-controls="{{ $target }}" data-permission-keep>
    <div class="kst-length" data-permission-keep>
        <label data-permission-keep>
            <span>แสดง</span>
            <select class="form-select form-select-sm" data-kst-length data-permission-keep aria-label="จำนวนรายการต่อหน้า">
                @foreach([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" @selected($size === $stableLength)>{{ $size }}</option>
                @endforeach
            </select>
            <span>รายการ</span>
        </label>
    </div>

    <div class="kst-search" data-permission-keep>
        <label data-permission-keep>
            <span>ค้นหา:</span>
            <input type="search"
                   class="form-control form-control-sm"
                   autocomplete="off"
                   placeholder="พิมพ์เพื่อค้นหา"
                   data-kst-search
                   data-permission-keep>
        </label>
    </div>
</div>
