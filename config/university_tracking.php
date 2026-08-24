<?php

return [
    'permission_key' => 'education_university',

    // ปรับสองค่านี้ให้ตรงกับ Layout จริงของโครงการได้โดยไม่ต้องแก้ View ทุกไฟล์
    'dashboard_layout' => 'admin.admin_master',
    'client_layout' => 'admin_client.admin_client',

    'max_pdf_kb' => 10240,
    'max_subjects_per_semester' => 40,
    'max_followup_issues' => 20,

    'enrollment_statuses' => [
        'studying' => 'กำลังศึกษา',
        'leave' => 'พักการเรียน',
        'transferred' => 'ย้ายสถานศึกษา/เปลี่ยนหลักสูตร',
        'graduated' => 'สำเร็จการศึกษา',
        'dropout' => 'ลาออก/ออกกลางคัน',
        'dismissed' => 'พ้นสภาพนักศึกษา',
        'lost_contact' => 'ขาดการติดต่อ',
        'other' => 'อื่น ๆ',
    ],

    'academic_statuses' => [
        'normal' => 'ปกติ',
        'watch' => 'เฝ้าระวัง',
        'probation' => 'ภาคทัณฑ์ทางการศึกษา',
        'leave' => 'พักการเรียน',
        'dismissed' => 'พ้นสภาพ',
        'completed' => 'สำเร็จภาคการศึกษา',
        'other' => 'อื่น ๆ',
    ],

    'risk_levels' => [
        'normal' => 'ปกติ',
        'watch' => 'เฝ้าระวัง',
        'risk' => 'เสี่ยง',
        'high_risk' => 'เสี่ยงสูง',
    ],

    'followup_methods' => [
        'in_person' => 'พบด้วยตนเอง',
        'phone' => 'โทรศัพท์',
        'line' => 'LINE/แชต',
        'video_call' => 'วิดีโอคอล',
        'university' => 'ประสานมหาวิทยาลัย',
        'home_visit' => 'เยี่ยมที่พัก/บ้าน',
        'other' => 'อื่น ๆ',
    ],

    'issue_categories' => [
        'academic' => 'การเรียน/ผลการเรียน',
        'attendance' => 'การเข้าเรียน/ขาดเรียน',
        'adaptation' => 'การปรับตัวในมหาวิทยาลัย',
        'finance' => 'การเงิน/ทุนการศึกษา',
        'housing' => 'ที่พัก/การเดินทาง',
        'health' => 'สุขภาพกาย',
        'mental' => 'สภาพจิตใจ/ความเครียด',
        'family' => 'ครอบครัว',
        'relationship' => 'ความสัมพันธ์กับเพื่อน/อาจารย์',
        'work' => 'การทำงานระหว่างเรียน',
        'behavior_risk' => 'พฤติกรรมเสี่ยง/วินัย',
        'motivation' => 'แรงจูงใจในการเรียน',
        'career' => 'เป้าหมายการเรียน/อาชีพ',
        'contact' => 'ความต่อเนื่องในการติดต่อ',
        'other' => 'อื่น ๆ',
    ],

    'issue_statuses' => [
        'open' => 'ยังต้องติดตาม',
        'improving' => 'ดีขึ้น',
        'resolved' => 'คลี่คลายแล้ว',
        'referred' => 'ส่งต่อ/ประสานหน่วยงาน',
    ],

    'outcome_types' => [
        'graduated' => 'สำเร็จการศึกษา',
        'dropout' => 'ลาออก/ออกกลางคัน',
        'dismissed' => 'พ้นสภาพนักศึกษา',
        'transferred' => 'ย้ายสถานศึกษา/เปลี่ยนเส้นทาง',
        'other' => 'อื่น ๆ',
    ],

    'outcome_reasons' => [
        'low_grade' => 'ผลการเรียนไม่ถึงเกณฑ์',
        'academic_dismissal' => 'พ้นสภาพทางการศึกษา',
        'financial' => 'ปัญหาการเงิน',
        'scholarship' => 'ขาดทุนการศึกษา/ทุนไม่ต่อเนื่อง',
        'work' => 'ต้องทำงาน/ภาระงาน',
        'family' => 'ปัญหาครอบครัว',
        'health' => 'ปัญหาสุขภาพกาย',
        'mental' => 'ปัญหาสภาพจิตใจ/ความเครียด',
        'motivation' => 'ขาดแรงจูงใจ',
        'adaptation' => 'ปรับตัวไม่ได้',
        'wrong_major' => 'คณะ/สาขาไม่เหมาะสม',
        'attendance' => 'ขาดเรียน/ไม่เข้าเรียน',
        'discipline' => 'ปัญหาพฤติกรรม/วินัย',
        'transferred' => 'ย้ายสถานศึกษา/เปลี่ยนหลักสูตร',
        'career_change' => 'เปลี่ยนเส้นทางอาชีพ',
        'lost_contact' => 'ขาดการติดต่อ',
        'other' => 'อื่น ๆ',
    ],

    'post_graduation_statuses' => [
        'employed' => 'มีงานทำ',
        'further_study' => 'ศึกษาต่อ',
        'job_seeking' => 'กำลังหางาน',
        'self_employed' => 'ประกอบอาชีพอิสระ',
        'not_available' => 'ยังไม่มีข้อมูล',
        'other' => 'อื่น ๆ',
    ],
];
