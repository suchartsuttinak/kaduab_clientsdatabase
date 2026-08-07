@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/school-followup.css') }}">
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $clientName = $client->fullname ?? $client->full_name ?? '-';
    $clientAge = $client->age ?? '-';
    $schoolName = optional($educationRecord)->school_name ?? 'ไม่พบข้อมูล';
    $educationName = optional(optional($educationRecord)->education)->education_name ?? 'ไม่พบข้อมูล';
 $semesterName = $educationRecord->semester_label
    ?? data_get($educationRecord, 'semester.semester_name', 'ไม่พบข้อมูล');

    $profileImage = asset('upload/no_image.jpg');
    $clientImageFile = !empty($client->image) ? public_path('upload/client_images/' . $client->image) : null;

    if (!empty($client->image) && $clientImageFile && file_exists($clientImageFile)) {
        $profileImage = asset('upload/client_images/' . $client->image);
    }
@endphp

<div class="container-fluid py-4 school-followup-page">
    @include('frontend.client.school_followup.partials.header')
    @include('frontend.client.school_followup.partials.summary')
    @include('frontend.client.school_followup.partials.school_followup_fillter')
    @include('frontend.client.school_followup.partials.table')
</div>

@include('frontend.client.school_followup.partials.create-modal')
@include('frontend.client.school_followup.partials.edit-modal')

@if ($errors->any())
<script>
document.addEventListener("DOMContentLoaded", function () {
    const createModalEl = document.getElementById('followupModal');
    if (createModalEl) {
        const modal = new bootstrap.Modal(createModalEl);
        modal.show();
    }
});
</script>
@endif
@endsection

@push('scripts')
<script>
    window.schoolFollowupConfig = {
        editUrlTemplate: "{{ route('school_followup.edit', ':id') }}",
        updateUrlTemplate: "{{ route('school_followup.update', ':id') }}"
    };
</script>
<script src="{{ asset('backend/assets/js/school-followup.js') }}"></script>
@endpush