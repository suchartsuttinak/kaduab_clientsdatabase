$(function () {
    $(document).on('click', '#delete', function (e) {
        e.preventDefault();

        const link = $(this).attr('href');
        if (!link) {
            return;
        }

        Swal.fire({
            title: 'ท่านแน่ใจ ?',
            text: 'ลบข้อมูลนี้ใช่หรือไม่ ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!token) {
                Swal.fire(
                    'ไม่สามารถดำเนินการได้',
                    'ไม่พบ CSRF token กรุณารีเฟรชหน้าแล้วลองใหม่อีกครั้ง',
                    'error'
                );
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = link;
            form.style.display = 'none';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = token;

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
