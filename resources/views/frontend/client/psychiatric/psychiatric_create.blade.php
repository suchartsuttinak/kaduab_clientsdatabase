@extends('admin_client.admin_client')

@section('content')
<div class="container-fluid mt-2 psychiatric-page">
    <div class="card shadow-sm border-0 psychiatric-card">
        @include('frontend.client.psychiatric.partials.header')

        <div class="card-body p-2 p-md-3">
            @include('frontend.client.psychiatric.partials._client_info')
            @include('frontend.client.psychiatric.partials._table')
        </div>
    </div>
</div>

@include('frontend.client.psychiatric.partials._create_modal')
@include('frontend.client.psychiatric.partials._edit_modal')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/psychiatric.css') }}">
<style>
    /* Modal ต้องอยู่เหนือ header/sidebar และเลื่อนภายในได้ */
    #createPsychiatricModal,
    #editPsychiatricModal {
        z-index: 2147483000 !important;
    }

    body.psychiatric-modal-open .modal-backdrop {
        z-index: 2147482990 !important;
    }

    #createPsychiatricModal .modal-dialog,
    #editPsychiatricModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 1080px;
        height: calc(100dvh - 2rem);
        margin: 1rem auto;
    }

    #createPsychiatricModal .modal-content,
    #editPsychiatricModal .modal-content,
    #createPsychiatricModal .psy-modal-form,
    #editPsychiatricModal .psy-modal-form {
        display: flex;
        width: 100%;
        height: 100%;
        min-height: 0;
        flex-direction: column;
        overflow: hidden;
    }

    #createPsychiatricModal .modal-body,
    #editPsychiatricModal .modal-body {
        min-height: 0;
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .psy-drug-field.is-hidden {
        display: none !important;
    }

    /* ใช้ตัวครอบตารางเป็นจุดเลื่อนแนวนอนเพียงจุดเดียว */
    .psychiatric-page .psy-inline-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .psychiatric-page .psy-inline-table {
        width: 100% !important;
        min-width: 1180px;
        margin: 0 !important;
    }

    .psychiatric-page .psy-inline-action-group {
        overflow: visible !important;
    }

    .psychiatric-page .psy-col-actions {
        position: static !important;
        right: auto !important;
        box-shadow: none !important;
    }

    /* ป้องกัน DataTable สร้าง scrollbar ซ้อน */
    .psychiatric-page .dataTables_scrollBody {
        overflow-x: visible !important;
        overflow-y: visible !important;
    }

    @media (min-width: 1400px) {
        .psychiatric-page .psy-inline-table {
            min-width: 100%;
            table-layout: fixed;
        }
    }

    @media (max-width: 767.98px) {
        #createPsychiatricModal,
        #editPsychiatricModal {
            padding: 0 !important;
        }

        #createPsychiatricModal .modal-dialog,
        #editPsychiatricModal .modal-dialog {
            width: 100%;
            max-width: none;
            height: 100dvh;
            margin: 0;
        }

        #createPsychiatricModal .modal-content,
        #editPsychiatricModal .modal-content {
            height: 100dvh;
            border-radius: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    window.psychiatricConfig = {
        editJsonUrl: @json(url('/psychiatric/edit-json')),
        updateBaseUrl: @json(url('/psychiatric')),
        today: @json(now('Asia/Bangkok')->toDateString())
    };
</script>
@include('frontend.client.psychiatric.partials._script_init')
@endpush
