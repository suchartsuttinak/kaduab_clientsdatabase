function confirmDelete(id) {
    Swal.fire({
        title: 'ท่านแน่ใจ ?',
        text: 'ลบข้อมูลนี้ใช่หรือไม่ ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function resetValidation(form){
    if (!form) return;
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function setTodayIfEmpty(form){
    if (!form) return;
    const today = new Date().toISOString().split('T')[0];
    const absentDate = form.querySelector('input[name="absent_date"]');
    const recordDate = form.querySelector('input[name="record_date"]');

    if (absentDate && !absentDate.value) absentDate.value = today;
    if (recordDate && !recordDate.value) recordDate.value = today;
}

function resetAbsentForm(){
    const form = document.getElementById('absent-form');
    if (!form) return;
    form.reset();
    resetValidation(form);
    setTodayIfEmpty(form);
}

function resetEditForm(){
    const form = document.getElementById('edit-absent-form');
    if (!form) return;
    form.reset();
    resetValidation(form);
    form.setAttribute('action', '');
}

function openEditAbsent(id){
    const form = $('#edit-absent-form');
    if (!form.length) return;

    form[0].reset();

    $.ajax({
        url: `${window.absentConfig.editBaseUrl}/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(res){
            if (!res.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: res.message || 'ไม่สามารถโหลดข้อมูลได้'
                });
                return;
            }

            const data = res.data;

            $('#edit_absent_date').val(data.absent_date ?? '');
            $('#edit_record_date').val(data.record_date ?? '');
            $('#edit_cause').val(data.cause ?? '');
            $('#edit_operation').val(data.operation ?? '');
            $('#edit_remark').val(data.remark ?? '');
            $('#edit_teacher').val(data.teacher ?? '');
            $('#edit_education_record_id').val(data.education_record_id ?? '');

            $('#edit_school_name').text(data.school_name ?? 'ไม่พบข้อมูล');
            $('#edit_education_name').text(data.education_name ?? 'ไม่พบข้อมูล');
            $('#edit_semester_name').text(data.semester_name ?? 'ไม่พบข้อมูล');

            $('#edit-absent-form').attr('action', `${window.absentConfig.updateBaseUrl}/${data.id}`);

            const modalEl = document.getElementById('editAbsentModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลสำหรับแก้ไขได้'
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {

    const openBtn = document.getElementById('btn-open-absent-modal');
    const cancelBtn = document.getElementById('btn-cancel-absent');
    const cancelEditBtn = document.getElementById('btn-cancel-edit-absent');
    const absentModal = document.getElementById('absentModal');
    const editModal = document.getElementById('editAbsentModal');

    openBtn?.addEventListener('click', resetAbsentForm);
    cancelBtn?.addEventListener('click', resetAbsentForm);
    cancelEditBtn?.addEventListener('click', resetEditForm);

    absentModal?.addEventListener('hidden.bs.modal', resetAbsentForm);
    editModal?.addEventListener('hidden.bs.modal', resetEditForm);

    setTodayIfEmpty(document.getElementById('absent-form'));

    $('#edit-absent-form').on('submit', function(e){
        e.preventDefault();

        const form = $(this);
        const actionUrl = form.attr('action');
        const formData = new FormData(this);

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: res.message || 'บันทึกข้อมูลสำเร็จ',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('editAbsentModal')).hide();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: res.message || 'ไม่สามารถแก้ไขข้อมูลได้'
                    });
                }
            },
            error: function(xhr){
                let errorText = 'ไม่สามารถบันทึกข้อมูลได้';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorText = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: errorText
                });
            }
        });
    });
});