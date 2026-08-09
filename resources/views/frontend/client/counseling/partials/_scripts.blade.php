<script>
document.addEventListener('DOMContentLoaded', function () {
    function initRisk(scope) {
        if (!scope) return;
        const select = scope.querySelector('.js-risk-level');
        const wrap = scope.querySelector('.js-risk-detail-wrap');
        if (!select || !wrap) return;

        function sync() {
            const show = select.value !== 'none';
            wrap.style.display = show ? '' : 'none';
            if (!show) {
                const input = wrap.querySelector('[name="risk_detail"]');
                if (input) input.value = '';
            }
        }

        select.addEventListener('change', sync);
        sync();
    }

    function initWorkflow(scope) {
        if (!scope) return;
        const status = scope.querySelector('.js-workflow-status');
        const wrap = scope.querySelector('.js-next-appointment-wrap');
        if (!status || !wrap) return;

        const appointment = wrap.querySelector('.js-next-appointment-date');
        const focus = wrap.querySelector('.js-followup-focus');
        const openStatuses = ['ongoing', 'follow_up'];

        function sync() {
            const isOpen = openStatuses.includes(status.value);
            wrap.style.display = isOpen ? '' : 'none';

            if (appointment) {
                appointment.required = isOpen;
                if (!isOpen) appointment.value = '';
            }

            if (focus) {
                focus.required = isOpen;
                if (!isOpen) focus.value = '';
            }
        }

        status.addEventListener('change', sync);
        sync();
    }

    document.querySelectorAll('form').forEach(function (form) {
        initRisk(form);
        initWorkflow(form);
    });

    document.querySelectorAll('.js-submit-once').forEach(function (button) {
        const form = button.form || button.closest('form');
        if (!form) return;
        form.addEventListener('submit', function () {
            if (button.dataset.submitting === '1') return;
            button.dataset.submitting = '1';
            button.disabled = true;
            setTimeout(function () {
                button.dataset.submitting = '0';
                button.disabled = false;
            }, 3500);
        });
    });

    document.querySelectorAll('.js-counseling-delete, .js-round-delete').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (typeof Swal === 'undefined') return;
            event.preventDefault();

            const isRound = form.classList.contains('js-round-delete');
            Swal.fire({
                title: 'ยืนยันการลบข้อมูล',
                text: isRound
                    ? 'ต้องการลบรอบล่าสุดนี้หรือไม่'
                    : 'การลบครั้งนี้จะลบรอบทั้งหมดภายใต้การให้คำปรึกษาครั้งนี้ด้วย',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                confirmButtonColor: '#dc2626'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    @if (session('success'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        }
    @endif

    @if (session('error'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถดำเนินการได้',
                text: @json(session('error')),
                confirmButtonText: 'OK'
            });
        }
    @endif

    @if ($errors->any() && old('_form_context') === 'create')
        const createModal = document.getElementById('counselingCreateModal');
        if (createModal && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(createModal).show();
        }
    @endif
});
</script>
