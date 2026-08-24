@if(session('success') || session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = @json(session('error') ? 'error' : 'success');
    const message = @json(session('error') ?? session('success'));
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: type === 'success' ? 'ดำเนินการเรียบร้อย' : 'ไม่สามารถดำเนินการได้',
            text: message,
            confirmButtonText: 'OK',
            timer: type === 'success' ? 2000 : undefined,
            timerProgressBar: type === 'success'
        });
    }
});
</script>
@endif
