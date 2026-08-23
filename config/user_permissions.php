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
            'label' => 'ทะเบียนแรกเข้า',
            'icon' => 'bi-person-vcard-fill',
            'description' => 'ข้อมูลพื้นฐาน การรับเข้า ครอบครัว เอกสาร และรายงานผู้รับบริการ',
            'items' => [
                'registration_client_profile' => [
                    'label' => 'ประวัติผู้รับบริการ',
                    'description' => 'ข้อมูลทะเบียนและข้อมูลประจำตัวผู้รับบริการ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_factfinding' => [
                    'label' => 'สอบข้อเท็จจริงเบื้องต้น',
                    'description' => 'แบบสอบข้อเท็จจริงและเอกสารประกอบ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_family' => [
                    'label' => 'บันทึกข้อมูลครอบครัว',
                    'description' => 'ข้อมูลบิดา มารดา ผู้ปกครอง และสภาพครอบครัว',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],                'registration_family_assessment' => [
                    'label' => 'ประเมินครอบครัว',
                    'description' => 'บันทึก แก้ไข ลบ และออกรายงานการประเมินครอบครัว',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_family_visit' => [
                    'label' => 'เยี่ยมครอบครัว',
                    'description' => 'บันทึกการเยี่ยมบ้านและภาพประกอบ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_family_members' => [
                    'label' => 'บันทึกสมาชิกครอบครัว',
                    'description' => 'รายชื่อและรายละเอียดสมาชิกในครอบครัว',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'registration_client_files' => [
                    'label' => 'จัดเก็บไฟล์เอกสาร',
                    'description' => 'ดู อัปโหลด ดาวน์โหลด และลบเอกสารแนบ',
                    'actions' => ['view', 'create', 'delete', 'print'],
                ],
                'registration_client_reports' => [
                    'label' => 'รายงานผู้รับบริการ',
                    'description' => 'ดูและพิมพ์รายงานข้อมูลผู้รับบริการ',
                    'actions' => ['view', 'print'],
                ],
            ],
        ],

        'education' => [
            'label' => 'การศึกษา',
            'icon' => 'bi-mortarboard-fill',
            'description' => 'ผลการเรียน ประวัติการศึกษา การติดตาม และการขาดเรียน',
            'items' => [
                'education_grade_entry' => [
                    'label' => 'บันทึกผลการเรียน',
                    'description' => 'เพิ่มและจัดการข้อมูลผลการเรียนรายภาคเรียน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'education_results' => [
                    'label' => 'แสดงผลการเรียน',
                    'description' => 'ดูประวัติและรายงานผลการเรียน',
                    'actions' => ['view', 'print'],
                ],
                'education_followup' => [
                    'label' => 'ติดตามการศึกษา',
                    'description' => 'ติดตามสถานศึกษา ครู และผลการดำเนินงาน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'education_absence' => [
                    'label' => 'บันทึกการขาดเรียน',
                    'description' => 'บันทึก แก้ไข และรายงานการขาดเรียน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            
                // 2.x เด็กมหาวิทยาลัย / ติดตามระดับอุดมศึกษา
                'education_university' => [
                    'label' => 'เด็กมหาวิทยาลัย',
                    'description' => 'Dashboard ผลการเรียนรายวิชา PDF การติดตาม ความเสี่ยง และผลสำเร็จ/ออกกลางคัน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
],
        ],

        'health' => [
            'label' => 'สุขภาพ',
            'icon' => 'bi-heart-pulse-fill',
            'description' => 'การบาดเจ็บ การตรวจร่างกาย การรักษา วัคซีน และสุขภาพจิต',
            'items' => [
                'health_accident' => [
                    'label' => 'บันทึกการบาดเจ็บ',
                    'description' => 'เหตุการณ์บาดเจ็บ การรักษา และการนัดหมาย',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_body_check' => [
                    'label' => 'บันทึกการตรวจร่างกาย',
                    'description' => 'ผลการตรวจสภาพร่างกายและบาดแผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_treatment_rights' => [
                    'label' => 'สิทธิรักษาพยาบาล',
                    'description' => 'สถานะสิทธิและสถานพยาบาลที่เข้ารับการรักษาเบื้องต้น',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_medical' => [
                    'label' => 'บันทึกการรักษาพยาบาล',
                    'description' => 'ประวัติการเจ็บป่วยและการรักษาพยาบาล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_vaccination' => [
                    'label' => 'ประวัติการรับวัคซีน',
                    'description' => 'ข้อมูลวัคซีน เข็มที่ได้รับ และสถานพยาบาล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_psychiatric' => [
                    'label' => 'การวินิจฉัยทางจิตเวช',
                    'description' => 'การวินิจฉัย การรักษา และการนัดหมายทางจิตเวช',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_addictive' => [
                    'label' => 'การตรวจสารเสพติด',
                    'description' => 'ผลตรวจสารเสพติดและการติดตามผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'health_annual_checkup' => [
                    'label' => 'ตรวจสุขภาพประจำปี',
                    'description' => 'บันทึกผลตรวจสุขภาพประจำปีและรายงานภาพรวม',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],

        'screening' => [
            'label' => 'แบบคัดกรอง',
            'icon' => 'bi-clipboard2-pulse-fill',
            'description' => 'แบบสังเกตพฤติกรรม สุขภาพจิต และภาวะโภชนาการ',
            'items' => [
                'screening_behavior_four_diseases' => [
                    'label' => 'แบบสังเกตพฤติกรรม 4 โรค',
                    'description' => 'แบบสังเกตพฤติกรรมและการติดตามผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'screening_snap_iv' => [
                    'label' => 'แบบประเมิน SNAP-IV',
                    'description' => 'แบบประเมิน SNAP-IV และรายงานผล',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'screening_depression' => [
                    'label' => 'แบบคัดกรองภาวะซึมเศร้า',
                    'description' => 'แบบคัดกรองภาวะซึมเศร้าและผลประเมิน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'screening_nutrition' => [
                    'label' => 'แบบประเมินโภชนาการ',
                    'description' => 'ส่วนสูง น้ำหนัก BMI และผลประเมินโภชนาการ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],

        'individual_development' => [
            'label' => 'พัฒนาและติดตามรายบุคคล',
            'icon' => 'bi-person-up',
            'description' => 'ศูนย์กลางข้อมูลเด็กทุกบ้าน และการวางแผนพัฒนา/ติดตามผลรายบุคคล',
            'items' => [
                'individual_development_center' => [
                    'label' => 'ศูนย์กลางการพัฒนาเด็ก',
                    'description' => 'ข้อมูลกลางระดับองค์กรสำหรับดูเด็กทุกบ้าน แผน เป้าหมาย งานติดตาม และผลล่าสุด — ควรให้เฉพาะผู้ที่ได้รับมอบหมายให้ดูข้อมูลข้ามบ้าน',
                    'actions' => ['view'],
                ],
                'individual_development' => [
                    'label' => 'แผนพัฒนาและติดตามรายบุคคล',
                    'description' => 'จุดแข็ง/ความต้องการ → Baseline → เป้าหมาย → กิจกรรม → ติดตาม → Outcome → รายงาน → ปิดแผน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],

        'social_welfare' => [
            'label' => 'สังคมสงเคราะห์',
            'icon' => 'bi-people-fill',
            'description' => 'พฤติกรรม การหลบหนี การจำหน่าย การช่วยเหลือ และการติดตาม',
            'items' => [
                                'welfare_counseling' => [
                    'label' => 'การให้คำปรึกษา',
                    'description' => 'บันทึกกระบวนการให้คำปรึกษาเป็นครั้งและรอบ พร้อมรายงานรายรอบและรายงานรวม',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
'welfare_behavior_problem' => [
                    'label' => 'บันทึกปัญหาพฤติกรรม',
                    'description' => 'บันทึกปัญหาพฤติกรรมและการดำเนินการที่เกี่ยวข้อง',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_escape' => [
                    'label' => 'การหลบหนีจากที่พักพิง',
                    'description' => 'เหตุการณ์หลบหนีและการติดตามค้นหา',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_outside_followup' => [
                    'label' => 'การติดตามเด็กที่อยู่ภายนอก',
                    'description' => 'ข้อมูลการส่งต่อ การอยู่ภายนอก และการติดตาม',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_discharge' => [
                    'label' => 'บันทึกการจำหน่าย',
                    'description' => 'การจำหน่าย ส่งต่อ คืนครอบครัว หรือสิ้นสุดการช่วยเหลือ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_job_agency' => [
                    'label' => 'การหางานให้ผู้รับบริการ',
                    'description' => 'ข้อมูลการประสานงานและการจัดหางาน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_help_items' => [
                    'label' => 'ช่วยเหลือสิ่งของเครื่องใช้',
                    'description' => 'การให้ความช่วยเหลือ สิ่งของ และค่าใช้จ่าย',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_followup' => [
                    'label' => 'บันทึกการติดตาม',
                    'description' => 'บันทึกติดตามด้านสังคมสงเคราะห์และรายงาน',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_client_activity' => [
                    'label' => 'ความเคลื่อนไหวผู้รับบริการ',
                    'description' => 'กิจกรรมและความเคลื่อนไหวล่าสุดของผู้รับบริการ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
                'welfare_stateless_person' => [
                    'label' => 'บุคคลไร้สัญชาติ',
                    'description' => 'ข้อมูลและการดำเนินงานด้านสถานะบุคคลและสัญชาติ',
                    'actions' => ['view', 'create', 'update', 'delete', 'print'],
                ],
            ],
        ],


        'registration_central' => [
            'label' => 'งานทะเบียนส่วนกลาง',
            'icon' => 'bi-folder2-open',
            'description' => 'ทะเบียนกลาง การย้ายโครงการ และการย้ายสถานที่พักพิง โดยไม่ซ้ำกับทะเบียนผู้รับบริการเดิม',
            'items' => [
                'registration_central_cases' => [
                    'label' => 'ทะเบียนกลางเคสทั้งหมด',
                    'description' => 'ดูทะเบียนกลางของผู้รับบริการตามขอบเขตบทบาท โครงการ และบ้าน',
                    'actions' => ['view', 'print'],
                ],
                'registration_project_transfer' => [
                    'label' => 'ย้ายโครงการ',
                    'description' => 'ดู บันทึก และอนุมัติการย้ายโครงการของผู้รับบริการ',
                    'actions' => ['view', 'create', 'update'],
                ],
                'registration_house_transfer' => [
                    'label' => 'ย้ายสถานที่พักพิง',
                    'description' => 'ดูและบันทึกการย้ายบ้านหรือสถานที่พักพิง',
                    'actions' => ['view', 'update'],
                ],
            ],
        ],

        'dashboard' => [
            'label' => 'หน้ายินดีต้อนรับ',
            'icon' => 'bi-speedometer2',
            'description' => 'ภาพรวมระบบ งานประชาสัมพันธ์ ทุนการศึกษา และรายงานวิเคราะห์ข้อมูลเด็ก',
            'items' => [
                'dashboard_overview' => ['label' => 'หน้าภาพรวม Dashboard', 'description' => 'ดูภาพรวมและสถิติหลักของระบบ หากปิดสิทธิ์ ระบบจะนำผู้ใช้ไปหน้าทะเบียนผู้รับบริการ', 'actions' => ['view', 'print']],
                'dashboard_issues' => ['label' => 'แจ้งเรื่องช่วยเหลือ', 'description' => 'ดูรายการเรื่องที่ประชาชนแจ้งขอรับความช่วยเหลือ', 'actions' => ['view']],
                'dashboard_news' => ['label' => 'จัดการข่าวสาร', 'description' => 'ดูหน้าเพิ่มข่าวและบันทึกข่าวสารประชาสัมพันธ์', 'actions' => ['view', 'create']],
                'dashboard_about' => ['label' => 'ประวัติความเป็นมา', 'description' => 'ดู เพิ่ม และลบข้อมูลประวัติ วัตถุประสงค์ และภารกิจ', 'actions' => ['view', 'create', 'delete']],
                'dashboard_scholarship_sponsors' => ['label' => 'ผู้สนับสนุนทุนการศึกษา', 'description' => 'ดูและบันทึกรายการผู้สนับสนุนและการบริจาคทุน', 'actions' => ['view', 'create', 'update', 'print']],
                'dashboard_scholarship_children' => ['label' => 'ทุนการศึกษาเด็ก', 'description' => 'ดู เพิ่ม แก้ไข ลบ และออกรายงานทุนการศึกษาเด็ก', 'actions' => ['view', 'create', 'update', 'delete', 'print']],
                'dashboard_child_analytics' => ['label' => 'รายงานวิเคราะห์ข้อมูลเด็กตามเงื่อนไข', 'description' => 'ดูและพิมพ์รายงานวิเคราะห์ข้อมูลเด็กตามตัวกรอง', 'actions' => ['view', 'print']],
            ],
        ],

        'master_data' => [
            'label' => 'ประเภทและหมวดหมู่',
            'icon' => 'bi-grid-fill',
            'description' => 'ข้อมูลอ้างอิงส่วนกลางสำหรับใช้เป็นตัวเลือกในฟอร์มต่าง ๆ',
            'items' => [
                'master_institutions' => ['label' => 'รายการสถานศึกษา', 'description' => 'จัดการข้อมูลสถานศึกษา', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_subjects' => ['label' => 'รายการวิชาเรียน', 'description' => 'จัดการข้อมูลวิชาเรียน', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_houses' => ['label' => 'รายการบ้านพัก', 'description' => 'จัดการข้อมูลบ้านและสถานที่พักพิง', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_education_levels' => ['label' => 'รายการระดับการศึกษา', 'description' => 'จัดการระดับการศึกษา', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_semesters' => ['label' => 'รายการปีการศึกษา', 'description' => 'จัดการปีการศึกษาและภาคเรียน', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_psychiatric_diseases' => ['label' => 'รายการโรคทางจิตเวช', 'description' => 'จัดการรายการโรคทางจิตเวช', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_behaviors' => ['label' => 'รายการพฤติกรรม', 'description' => 'จัดการรายการพฤติกรรม', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_outside_types' => ['label' => 'รายการเด็กที่อยู่ภายนอก', 'description' => 'จัดการประเภทการอยู่ภายนอก', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_documents' => ['label' => 'รายการเอกสาร', 'description' => 'จัดการประเภทเอกสาร', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_incomes' => ['label' => 'รายการรายได้', 'description' => 'จัดการประเภทรายได้', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_help_types' => ['label' => 'ประเภทการช่วยเหลือ', 'description' => 'จัดการประเภทการช่วยเหลือ', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_citizenships' => ['label' => 'รายการทางทะเบียน', 'description' => 'จัดการรายการสถานะทางทะเบียน', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_citizen_statuses' => ['label' => 'ได้รับสถานะทางทะเบียน', 'description' => 'จัดการผลการได้รับสถานะทางทะเบียน', 'actions' => ['view', 'create', 'update', 'delete']],
                'master_release_types' => ['label' => 'ประเภทการพ้นอุปการะ', 'description' => 'จัดการประเภทการพ้นอุปการะ', 'actions' => ['view', 'create', 'update', 'delete']],
            ],
        ],

        'central_reports' => [
            'label' => 'รายงานส่วนกลาง',
            'icon' => 'bi-file-earmark-bar-graph-fill',
            'description' => 'รายงานภาพรวมที่แยกจากฟอร์มรายบุคคล',
            'items' => [
                'report_discharge_all' => ['label' => 'รายงานการจำหน่าย', 'description' => 'ดูและพิมพ์รายงานการจำหน่ายรวมทุกผู้รับบริการตามขอบเขตข้อมูล', 'actions' => ['view', 'print']],
            ],
        ],

            'system_management' => [
            'label' => 'การจัดการระบบ',
            'icon' => 'bi-person-gear',
            'description' => 'กำหนดผู้ใช้งาน สิทธิ์การเข้าถึง และตรวจสอบความปลอดภัยของระบบ',

            'items' => [


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        | เป็นข้อมูลประวัติของระบบ
        | อนุญาตเฉพาะการดูเท่านั้น
        |
        | ไม่มี create / update / delete
        |--------------------------------------------------------------------------
        */
        'system_audit_logs' => [
            'label' => 'ประวัติการใช้งานระบบ',
            'description' => 'ตรวจสอบประวัติการเข้าสู่ระบบ การเข้าถึง และเหตุการณ์สำคัญภายในระบบ',
            'actions' => [
                'view',
            ],
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
        /*
        |--------------------------------------------------------------------------
        | พัฒนาและติดตามรายบุคคล (Individual Development)
        |--------------------------------------------------------------------------
        | Permission key: individual_development
        | Lifecycle guard ใน Controller/Service ยังคงเป็นด่านสุดท้ายเสมอ
        */
        ['routes' => [
            'individual-development.center',
        ], 'permissions' => ['individual_development_center'], 'action' => 'view'],

        ['routes' => [
            'individual-development.index',
            'individual-development.timeline',
            'individual-development.baseline.show',
            'individual-development.goals.index',
            'individual-development.followups.show',
            'individual-development.outcomes.index',
            'individual-development.outcomes.show',
        ], 'permissions' => ['individual_development'], 'action' => 'view'],

        ['routes' => [
            'individual-development.create',
            'individual-development.store',
            'individual-development.baseline.create',
            'individual-development.baseline.store',
            'individual-development.goals.create',
            'individual-development.goals.store',
            'individual-development.activities.create',
            'individual-development.activities.store',
            'individual-development.followups.create',
            'individual-development.followups.store',
            'individual-development.outcomes.create',
            'individual-development.outcomes.store',
            'individual-development.coordinations.store',
        ], 'permissions' => ['individual_development'], 'action' => 'create'],

        ['routes' => [
            'individual-development.edit',
            'individual-development.update',
            'individual-development.profile.update',
            'individual-development.support-network.update',
            'individual-development.discharge-plan.update',
            'individual-development.documents.update',
            'individual-development.coordinations.update',
            'individual-development.close.form',
            'individual-development.close',
            'individual-development.cancel',
            'individual-development.baseline.edit',
            'individual-development.baseline.update',
            'individual-development.goals.edit',
            'individual-development.goals.update',
            'individual-development.goals.achieve',
            'individual-development.goals.cancel',
            'individual-development.goals.reopen',
            'individual-development.activities.edit',
            'individual-development.activities.update',
            'individual-development.activities.cancel',
            'individual-development.followups.edit',
            'individual-development.followups.update',
            'individual-development.outcomes.edit',
            'individual-development.outcomes.update',
        ], 'permissions' => ['individual_development'], 'action' => 'update'],

        ['routes' => [
            'individual-development.destroy',
            'individual-development.goals.destroy',
            'individual-development.activities.destroy',
            'individual-development.followups.destroy',
            'individual-development.coordinations.destroy',
        ], 'permissions' => ['individual_development'], 'action' => 'delete'],

        ['routes' => [
            'individual-development.report.hub',
            'individual-development.report.progress',
            'individual-development.report.summary',
            'individual-development.report.show',
            'individual-development.report.pdf',
        ], 'permissions' => ['individual_development'], 'action' => 'print'],
        // สิทธิ์ส่วนกลางเพิ่มเติม
        ['routes' => ['client.cases'], 'permissions' => ['registration_central_cases'], 'action' => 'view'],
        ['routes' => ['client.transfers'], 'permissions' => ['registration_project_transfer'], 'action' => 'view'],
        ['routes' => ['client.transfer.create', 'client.transfer.store'], 'permissions' => ['registration_project_transfer'], 'action' => 'create'],
        ['routes' => ['client.transfer.approve', 'client.transfer.reject'], 'permissions' => ['registration_project_transfer'], 'action' => 'update'],
        ['routes' => ['client-house-transfers.index'], 'permissions' => ['registration_house_transfer'], 'action' => 'view'],
        ['routes' => ['client-house-transfers.update'], 'permissions' => ['registration_house_transfer'], 'action' => 'update'],

        ['routes' => ['dashboard', 'statistics.index'], 'permissions' => ['dashboard_overview'], 'action' => 'view'],
        ['routes' => ['statistics.report'], 'permissions' => ['dashboard_overview'], 'action' => 'print'],
        ['routes' => ['issues.index'], 'permissions' => ['dashboard_issues'], 'action' => 'view'],
        ['routes' => ['news.create'], 'permissions' => ['dashboard_news'], 'action' => 'view'],
        ['routes' => ['news.store'], 'permissions' => ['dashboard_news'], 'action' => 'create'],
        ['routes' => ['landing.about.index'], 'permissions' => ['dashboard_about'], 'action' => 'view'],
        ['routes' => ['landing.about.store'], 'permissions' => ['dashboard_about'], 'action' => 'create'],
        ['routes' => ['landing.about.delete'], 'permissions' => ['dashboard_about'], 'action' => 'delete'],
        ['routes' => ['scholarship.index', 'scholarship.donation.index'], 'permissions' => ['dashboard_scholarship_sponsors'], 'action' => 'view'],
        ['routes' => ['scholarship.donation.create', 'scholarship.donation.store'], 'permissions' => ['dashboard_scholarship_sponsors'], 'action' => 'create'],
        ['routes' => ['scholarship.children.index', 'scholarship.children.photo', 'scholarship.children.attachments.view'], 'permissions' => ['dashboard_scholarship_children'], 'action' => 'view'],
        ['routes' => ['scholarship.children.store', 'scholarship.children.applications.store', 'scholarship.children.expenses.store'], 'permissions' => ['dashboard_scholarship_children'], 'action' => 'create'],
        ['routes' => ['scholarship.children.update', 'scholarship.children.status', 'scholarship.children.expenses.update'], 'permissions' => ['dashboard_scholarship_children'], 'action' => 'update'],
        ['routes' => ['scholarship.children.delete', 'scholarship.children.expenses.destroy'], 'permissions' => ['dashboard_scholarship_children'], 'action' => 'delete'],
        ['routes' => ['scholarship.children.report'], 'permissions' => ['dashboard_scholarship_children'], 'action' => 'print'],
        ['routes' => ['child.analytics.report.index'], 'permissions' => ['dashboard_child_analytics'], 'action' => 'view'],

        ['routes' => ['institution.all'], 'permissions' => ['master_institutions'], 'action' => 'view'],
        ['routes' => ['institution.store'], 'permissions' => ['master_institutions'], 'action' => 'create'],
        ['routes' => ['institution.edit', 'institution.update'], 'permissions' => ['master_institutions'], 'action' => 'update'],
        ['routes' => ['institution.delete'], 'permissions' => ['master_institutions'], 'action' => 'delete'],
        ['routes' => ['subject.show'], 'permissions' => ['master_subjects'], 'action' => 'view'],
        ['routes' => ['subject.store'], 'permissions' => ['master_subjects'], 'action' => 'create'],
        ['routes' => ['subject.edit', 'subject.update'], 'permissions' => ['master_subjects'], 'action' => 'update'],
        ['routes' => ['subject.delete'], 'permissions' => ['master_subjects'], 'action' => 'delete'],
        ['routes' => ['house.show'], 'permissions' => ['master_houses'], 'action' => 'view'],
        ['routes' => ['house.store'], 'permissions' => ['master_houses'], 'action' => 'create'],
        ['routes' => ['house.edit', 'house.update'], 'permissions' => ['master_houses'], 'action' => 'update'],
        ['routes' => ['house.delete'], 'permissions' => ['master_houses'], 'action' => 'delete'],
        ['routes' => ['education.show'], 'permissions' => ['master_education_levels'], 'action' => 'view'],
        ['routes' => ['education.store'], 'permissions' => ['master_education_levels'], 'action' => 'create'],
        ['routes' => ['education.edit', 'education.update'], 'permissions' => ['master_education_levels'], 'action' => 'update'],
        ['routes' => ['education.delete'], 'permissions' => ['master_education_levels'], 'action' => 'delete'],
        ['routes' => ['semester.show'], 'permissions' => ['master_semesters'], 'action' => 'view'],
        ['routes' => ['semester.store'], 'permissions' => ['master_semesters'], 'action' => 'create'],
        ['routes' => ['semester.edit', 'semester.update'], 'permissions' => ['master_semesters'], 'action' => 'update'],
        ['routes' => ['semester.delete'], 'permissions' => ['master_semesters'], 'action' => 'delete'],
        ['routes' => ['psycho.show'], 'permissions' => ['master_psychiatric_diseases'], 'action' => 'view'],
        ['routes' => ['psycho.store'], 'permissions' => ['master_psychiatric_diseases'], 'action' => 'create'],
        ['routes' => ['psycho.edit', 'psycho.update'], 'permissions' => ['master_psychiatric_diseases'], 'action' => 'update'],
        ['routes' => ['psycho.delete'], 'permissions' => ['master_psychiatric_diseases'], 'action' => 'delete'],
        ['routes' => ['misbehavior.show'], 'permissions' => ['master_behaviors'], 'action' => 'view'],
        ['routes' => ['misbehavior.store'], 'permissions' => ['master_behaviors'], 'action' => 'create'],
        ['routes' => ['misbehavior.edit', 'misbehavior.update'], 'permissions' => ['master_behaviors'], 'action' => 'update'],
        ['routes' => ['misbehavior.delete'], 'permissions' => ['master_behaviors'], 'action' => 'delete'],
        ['routes' => ['outside.show'], 'permissions' => ['master_outside_types'], 'action' => 'view'],
        ['routes' => ['outside.store'], 'permissions' => ['master_outside_types'], 'action' => 'create'],
        ['routes' => ['outside.edit', 'outside.update'], 'permissions' => ['master_outside_types'], 'action' => 'update'],
        ['routes' => ['outside.delete'], 'permissions' => ['master_outside_types'], 'action' => 'delete'],
        ['routes' => ['document.show'], 'permissions' => ['master_documents'], 'action' => 'view'],
        ['routes' => ['document.store'], 'permissions' => ['master_documents'], 'action' => 'create'],
        ['routes' => ['document.edit', 'document.update'], 'permissions' => ['master_documents'], 'action' => 'update'],
        ['routes' => ['document.delete'], 'permissions' => ['master_documents'], 'action' => 'delete'],
        ['routes' => ['income.show'], 'permissions' => ['master_incomes'], 'action' => 'view'],
        ['routes' => ['income.store'], 'permissions' => ['master_incomes'], 'action' => 'create'],
        ['routes' => ['income.edit', 'income.update'], 'permissions' => ['master_incomes'], 'action' => 'update'],
        ['routes' => ['income.delete'], 'permissions' => ['master_incomes'], 'action' => 'delete'],
        ['routes' => ['help_type.show'], 'permissions' => ['master_help_types'], 'action' => 'view'],
        ['routes' => ['help_type.store'], 'permissions' => ['master_help_types'], 'action' => 'create'],
        ['routes' => ['help_type.edit', 'help_type.update'], 'permissions' => ['master_help_types'], 'action' => 'update'],
        ['routes' => ['help_type.delete'], 'permissions' => ['master_help_types'], 'action' => 'delete'],
        ['routes' => ['citizenship.show'], 'permissions' => ['master_citizenships'], 'action' => 'view'],
        ['routes' => ['citizenship.store'], 'permissions' => ['master_citizenships'], 'action' => 'create'],
        ['routes' => ['citizenship.edit', 'citizenship.update'], 'permissions' => ['master_citizenships'], 'action' => 'update'],
        ['routes' => ['citizenship.delete'], 'permissions' => ['master_citizenships'], 'action' => 'delete'],
        ['routes' => ['citizen.show'], 'permissions' => ['master_citizen_statuses'], 'action' => 'view'],
        ['routes' => ['citizen.store'], 'permissions' => ['master_citizen_statuses'], 'action' => 'create'],
        ['routes' => ['citizen.edit', 'citizen.update'], 'permissions' => ['master_citizen_statuses'], 'action' => 'update'],
        ['routes' => ['citizen.delete'], 'permissions' => ['master_citizen_statuses'], 'action' => 'delete'],
        ['routes' => ['translate.show'], 'permissions' => ['master_release_types'], 'action' => 'view'],
        ['routes' => ['translate.store'], 'permissions' => ['master_release_types'], 'action' => 'create'],
        ['routes' => ['translate.edit', 'translate.update'], 'permissions' => ['master_release_types'], 'action' => 'update'],
        ['routes' => ['translate.delete'], 'permissions' => ['master_release_types'], 'action' => 'delete'],

        ['routes' => ['refers.all'], 'permissions' => ['report_discharge_all'], 'action' => 'view'],
        // User Management ใช้ role:admin,executive + controller guard โดยตรง
        // จงใจไม่เปิดให้มอบสิทธิ์ส่วนนี้ผ่าน permission checkbox
        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */
        ['routes' => ['audit_logs.index'], 'permissions' => ['system_audit_logs'], 'action' => 'view'],

        // ทางเข้าแฟ้มผู้รับบริการ: ต้องมีสิทธิ์ดูอย่างน้อยหนึ่งฟอร์ม
        ['routes' => ['client.show', 'client.image', 'admin.index', 'admin.client.overview'], 'permissions' => [
            'registration_client_profile',
            'registration_factfinding',
            'registration_family',
            'registration_family_assessment',
            'registration_family_visit',
            'registration_family_members',
            'registration_client_files',
            'registration_client_reports',
            'education_grade_entry',
            'education_results',
            'education_followup',
            'education_absence',
            'education_university',
            'health_accident',
            'health_body_check',
            'health_treatment_rights',
            'health_medical',
            'health_vaccination',
            'health_psychiatric',
            'health_addictive',
            'health_annual_checkup',
            'screening_behavior_four_diseases',
            'screening_snap_iv',
            'screening_depression',
            'screening_nutrition',
            'individual_development',
            'welfare_counseling',
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

        // 1.3 ข้อมูลครอบครัว
        ['routes' => ['family.add'], 'permissions' => ['registration_family'], 'action' => 'view'],
        ['routes' => ['family.store'], 'permissions' => ['registration_family'], 'action' => 'create'],

        // 1.3B ประเมินครอบครัว — แยกสิทธิ์จากข้อมูลครอบครัว
        ['routes' => ['estimate.show', 'estimate.image.view'], 'permissions' => ['registration_family_assessment'], 'action' => 'view'],
        ['routes' => ['estimate.store'], 'permissions' => ['registration_family_assessment'], 'action' => 'create'],
        ['routes' => ['estimate.edit', 'estimate.update'], 'permissions' => ['registration_family_assessment'], 'action' => 'update'],
        ['routes' => ['estimate.delete'], 'permissions' => ['registration_family_assessment'], 'action' => 'delete'],
        ['routes' => ['estimate.report'], 'permissions' => ['registration_family_assessment'], 'action' => 'print'],

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
        ['routes' => ['education_record.edit', 'education_record_edit', 'education_record.update', 'education_record_update', 'education_record_update_legacy'], 'permissions' => ['education_grade_entry'], 'action' => 'update'],
        ['routes' => ['education_record_delete'], 'permissions' => ['education_grade_entry'], 'action' => 'delete'],

        // 2.2 แสดง/รายงานผลการเรียน
        ['routes' => ['education_record_show', 'education_record_show_legacy'], 'permissions' => ['education_results'], 'action' => 'view'],
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

        // 3.3 สิทธิรักษาพยาบาล
        ['routes' => ['healthcare_rights.index'], 'permissions' => ['health_treatment_rights'], 'action' => 'view'],
        ['routes' => ['healthcare_rights.store'], 'permissions' => ['health_treatment_rights'], 'action' => 'create'],
        ['routes' => ['healthcare_rights.edit', 'healthcare_rights.update'], 'permissions' => ['health_treatment_rights'], 'action' => 'update'],
        ['routes' => ['healthcare_rights.destroy'], 'permissions' => ['health_treatment_rights'], 'action' => 'delete'],
        ['routes' => ['healthcare_rights.report'], 'permissions' => ['health_treatment_rights'], 'action' => 'print'],

        // 3.4 รักษาพยาบาล
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
        ['routes' => ['healthc_heckups.index', 'healthc_heckups.edit_json', 'healthc_heckups.document.view'], 'permissions' => ['health_annual_checkup'], 'action' => 'view'],
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
        // การให้คำปรึกษา
        ['routes' => [
            'counseling.index',
            'counseling.show',
        ], 'permissions' => ['welfare_counseling'], 'action' => 'view'],

        ['routes' => [
            'counseling.store',
            'counseling.followup.create',
            'counseling.followup.store',
        ], 'permissions' => ['welfare_counseling'], 'action' => 'create'],

        ['routes' => [
            'counseling.edit',
            'counseling.update',
            'counseling.followup.edit',
            'counseling.followup.update',
        ], 'permissions' => ['welfare_counseling'], 'action' => 'update'],

        ['routes' => [
            'counseling.delete',
            'counseling.followup.delete',
        ], 'permissions' => ['welfare_counseling'], 'action' => 'delete'],

        ['routes' => [
            'counseling.report',
            'counseling.followup.report',
        ], 'permissions' => ['welfare_counseling'], 'action' => 'print'],
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
        ['routes' => ['refers.index'], 'permissions' => ['welfare_discharge'], 'action' => 'view'],
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
            'health_accident', 'health_body_check', 'health_treatment_rights', 'health_medical', 'health_vaccination',
            'health_psychiatric', 'health_addictive', 'health_annual_checkup',
        ], 'action' => 'view'],
    
        // 2.x เด็กมหาวิทยาลัย / ติดตามระดับอุดมศึกษา
        ['routes' => ['university.dashboard', 'university.enrollments.index', 'university.client', 'university.enrollments.show', 'university.semesters.show', 'university.documents.view', 'university.outcomes.form'], 'permissions' => ['education_university'], 'action' => 'view'],
        ['routes' => ['university.enrollments.create', 'university.enrollments.store', 'university.semesters.create', 'university.semesters.store', 'university.followups.create', 'university.followups.store', 'university.outcomes.store', 'university.documents.store'], 'permissions' => ['education_university'], 'action' => 'create'],
        ['routes' => ['university.enrollments.edit', 'university.enrollments.update', 'university.semesters.edit', 'university.semesters.update', 'university.followups.edit', 'university.followups.update', 'university.outcomes.update'], 'permissions' => ['education_university'], 'action' => 'update'],
        ['routes' => ['university.enrollments.destroy', 'university.semesters.destroy', 'university.followups.destroy', 'university.outcomes.destroy', 'university.documents.destroy'], 'permissions' => ['education_university'], 'action' => 'delete'],
        ['routes' => ['university.documents.download', 'university.reports.semester', 'university.reports.enrollment'], 'permissions' => ['education_university'], 'action' => 'print'],
],
];
