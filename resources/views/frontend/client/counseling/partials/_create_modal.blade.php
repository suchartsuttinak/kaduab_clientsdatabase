<div class="modal fade csl-modal"
     id="counselingCreateModal"
     tabindex="-1"
     aria-labelledby="counselingCreateModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('counseling.store') }}"
                  method="POST"
                  id="counselingCreateForm">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="create">

                <div class="modal-header">
                    <h5 class="modal-title" id="counselingCreateModalLabel">
                        <i class="bi bi-chat-heart me-1 text-primary"></i>
                        เริ่มการให้คำปรึกษา ครั้งที่ {{ $nextSessionNo }} • รอบที่ 1
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body csl-page py-0">
                    @php($formMode = 'create')
                    @include('frontend.client.counseling.partials._initial_fields')
                </div>

                <div class="modal-footer">
                    <button type="button" class="csl-btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ปิด
                    </button>
                    <button type="submit" class="csl-btn-primary js-submit-once">
                        <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
