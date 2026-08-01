@extends('admin_client.admin_client')

@section('content')
@php
    $addictiveAddHasErrors = $errors->any() && old('_form_context') === 'addictive_add';
    $addictiveEditHasErrors = $errors->any() && old('_form_context') === 'addictive_edit';

    // ประกาศเป็นตัวแปร PHP ก่อนส่งเข้า @json ป้องกัน Blade ParseError
    $addictiveEditOldValues = [
        'id' => old('_edit_id'),
        'date' => old('date'),
        'count' => old('count'),
        'exam' => old('exam'),
        'refer' => old('refer'),
        'record' => old('record'),
        'recorder' => old('recorder'),
    ];
@endphp

<div class="container-fluid mt-2 addictive-page">
    <div class="card shadow-sm border-0 addictive-card">
        @include('frontend.client.addictive.partials.header')

        <div class="card-body p-2 p-md-3">
            @include('frontend.client.addictive.partials._client_info')
            @include('frontend.client.addictive.partials._table')
        </div>
    </div>
</div>

@include('frontend.client.addictive.partials._create_modal')
@include('frontend.client.addictive.partials._edit_modal')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/addictive.css') }}">
@endpush

@push('scripts')
<script>
    window.addictiveConfig = {
        jsonUrl: @json(url('/addictive/json')),
        updateBaseUrl: @json(url('/addictive/update')),
        addHasErrors: @json($addictiveAddHasErrors),
        editHasErrors: @json($addictiveEditHasErrors),
        editOldValues: @json($addictiveEditOldValues)
    };
</script>
@include('frontend.client.addictive.partials._script_init')
@endpush
