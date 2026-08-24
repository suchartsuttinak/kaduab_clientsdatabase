<script>
(function () {
    'use strict';

    function fieldLabel(field) {
        if (!field) return 'ข้อมูลที่จำเป็น';
        const id = field.id;
        let label = id ? document.querySelector('label[for="' + CSS.escape(id) + '"]') : null;
        if (!label) {
            const wrapper = field.closest('.col-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-8,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.mb-3,.form-group');
            label = wrapper ? wrapper.querySelector('.form-label,label') : null;
        }
        const text = label ? label.textContent.replace('*', '').trim() : '';
        return text || 'ข้อมูลที่จำเป็น';
    }

    function thaiMessage(field) {
        const label = fieldLabel(field);
        if (field.validity.valueMissing) {
            if (field.matches('select')) return 'กรุณาเลือก' + label;
            return 'กรุณาระบุ' + label;
        }
        if (field.validity.rangeUnderflow) return label + 'ต้องไม่น้อยกว่าค่าหรือวันที่กำหนด';
        if (field.validity.rangeOverflow) return label + 'ต้องไม่เกินค่าหรือวันที่กำหนด';
        if (field.validity.typeMismatch) return label + 'มีรูปแบบไม่ถูกต้อง';
        if (field.validity.tooLong) return label + 'มีความยาวเกินกว่าที่กำหนด';
        if (field.validity.badInput) return label + 'มีค่าไม่ถูกต้อง';
        return 'กรุณาตรวจสอบ' + label;
    }

    document.querySelectorAll('form[data-idp-th-validation="1"]').forEach(function (form) {
        form.querySelectorAll('input,select,textarea').forEach(function (field) {
            field.addEventListener('invalid', function () {
                if (field.validity.valid) return;
                field.setCustomValidity(thaiMessage(field));
            });
            ['input', 'change'].forEach(function (eventName) {
                field.addEventListener(eventName, function () { field.setCustomValidity(''); });
            });
        });
    });
})();
</script>
