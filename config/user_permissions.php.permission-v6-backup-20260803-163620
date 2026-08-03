<?php

return [
    /*
    |--------------------------------------------------------------------------
    | การกระทำมาตรฐานของสิทธิ์รายฟอร์ม
    |--------------------------------------------------------------------------
    |
    | คีย์เหล่านี้ตรงกับคอลัมน์ can_* ในตาราง user_form_permissions
    |
    */
    'actions' => [
        'view' => 'ดูข้อมูล',
        'create' => 'เพิ่ม/บันทึก',
        'update' => 'แก้ไข',
        'delete' => 'ลบ',
        'print' => 'รายงาน/พิมพ์',
    ],

    /*
    |--------------------------------------------------------------------------
    | หมวดและรายการฟอร์ม
    |--------------------------------------------------------------------------
    |
    | permission key ต้องไม่ซ้ำกัน และไม่ควรเปลี่ยนหลังเริ่มใช้งานจริง
    | เพราะคีย์ถูกบันทึกไว้ในฐานข้อมูล
    |
    */
    'groups' => [
        'registration' => [
            'label' => '1. ทะเบียนแรกเข้า',
            'icon' => 'bi-person-vcard-fill',
            'description' => 'ข้อมูลพื้นฐาน การรับเข้า ครอบครัว เอกสาร และรายงานผู้รับบริการ',
            'items' => [
                'registration_client_profile' => [
                    'label' => '1.1 ประวัติผู้รับบริการ',
                    'description' => 'ข้อมูลทะเบียนและข้อมูลประจำตัวผู้รับบริการ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_factfinding' => [
                    'label' => '1.2 สอบข้อเท็จจริงเบื้องต้น',
                    'description' => 'แบบสอบข้อเท็จจริงและเอกสารประกอบ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_family' => [
                    'label' => '1.3 บันทึกข้อมูลครอบครัว',
                    'description' => 'ข้อมูลบิดา มารดา ผู้ปกครอง และสภาพครอบครัว',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_family_visit' => [
                    'label' => '1.4 เยี่ยมครอบครัว',
                    'description' => 'บันทึกการเยี่ยมบ้านและภาพประกอบ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_family_members' => [
                    'label' => '1.5 บันทึกสมาชิกครอบครัว',
                    'description' => 'รายชื่อและรายละเอียดสมาชิกในครอบครัว',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_client_files' => [
                    'label' => '1.6 จัดเก็บไฟล์เอกสาร',
                    'description' => 'ดู อัปโหลด ดาวน์โหลด และลบเอกสารแนบ',
                    'actions' => ['view', 'create', 'delete', 'print'],
                ],
                'registration_client_reports' => [
                    'label' => '1.7 รายงานผู้รับบริการ',
                    'description' => 'ดูและพิมพ์รายงานข้อมูลผู้รับบริการ',
                    'actions' => ['view', 'print'],
                ],
            ],
        ],

        'education' => [
            'label' => '2. การศึกษา',
            'icon' => 'bi-mortarboard-fill',
            'description' => 'ผลการเรียน ประวัติการศึกษา การติดตาม และการขาดเรียน',
            'items' => [
                'education_grade_entry' => [
                    'label' => '2.1 บันทึกผลการเรียน',
                    'description' => 'เพิ่มและจัดการข้อมูลผลการเรียนรายภาคเรียน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'education_results' => [
                    'label' => '2.2 แสดงผลการเรียน',
                    'description' => 'ดูประวัติและรายงานผลการเรียน',
                    'actions' => ['view', 'print'],
                ],
                'education_followup' => [
                    'label' => '2.3 ติดตามการศึกษา',
                    'description' => 'ติดตามสถานศึกษา ครู และผลการดำเนินงาน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'education_absence' => [
                    'label' => '2.4 บันทึกการขาดเรียน',
                    'description' => 'บันทึก แก้ไข และรายงานการขาดเรียน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],

        'health' => [
            'label' => '3. สุขภาพ',
            'icon' => 'bi-heart-pulse-fill',
            'description' => 'การบาดเจ็บ การตรวจร่างกาย การรักษา วัคซีน และสุขภาพจิต',
            'items' => [
                'health_accident' => [
                    'label' => '3.1 บันทึกการบาดเจ็บ',
                    'description' => 'เหตุการณ์บาดเจ็บ การรักษา และการนัดหมาย',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_body_check' => [
                    'label' => '3.2 บันทึกการตรวจร่างกาย',
                    'description' => 'ผลการตรวจสภาพร่างกายและบาดแผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_medical' => [
                    'label' => '3.3 บันทึกการรักษาพยาบาล',
                    'description' => 'ประวัติการเจ็บป่วยและการรักษาพยาบาล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_vaccination' => [
                    'label' => '3.4 ประวัติการรับวัคซีน',
                    'description' => 'ข้อมูลวัคซีน เข็มที่ได้รับ และสถานพยาบาล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_psychiatric' => [
                    'label' => '3.5 การวินิจฉัยทางจิตเวช',
                    'description' => 'การวินิจฉัย การรักษา และการนัดหมายทางจิตเวช',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_addictive' => [
                    'label' => '3.6 การตรวจสารเสพติด',
                    'description' => 'ผลตรวจสารเสพติดและการติดตามผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_annual_checkup' => [
                    'label' => '3.7 ตรวจสุขภาพประจำปี',
                    'description' => 'บันทึกผลตรวจสุขภาพประจำปีและรายงานภาพรวม',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],

        'screening' => [
            'label' => '4. แบบคัดกรอง',
            'icon' => 'bi-clipboard2-pulse-fill',
            'description' => 'แบบสังเกตพฤติกรรม สุขภาพจิต และภาวะโภชนาการ',
            'items' => [
                'screening_behavior_four_diseases' => [
                    'label' => '4.1 แบบสังเกตพฤติกรรม 4 โรค',
                    'description' => 'แบบสังเกตพฤติกรรมและการติดตามผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'screening_snap_iv' => [
                    'label' => '4.2 แบบประเมิน SNAP-IV',
                    'description' => 'แบบประเมิน SNAP-IV และรายงานผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'screening_depression' => [
                    'label' => '4.3 แบบคัดกรองภาวะซึมเศร้า',
                    'description' => 'แบบคัดกรองภาวะซึมเศร้าและผลประเมิน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'screening_nutrition' => [
                    'label' => '4.4 แบบประเมินโภชนาการ',
                    'description' => 'ส่วนสูง น้ำหนัก BMI และผลประเมินโภชนาการ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],

        'social_welfare' => [
            'label' => '5. สังคมสงเคราะห์',
            'icon' => 'bi-people-fill',
            'description' => 'พฤติกรรม การหลบหนี การจำหน่าย การช่วยเหลือ และการติดตาม',
            'items' => [
                'welfare_behavior_problem' => [
                    'label' => '5.1 บันทึกปัญหาพฤติกรรม',
                    'description' => 'บันทึกปัญหาพฤติกรรมและการดำเนินการที่เกี่ยวข้อง',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_escape' => [
                    'label' => '5.2 การหลบหนีจากที่พักพิง',
                    'description' => 'เหตุการณ์หลบหนีและการติดตามค้นหา',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_outside_followup' => [
                    'label' => '5.3 การติดตามเด็กที่อยู่ภายนอก',
                    'description' => 'ข้อมูลการส่งต่อ การอยู่ภายนอก และการติดตาม',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_discharge' => [
                    'label' => '5.4 บันทึกการจำหน่าย',
                    'description' => 'การจำหน่าย ส่งต่อ คืนครอบครัว หรือสิ้นสุดการช่วยเหลือ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_job_agency' => [
                    'label' => '5.5 การหางานให้ผู้รับบริการ',
                    'description' => 'ข้อมูลการประสานงานและการจัดหางาน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_help_items' => [
                    'label' => '5.6 ช่วยเหลือสิ่งของเครื่องใช้',
                    'description' => 'การให้ความช่วยเหลือ สิ่งของ และค่าใช้จ่าย',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_followup' => [
                    'label' => '5.7 บันทึกการติดตาม',
                    'description' => 'บันทึกติดตามด้านสังคมสงเคราะห์และรายงาน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_client_activity' => [
                    'label' => '5.8 ความเคลื่อนไหวผู้รับบริการ',
                    'description' => 'กิจกรรมและความเคลื่อนไหวล่าสุดของผู้รับบริการ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_stateless_person' => [
                    'label' => '5.9 บุคคลไร้สัญชาติ',
                    'description' => 'ข้อมูลและการดำเนินงานด้านสถานะบุคคลและสัญชาติ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | เชื่อมชื่อ Route กับสิทธิ์รายฟอร์ม
    |--------------------------------------------------------------------------
    |
    | เรียงรายการที่เฉพาะเจาะจงก่อน wildcard เสมอ
    | Route ที่ไม่อยู่ในรายการนี้จะทำงานตามระบบเดิม
    |
    */
    'route_permissions' => [
        // ทางเข้าแฟ้มผู้รับบริการ: ต้องมีสิทธิ์ดูอย่างน้อยหนึ่งฟอร์ม
        ['routes' => ['client.show', 'admin.index', 'admin.client.overview'], 'permissions' => [
            'registration_client_profile',
            'registration_factfinding',
            'registration_family',
            'registration_family_visit',
            'registration_family_members',
            'registration_client_files',
            'registration_client_reports',
            'education_grade_entry',
            'education_results',
            'education_followup',
            'education_absence',
            'health_accident',
            'health_body_check',
            'health_medical',
            'health_vaccination',
            'health_psychiatric',
            'health_addictive',
            'health_annual_checkup',
            'screening_behavior_four_diseases',
            'screening_snap_iv',
            'screening_depression',
            'screening_nutrition',
            'welfare_behavior_problem',
            'welfare_escape',
            'welfare_outside_followup',
            'welfare_discharge',
            'welfare_job_agency',
            'welfare_help_items',
            'welfare_followup',
            'welfare_client_activity',
            'welfare_stateless_person',
        ], 'action' => 'view'],

        // ประวัติการให้บริการบนหน้าแฟ้มผู้รับบริการ
        ['routes' => ['admin.client.service_logs'], 'permissions' => ['welfare_client_activity'], 'action' => 'view'],

        // 1.1 ประวัติผู้รับบริการ
        ['routes' => ['client.add'], 'permissions' => ['registration_client_profile'], 'action' => 'view'],
        ['routes' => ['client.store'], 'permissions' => ['registration_client_profile'], 'action' => 'create'],
        ['routes' => ['client.edit'], 'permissions' => ['registration_client_profile'], 'action' => 'view'],
        ['routes' => ['client.update', 'client.changeStatus'], 'permissions' => ['registration_client_profile'], 'action' => 'update'],
        ['routes' => ['client.delete'], 'permissions' => ['registration_client_profile'], 'action' => 'delete'],

        // 1.2 สอบข้อเท็จจริง
        ['routes' => ['factfinding.add'], 'permissions' => ['registration_factfinding'], 'action' => 'view'],
        ['routes' => ['factfinding.store'], 'permissions' => ['registration_factfinding'], 'action' => 'create'],
        ['routes' => ['factfinding.edit', 'factfinding.update'], 'permissions' => ['registration_factfinding'], 'action' => 'update'],
        ['routes' => ['factfinding.delete'], 'permissions' => ['registration_factfinding'], 'action' => 'delete'],

        // 1.3 ครอบครัวและการประเมินครอบครัว
        ['routes' => ['family.add', 'estimate.show'], 'permissions' => ['registration_family'], 'action' => 'view'],
        ['routes' => ['family.store', 'estimate.store'], 'permissions' => ['registration_family'], 'action' => 'create'],
        ['routes' => ['estimate.edit', 'estimate.update'], 'permissions' => ['registration_family'], 'action' => 'update'],
        ['routes' => ['estimate.delete'], 'permissions' => ['registration_family'], 'action' => 'delete'],
        ['routes' => ['estimate.report'], 'permissions' => ['registration_family'], 'action' => 'print'],

        // 1.4 เยี่ยมครอบครัว
        ['routes' => ['visitFamily.create'], 'permissions' => ['registration_family_visit'], 'action' => 'view'],
        ['routes' => ['vitsitFamily.store'], 'permissions' => ['registration_family_visit'], 'action' => 'create'],
        ['routes' => ['vitsitFamily.edit', 'vitsitFamily.update', 'image.replace'], 'permissions' => ['registration_family_visit'], 'action' => 'update'],
        ['routes' => ['image.destroy'], 'permissions' => ['registration_family_visit'], 'action' => 'delete'],
        ['routes' => ['vitsitFamily.report'], 'permissions' => ['registration_family_visit'], 'action' => 'print'],

        // 1.5 สมาชิกครอบครัว
        ['routes' => ['member.create', 'member.show'], 'permissions' => ['registration_family_members'], 'action' => 'view'],
        ['routes' => ['member.store'], 'permissions' => ['registration_family_members'], 'action' => 'create'],
        ['routes' => ['member.edit', 'member.update'], 'permissions' => ['registration_family_members'], 'action' => 'update'],
        ['routes' => ['member.delete'], 'permissions' => ['registration_family_members'], 'action' => 'delete'],

        // 1.6 เอกสารแนบ
        ['routes' => ['client_files.index', 'client_files.create', 'client_files.view', 'client_files.download'], 'permissions' => ['registration_client_files'], 'action' => 'view'],
        ['routes' => ['client_files.store'], 'permissions' => ['registration_client_files'], 'action' => 'create'],
        ['routes' => ['client_files.destroy'], 'permissions' => ['registration_client_files'], 'action' => 'delete'],

        // 1.7 รายงานผู้รับบริการ
        ['routes' => ['client.report'], 'permissions' => ['registration_client_reports'], 'action' => 'print'],

        // 2.1 บันทึกผลการเรียน
        ['routes' => ['education_record.add', 'education_record_add'], 'permissions' => ['education_grade_entry'], 'action' => 'view'],
        ['routes' => ['education_record.store', 'education_record_store'], 'permissions' => ['education_grade_entry'], 'action' => 'create'],
        ['routes' => ['education_record.edit', 'education_record_edit', 'education_record.update', 'education_record_update'], 'permissions' => ['education_grade_entry'], 'action' => 'update'],
        ['routes' => ['education_record_delete'], 'permissions' => ['education_grade_entry'], 'action' => 'delete'],

        // 2.2 แสดง/รายงานผลการเรียน
        ['routes' => ['education_record_show'], 'permissions' => ['education_results'], 'action' => 'view'],
        ['routes' => ['education_record.report', 'education_record.report_by_id', 'education_record_report', 'education_record_report_by_id'], 'permissions' => ['education_results'], 'action' => 'print'],

        // 2.3 ติดตามการศึกษา
        ['routes' => ['school_followup_add'], 'permissions' => ['education_followup'], 'action' => 'view'],
        ['routes' => ['school_followup_store'], 'permissions' => ['education_followup'], 'action' => 'create'],
        ['routes' => ['school_followup.edit', 'school_followup.update'], 'permissions' => ['education_followup'], 'action' => 'update'],
        ['routes' => ['school_followup.delete'], 'permissions' => ['education_followup'], 'action' => 'delete'],
        ['routes' => ['school_followup.report', 'school_followup.report.range'], 'permissions' => ['education_followup'], 'action' => 'print'],

        // 2.4 การขาดเรียน
        ['routes' => ['absent.add'], 'permissions' => ['education_absence'], 'action' => 'view'],
        ['routes' => ['absent.store'], 'permissions' => ['education_absence'], 'action' => 'create'],
        ['routes' => ['absent.edit', 'absent.edit.view', 'absent.update'], 'permissions' => ['education_absence'], 'action' => 'update'],
        ['routes' => ['absent.delete'], 'permissions' => ['education_absence'], 'action' => 'delete'],
        ['routes' => ['absent.report', 'absent.report.range'], 'permissions' => ['education_absence'], 'action' => 'print'],

        // 3.1 การบาดเจ็บ
        ['routes' => ['accident.add'], 'permissions' => ['health_accident'], 'action' => 'view'],
        ['routes' => ['accident.store'], 'permissions' => ['health_accident'], 'action' => 'create'],
        ['routes' => ['accident.edit', 'accident.update'], 'permissions' => ['health_accident'], 'action' => 'update'],
        ['routes' => ['accident.delete'], 'permissions' => ['health_accident'], 'action' => 'delete'],
        ['routes' => ['accident.report'], 'permissions' => ['health_accident'], 'action' => 'print'],

        // 3.2 ตรวจร่างกาย
        ['routes' => ['check_body.add'], 'permissions' => ['health_body_check'], 'action' => 'view'],
        ['routes' => ['check_body.store'], 'permissions' => ['health_body_check'], 'action' => 'create'],
        ['routes' => ['check_body.edit', 'check_body.update'], 'permissions' => ['health_body_check'], 'action' => 'update'],
        ['routes' => ['check_body.delete'], 'permissions' => ['health_body_check'], 'action' => 'delete'],
        ['routes' => ['check_body.report'], 'permissions' => ['health_body_check'], 'action' => 'print'],

        // 3.3 รักษาพยาบาล
        ['routes' => ['medical.add', 'medical.json'], 'permissions' => ['health_medical'], 'action' => 'view'],
        ['routes' => ['medical.store'], 'permissions' => ['health_medical'], 'action' => 'create'],
        ['routes' => ['medical.update'], 'permissions' => ['health_medical'], 'action' => 'update'],
        ['routes' => ['medical.delete'], 'permissions' => ['health_medical'], 'action' => 'delete'],
        ['routes' => ['medical.report'], 'permissions' => ['health_medical'], 'action' => 'print'],

        // 3.4 วัคซีน
        ['routes' => ['vaccine.index', 'vaccine.edit'], 'permissions' => ['health_vaccination'], 'action' => 'view'],
        ['routes' => ['vaccine.store'], 'permissions' => ['health_vaccination'], 'action' => 'create'],
        ['routes' => ['vaccine.update'], 'permissions' => ['health_vaccination'], 'action' => 'update'],
        ['routes' => ['vaccine.delete'], 'permissions' => ['health_vaccination'], 'action' => 'delete'],
        ['routes' => ['vaccine.report'], 'permissions' => ['health_vaccination'], 'action' => 'print'],

        // 3.5 จิตเวช
        ['routes' => ['psychiatric.create', 'psychiatric.edit.json'], 'permissions' => ['health_psychiatric'], 'action' => 'view'],
        ['routes' => ['psychiatric.store'], 'permissions' => ['health_psychiatric'], 'action' => 'create'],
        ['routes' => ['psychiatric.update'], 'permissions' => ['health_psychiatric'], 'action' => 'update'],
        ['routes' => ['psychiatric.delete'], 'permissions' => ['health_psychiatric'], 'action' => 'delete'],
        ['routes' => ['psychiatric.report'], 'permissions' => ['health_psychiatric'], 'action' => 'print'],

        // 3.6 สารเสพติด
        ['routes' => ['addictive.create', 'addictive.json'], 'permissions' => ['health_addictive'], 'action' => 'view'],
        ['routes' => ['addictive.store'], 'permissions' => ['health_addictive'], 'action' => 'create'],
        ['routes' => ['addictive.edit', 'addictive.update'], 'permissions' => ['health_addictive'], 'action' => 'update'],
        ['routes' => ['addictive.delete'], 'permissions' => ['health_addictive'], 'action' => 'delete'],
        ['routes' => ['addictive.report', 'addictive.report.all'], 'permissions' => ['health_addictive'], 'action' => 'print'],

        // 3.7 ตรวจสุขภาพประจำปี
        ['routes' => ['healthc_heckups.index', 'healthc_heckups.edit_json'], 'permissions' => ['health_annual_checkup'], 'action' => 'view'],
        ['routes' => ['healthc_heckups.store'], 'permissions' => ['health_annual_checkup'], 'action' => 'create'],
        ['routes' => ['healthc_heckups.update'], 'permissions' => ['health_annual_checkup'], 'action' => 'update'],
        ['routes' => ['healthc_heckups.delete'], 'permissions' => ['health_annual_checkup'], 'action' => 'delete'],
        ['routes' => ['healthc_heckups.report'], 'permissions' => ['health_annual_checkup'], 'action' => 'print'],

        // 4. แบบคัดกรอง
        ['routes' => ['behavior-screenings.index', 'behavior-screenings.show', 'behavior-screenings.create'], 'permissions' => ['screening_behavior_four_diseases'], 'action' => 'view'],
        ['routes' => ['behavior-screenings.store'], 'permissions' => ['screening_behavior_four_diseases'], 'action' => 'create'],
        ['routes' => ['behavior-screenings.destroy'], 'permissions' => ['screening_behavior_four_diseases'], 'action' => 'delete'],
        ['routes' => ['behavior-screenings.official-report'], 'permissions' => ['screening_behavior_four_diseases'], 'action' => 'print'],

        ['routes' => ['snap-iv.index', 'snap-iv.show', 'snap-iv.create'], 'permissions' => ['screening_snap_iv'], 'action' => 'view'],
        ['routes' => ['snap-iv.store'], 'permissions' => ['screening_snap_iv'], 'action' => 'create'],
        ['routes' => ['snap-iv.edit', 'snap-iv.update'], 'permissions' => ['screening_snap_iv'], 'action' => 'update'],
        ['routes' => ['snap-iv.destroy'], 'permissions' => ['screening_snap_iv'], 'action' => 'delete'],
        ['routes' => ['snap-iv.official-report'], 'permissions' => ['screening_snap_iv'], 'action' => 'print'],

        ['routes' => ['depression-screenings.index', 'depression-screenings.show', 'depression-screenings.create'], 'permissions' => ['screening_depression'], 'action' => 'view'],
        ['routes' => ['depression-screenings.store'], 'permissions' => ['screening_depression'], 'action' => 'create'],
        ['routes' => ['depression-screenings.destroy'], 'permissions' => ['screening_depression'], 'action' => 'delete'],
        ['routes' => ['depression-screenings.official-report'], 'permissions' => ['screening_depression'], 'action' => 'print'],

        ['routes' => ['nutrition_assessments.index', 'nutrition_assessments.show', 'nutrition_assessments.create'], 'permissions' => ['screening_nutrition'], 'action' => 'view'],
        ['routes' => ['nutrition_assessments.store'], 'permissions' => ['screening_nutrition'], 'action' => 'create'],
        ['routes' => ['nutrition_assessments.edit', 'nutrition_assessments.update'], 'permissions' => ['screening_nutrition'], 'action' => 'update'],
        ['routes' => ['nutrition_assessments.destroy'], 'permissions' => ['screening_nutrition'], 'action' => 'delete'],

        // 5.1 ปัญหาพฤติกรรม
        ['routes' => ['observe.create'], 'permissions' => ['welfare_behavior_problem'], 'action' => 'view'],
        ['routes' => ['observe.store', 'observe.followup.store'], 'permissions' => ['welfare_behavior_problem'], 'action' => 'create'],
        ['routes' => ['observe.edit', 'observe.update', 'observe.followup.edit', 'observe.followup.update'], 'permissions' => ['welfare_behavior_problem'], 'action' => 'update'],
        ['routes' => ['observe.delete', 'observe.followup.delete'], 'permissions' => ['welfare_behavior_problem'], 'action' => 'delete'],
        ['routes' => ['observe.report'], 'permissions' => ['welfare_behavior_problem'], 'action' => 'print'],

        // 5.2 หลบหนี
        ['routes' => ['escape.index', 'escape.add'], 'permissions' => ['welfare_escape'], 'action' => 'view'],
        ['routes' => ['escape.store', 'escape_follows.store'], 'permissions' => ['welfare_escape'], 'action' => 'create'],
        ['routes' => ['escape.edit', 'escape.update', 'escape.copy', 'escape_follows.update'], 'permissions' => ['welfare_escape'], 'action' => 'update'],
        ['routes' => ['escape.delete', 'escape_follows.delete'], 'permissions' => ['welfare_escape'], 'action' => 'delete'],
        ['routes' => ['escape.report'], 'permissions' => ['welfare_escape'], 'action' => 'print'],

        // 5.3 เด็กภายนอก
        ['routes' => ['case_outside.show', 'case_outside.filter'], 'permissions' => ['welfare_outside_followup'], 'action' => 'view'],
        ['routes' => ['case_outside.store'], 'permissions' => ['welfare_outside_followup'], 'action' => 'create'],
        ['routes' => ['case_outside.update'], 'permissions' => ['welfare_outside_followup'], 'action' => 'update'],
        ['routes' => ['case_outside.delete'], 'permissions' => ['welfare_outside_followup'], 'action' => 'delete'],
        ['routes' => ['case_outside.report'], 'permissions' => ['welfare_outside_followup'], 'action' => 'print'],

        // 5.4 จำหน่าย
        ['routes' => ['refers.index', 'refers.all'], 'permissions' => ['welfare_discharge'], 'action' => 'view'],
        ['routes' => ['refers.store'], 'permissions' => ['welfare_discharge'], 'action' => 'create'],
        ['routes' => ['refers.approve', 'refers.restore'], 'permissions' => ['welfare_discharge'], 'action' => 'update'],
        ['routes' => ['refers.report'], 'permissions' => ['welfare_discharge'], 'action' => 'print'],

        // 5.5 หางาน
        ['routes' => ['job_agencies.show'], 'permissions' => ['welfare_job_agency'], 'action' => 'view'],
        ['routes' => ['job_agencies.store'], 'permissions' => ['welfare_job_agency'], 'action' => 'create'],
        ['routes' => ['job_agencies.update'], 'permissions' => ['welfare_job_agency'], 'action' => 'update'],
        ['routes' => ['job_agencies.delete'], 'permissions' => ['welfare_job_agency'], 'action' => 'delete'],
        ['routes' => ['job_agencies.report'], 'permissions' => ['welfare_job_agency'], 'action' => 'print'],

        // 5.6 สิ่งของเครื่องใช้
        ['routes' => ['help_sessions.show', 'help_sessions.create'], 'permissions' => ['welfare_help_items'], 'action' => 'view'],
        ['routes' => ['help_sessions.store'], 'permissions' => ['welfare_help_items'], 'action' => 'create'],
        ['routes' => ['help_sessions.edit', 'help_sessions.update'], 'permissions' => ['welfare_help_items'], 'action' => 'update'],
        ['routes' => ['help_sessions.destroy'], 'permissions' => ['welfare_help_items'], 'action' => 'delete'],
        ['routes' => ['help_sessions.report', 'help_sessions.report_range'], 'permissions' => ['welfare_help_items'], 'action' => 'print'],

        // 5.7 ติดตามผล
        ['routes' => ['followup.index'], 'permissions' => ['welfare_followup'], 'action' => 'view'],
        ['routes' => ['followup.store'], 'permissions' => ['welfare_followup'], 'action' => 'create'],
        ['routes' => ['followup.update'], 'permissions' => ['welfare_followup'], 'action' => 'update'],
        ['routes' => ['followup.delete'], 'permissions' => ['welfare_followup'], 'action' => 'delete'],
        ['routes' => ['followup.report', 'followup.pdf', 'followup.report_item'], 'permissions' => ['welfare_followup'], 'action' => 'print'],

        // 5.8 ความเคลื่อนไหว
        ['routes' => ['case-activities.index'], 'permissions' => ['welfare_client_activity'], 'action' => 'view'],
        ['routes' => ['case-activities.report'], 'permissions' => ['welfare_client_activity'], 'action' => 'print'],

        // 5.9 บุคคลไร้สัญชาติ
        ['routes' => ['idstation.index', 'idstation.central.index'], 'permissions' => ['welfare_stateless_person'], 'action' => 'view'],
        ['routes' => ['idstation.store'], 'permissions' => ['welfare_stateless_person'], 'action' => 'create'],
        ['routes' => ['idstation.update'], 'permissions' => ['welfare_stateless_person'], 'action' => 'update'],
        ['routes' => ['idstation.destroy'], 'permissions' => ['welfare_stateless_person'], 'action' => 'delete'],
        ['routes' => ['idstation.central.report'], 'permissions' => ['welfare_stateless_person'], 'action' => 'print'],

        // Dashboard สุขภาพของผู้รับบริการ: ต้องมีสิทธิ์ดูอย่างน้อยหนึ่งฟอร์มสุขภาพ
        ['routes' => ['admin.client.health'], 'permissions' => [
            'health_accident', 'health_body_check', 'health_medical', 'health_vaccination',
            'health_psychiatric', 'health_addictive', 'health_annual_checkup',
        ], 'action' => 'view'],
    ],
];
