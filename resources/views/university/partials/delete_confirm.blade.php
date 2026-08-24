<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form.ut-delete-form').forEach(function (form) {
        if (form.dataset.bound === '1') return;
        form.dataset.bound = '1';
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === '1') return;
            event.preventDefault();
            const message = form.dataset.message || 'ยืนยันการลบข้อมูลนี้หรือไม่?';
            if (typeof Swal === 'undefined') {
                if (window.confirm(message)) { form.dataset.confirmed = '1'; form.submit(); }
                return;
            }
            Swal.fire({
                icon: 'warning', title: 'ยืนยันการลบ', text: message,
                showCancelButton: true, confirmButtonText: 'ลบข้อมูล', cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#dc2626'
            }).then(function (result) {
                if (result.isConfirmed) { form.dataset.confirmed = '1'; form.submit(); }
            });
        });
    });
});
</script>
