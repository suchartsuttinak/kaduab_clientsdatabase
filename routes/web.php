<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\backend\AuditLogController;
use App\Http\Controllers\backend\IdstationCentralController;
use App\Http\Controllers\backend\OperationController;
use App\Http\Controllers\backend\PublicizeController;
use App\Http\Controllers\ChildAnalyticsReportController;
use App\Http\Controllers\ClientAdmin\AdminClientController;
use App\Http\Controllers\Frontend\BehaviorScreeningController;
use App\Http\Controllers\Frontend\CaseActivityController;
use App\Http\Controllers\Frontend\ClientHouseTransferController;
use App\Http\Controllers\Frontend\DepressionScreeningController;
use App\Http\Controllers\Frontend\IdstationController;
use App\Http\Controllers\Frontend\NutritionAssessmentController;
use App\Http\Controllers\Frontend\SnapIvScreeningController;
use App\Http\Controllers\Landing\AboutController;
use App\Http\Controllers\Landing\IssueController;
use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Landing\NewsController;
use App\Http\Controllers\Landing\ScholarshipChildController;
use App\Http\Controllers\Landing\ScholarshipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

       



        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

      Route::prefix('admin/audit-logs')
    ->middleware([
        'auth',
        'prevent-back',
        'form-permissions-explicit',
    ])
    ->group(function () {

        Route::get('/', [AuditLogController::class, 'index'])
            ->name('audit_logs.index');
    });

        /*
        |--------------------------------------------------------------------------
        | Public Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/', [LandingController::class, 'index'])
            ->name('landing.index');

        Route::get('/scholarship-children/public-report', [ScholarshipChildController::class, 'publicReport'])
            ->name('scholarship.children.public_report');

        Route::post('/issues', [IssueController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('issues.store');

        Route::get('/news', [NewsController::class, 'index'])
            ->name('news.index');

        Route::get('/news/{id}', [NewsController::class, 'show'])
            ->whereNumber('id')
            ->name('news.show');

        Route::get('/scholarship/create', [ScholarshipController::class, 'create'])
            ->name('scholarship.create');

        Route::post('/scholarship/store', [ScholarshipController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('scholarship.store');


        /*
        |--------------------------------------------------------------------------
        | Dashboard & Statistics
        |--------------------------------------------------------------------------
        */
        Route::middleware(['auth', 'role:admin,executive,social_worker'])
        ->controller(StatisticsController::class)
            ->group(function () {
                Route::get('/dashboard', 'index')->name('dashboard');

                Route::get('/statistics', 'index')
                    ->name('statistics.index');

                Route::get('/statistics/report', 'report')
                    ->name('statistics.report');
            });


        /*
        |--------------------------------------------------------------------------
        | Landing Management
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth', 'role:admin,executive'])->group(function () {
            Route::get('/issues', [IssueController::class, 'index'])
                ->name('issues.index');

            Route::get('/news/create', [NewsController::class, 'create'])
                ->name('news.create');

            Route::post('/news', [NewsController::class, 'store'])
                ->name('news.store');

            Route::get('/about', [AboutController::class, 'index'])
                ->name('landing.about.index');

            Route::post('/about', [AboutController::class, 'store'])
                ->name('landing.about.store');

            Route::delete('/about/{id}', [AboutController::class, 'destroy'])
                ->name('landing.about.delete');
        });


        /*
        |--------------------------------------------------------------------------
        | Publicize Management
        |--------------------------------------------------------------------------
        */

            Route::middleware([
            'auth',
            'role:admin,executive,social_worker,teacher_caregiver'
        ])
            ->prefix('publicizes')
            ->group(function () {

                Route::get('/', [PublicizeController::class, 'index'])
                    ->name('publicizes.index');

                Route::get('/create', [PublicizeController::class, 'create'])
                    ->name('publicizes.create');

                Route::post('/store', [PublicizeController::class, 'store'])
                    ->name('publicizes.store');

                Route::get('/edit/{publicize}', [PublicizeController::class, 'edit'])
                    ->name('publicizes.edit');

                Route::get('/file/{publicize}', [PublicizeController::class, 'viewFile'])
                    ->name('publicizes.file');

                Route::put('/update/{publicize}', [PublicizeController::class, 'update'])
                    ->name('publicizes.update');

                Route::delete('/delete/{publicize}', [PublicizeController::class, 'destroy'])
                    ->name('publicizes.destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | Scholarship Management
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth', 'role:admin'])->group(function () {
            Route::get('/scholarship', [ScholarshipController::class, 'index'])
                ->name('scholarship.index');

            Route::get('/scholarship/{scholarship}/donation/create', [ScholarshipController::class, 'createDonation'])
                ->whereNumber('scholarship')
                ->name('scholarship.donation.create');

            Route::post('/scholarship/{scholarship}/donation/store', [ScholarshipController::class, 'storeDonation'])
                ->whereNumber('scholarship')
                ->name('scholarship.donation.store');

            Route::get('/scholarship/{scholarship}/donations', [ScholarshipController::class, 'donationIndex'])
                ->whereNumber('scholarship')
                ->name('scholarship.donation.index');
        });


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');

            Route::patch('/profile', [ProfileController::class, 'update'])
                ->name('profile.update');

            Route::delete('/profile', [ProfileController::class, 'destroy'])
                ->name('profile.destroy');

            Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])
                ->name('admin.profile');

            Route::post('/profile/store', [AdminController::class, 'ProfileStore'])
                ->name('profile.store');

            Route::post('/admin/password/update', [AdminController::class, 'PasswordUpdate'])
                ->name('admin.password.update');
        });

        Route::post('/admin/logout', [AdminController::class, 'AdminLogout'])
            ->middleware('auth')
            ->name('admin.logout');


        /*
        |--------------------------------------------------------------------------
        | Admin Client
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth')->group(function () {
            Route::get('/admin/client/{id}', [AdminClientController::class, 'Index'])
                ->whereNumber('id')
                ->name('admin.index');

            Route::get('/client/report/{id}', [AdminClientController::class, 'ClientReport'])
                ->whereNumber('id')
                ->name('client.report');

            Route::get('/admin/client/{id}/overview', [AdminClientController::class, 'Overview'])
                ->whereNumber('id')
                ->name('admin.client.overview');

            Route::get('/admin/client/{id}/service-logs', [AdminClientController::class, 'ServiceLogs'])
                ->whereNumber('id')
                ->name('admin.client.service_logs');

            Route::get('/admin/client/{id}/health', [AdminClientController::class, 'HealthDashboard'])
                ->whereNumber('id')
                ->name('admin.client.health');

            Route::get('/admin/client/{id}/publicize', [AdminClientController::class, 'PublicizeDashboard'])
                ->whereNumber('id')
                ->name('admin.client.publicize');
        });


        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */

       Route::prefix('admin/users')
    ->middleware(['auth', 'prevent-back', 'form-permissions-explicit'])
    ->group(function () {

        Route::get('/', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::get('/create', [UserManagementController::class, 'create'])
            ->name('users.create');

        Route::post('/store', [UserManagementController::class, 'store'])
            ->name('users.store');

        Route::get('/edit/{id}', [UserManagementController::class, 'edit'])
            ->whereNumber('id')
            ->name('users.edit');

        Route::put('/update/{id}', [UserManagementController::class, 'update'])
            ->whereNumber('id')
            ->name('users.update');

        Route::patch('/toggle-status/{id}', [UserManagementController::class, 'toggleStatus'])
            ->whereNumber('id')
            ->name('users.toggle-status');

        Route::delete('/delete/{id}', [UserManagementController::class, 'destroy'])
            ->whereNumber('id')
            ->name('users.destroy');
    });

        /*
        |--------------------------------------------------------------------------
        | Operations
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth'])
            ->prefix('operations')
            ->group(function () {
                Route::get('/', [OperationController::class, 'index'])
                    ->name('operations.index');

                Route::get('/create', [OperationController::class, 'create'])
                    ->name('operations.create');

                Route::post('/store', [OperationController::class, 'store'])
                    ->name('operations.store');

                Route::get('/edit/{id}', [OperationController::class, 'edit'])
                    ->whereNumber('id')
                    ->name('operations.edit');

                Route::put('/update/{id}', [OperationController::class, 'update'])
                    ->whereNumber('id')
                    ->name('operations.update');

                Route::delete('/delete/{id}', [OperationController::class, 'destroy'])
                    ->whereNumber('id')
                    ->name('operations.delete');

                Route::get('/report/daily', [OperationController::class, 'dailyReport'])
                    ->name('operations.report.daily');
            });


        /*
        |--------------------------------------------------------------------------
        | Client House Transfers
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth'])
            ->prefix('client-house-transfers')
            ->group(function () {
                Route::get('/', [ClientHouseTransferController::class, 'index'])
                    ->name('client-house-transfers.index');

                Route::put('/{client}', [ClientHouseTransferController::class, 'update'])
                    ->whereNumber('client')
                    ->name('client-house-transfers.update');
            });


        /*
        |--------------------------------------------------------------------------
        | Behavior Screening
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth'])
            ->prefix('behavior-screenings')
            ->group(function () {
                Route::get('/client/{client}', [BehaviorScreeningController::class, 'index'])
                    ->whereNumber('client')
                    ->name('behavior-screenings.index');

                Route::get('/client/{client}/create', [BehaviorScreeningController::class, 'create'])
                    ->whereNumber('client')
                    ->name('behavior-screenings.create');

                Route::post('/client/{client}', [BehaviorScreeningController::class, 'store'])
                    ->whereNumber('client')
                    ->name('behavior-screenings.store');

                Route::get('/{screening}/official-report', [BehaviorScreeningController::class, 'officialReport'])
                    ->whereNumber('screening')
                    ->name('behavior-screenings.official-report');

                Route::get('/{screening}', [BehaviorScreeningController::class, 'show'])
                    ->whereNumber('screening')
                    ->name('behavior-screenings.show');

                Route::delete('/{screening}', [BehaviorScreeningController::class, 'destroy'])
                    ->whereNumber('screening')
                    ->name('behavior-screenings.destroy');
            });


         /*
        |--------------------------------------------------------------------------
        | SNAP-IV Screening
        |--------------------------------------------------------------------------
        */
        Route::prefix('snap-iv')
            ->middleware(['auth', 'role:admin,executive,social_worker'])
            ->name('snap-iv.')
            ->controller(SnapIvScreeningController::class)
            ->group(function () {
                Route::get('/{client}', 'index')
                    ->whereNumber('client')
                    ->name('index');

                Route::get('/{client}/create', 'create')
                    ->whereNumber('client')
                    ->name('create');

                Route::post('/{client}', 'store')
                    ->whereNumber('client')
                    ->name('store');

                Route::get('/edit/{screening}', 'edit')
                    ->whereNumber('screening')
                    ->name('edit');

                Route::put('/update/{screening}', 'update')
                    ->whereNumber('screening')
                    ->name('update');

                Route::get('/show/{screening}', 'show')
                    ->whereNumber('screening')
                    ->name('show');

                Route::get('/official-report/{screening}', 'officialReport')
                    ->whereNumber('screening')
                    ->name('official-report');

                Route::delete('/{screening}', 'destroy')
                    ->whereNumber('screening')
                    ->name('destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | แบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
        |--------------------------------------------------------------------------
        */


        /*
        |--------------------------------------------------------------------------
        | แบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth'])
            ->prefix('depression-screenings')
            ->group(function () {

                Route::get('/client/{client}', [
                    DepressionScreeningController::class,
                    'index'
                ])
                    ->whereNumber('client')
                    ->name('depression-screenings.index');

                Route::get('/client/{client}/create', [
                    DepressionScreeningController::class,
                    'create'
                ])
                    ->whereNumber('client')
                    ->name('depression-screenings.create');

                Route::post('/client/{client}', [
                    DepressionScreeningController::class,
                    'store'
                ])
                    ->whereNumber('client')
                    ->name('depression-screenings.store');

                Route::get('/{screening}/official-report', [
                    DepressionScreeningController::class,
                    'officialReport'
                ])
                    ->whereNumber('screening')
                    ->name('depression-screenings.official-report');

                Route::get('/{screening}', [
                    DepressionScreeningController::class,
                    'show'
                ])
                    ->whereNumber('screening')
                    ->name('depression-screenings.show');

                Route::delete('/{screening}', [
                    DepressionScreeningController::class,
                    'destroy'
                ])
                    ->whereNumber('screening')
                    ->name('depression-screenings.destroy');

            });


            
        /*
        |--------------------------------------------------------------------------
        | แบบคัดกรองโภชนาการ
        |--------------------------------------------------------------------------
        */
           Route::middleware([
            'auth',
            'role:admin,executive,social_worker,teacher_caregiver'
        ])
        ->prefix('clients/{client}/nutrition-assessments')
        ->name('nutrition_assessments.')
        ->controller(NutritionAssessmentController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('index');

            Route::get('/create', 'create')
                ->name('create');

                Route::post('/', 'store')
                ->name('store');

                Route::get('/{assessment}', 'show')
                ->name('show');

                Route::get('/{assessment}/edit', 'edit')
                ->name('edit');

            Route::put('/{assessment}', 'update')
                ->name('update');

            Route::delete('/{assessment}', 'destroy')
                ->name('destroy');

        });


        /*
        |--------------------------------------------------------------------------
        | Case Activities
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth'])->group(function () {
            Route::get('/admin/client/{client}/case-activities', [CaseActivityController::class, 'index'])
                ->whereNumber('client')
                ->name('case-activities.index');

            Route::get('/admin/client/{client}/case-activities/report', [CaseActivityController::class, 'report'])
                ->whereNumber('client')
                ->name('case-activities.report');
        });


        
       // =========================
        // ID Station
        // บุคคลไม่มีสถานะทางทะเบียน
        // =========================
        Route::middleware(['auth'])->group(function () {

            Route::get('/admin/client/{client}/idstation', [IdstationController::class, 'index'])
                ->name('idstation.index');

            Route::post('/admin/client/{client}/idstation/store', [IdstationController::class, 'store'])
                ->name('idstation.store');

            Route::put('/admin/client/idstation/{idstation}/update', [IdstationController::class, 'update'])
                ->name('idstation.update');

            Route::delete('/admin/client/idstation/{idstation}/delete', [IdstationController::class, 'destroy'])
                ->name('idstation.destroy');


            // =========================
            // ID Station Central Registry
            // ศูนย์กลางบุคคลไม่มีสถานะทางทะเบียน
            // =========================
            Route::get('/admin/idstation-central', [IdstationCentralController::class, 'index'])
                ->name('idstation.central.index');

            Route::get('/admin/idstation-central/report', [IdstationCentralController::class, 'report'])
                ->name('idstation.central.report');

        });

            // รายงานวิเคราะห์ข้อมูลเด็กตามเงื่อนไข
            Route::middleware(['auth', 'role:admin,executive,social_worker'])->group(function () {

                Route::get('/admin/child-analytics-report', [ChildAnalyticsReportController::class, 'index'])
                    ->name('child.analytics.report.index');

            });







        /*
        |--------------------------------------------------------------------------
        | Auth Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/auth.php';


        /*
        |--------------------------------------------------------------------------
        | Backend Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/backend/client.php';
        require __DIR__.'/backend/document.php';
        require __DIR__.'/backend/education.php';
        require __DIR__.'/backend/helpType.php';
        require __DIR__.'/backend/income.php';
        require __DIR__.'/backend/institution.php';
        require __DIR__.'/backend/misbehavior.php';
        require __DIR__.'/backend/outside.php';
        require __DIR__.'/backend/psycho.php';
        require __DIR__.'/backend/semester.php';
        require __DIR__.'/backend/subject.php';
        require __DIR__.'/backend/translate.php';
        require __DIR__.'/backend/scholarshipChild.php';
        require __DIR__.'/backend/citizen.php';
        require __DIR__.'/backend/citizenship.php';
        require __DIR__.'/backend/house.php';
        



        /*
        |--------------------------------------------------------------------------
        | Frontend Routes
        |--------------------------------------------------------------------------
        */

        require __DIR__.'/frontend/absent.php';
        require __DIR__.'/frontend/accident.php';
        require __DIR__.'/frontend/addictive.php';
        require __DIR__.'/frontend/case_outside.php';
        require __DIR__.'/frontend/checkBody.php';
        require __DIR__.'/frontend/EducationRecord.php';
        require __DIR__.'/frontend/escape.php';
        require __DIR__.'/frontend/estimate.php';
        require __DIR__.'/frontend/factfinding.php';
        require __DIR__.'/frontend/family.php';
        require __DIR__.'/frontend/VisitFamily.php';
        require __DIR__.'/frontend/jobAgency.php';
        require __DIR__.'/frontend/medical.php';
        require __DIR__.'/frontend/healthcareRights.php';
        require __DIR__.'/frontend/member.php';
        require __DIR__.'/frontend/observe.php';
        require __DIR__.'/frontend/psychiatric.php';
        require __DIR__.'/frontend/counseling.php';
        require __DIR__.'/frontend/refer.php';
        require __DIR__.'/frontend/SchoolFollowup.php';
        require __DIR__.'/frontend/vaccination.php';
        require __DIR__.'/frontend/HelpSession.php';
        require __DIR__.'/frontend/client_files.php';
        require __DIR__.'/frontend/individual_development.php';
        require __DIR__.'/frontend/followup.php';
        require __DIR__.'/frontend/healthcCheckup.php';