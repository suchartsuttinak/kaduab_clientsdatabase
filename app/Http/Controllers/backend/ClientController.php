<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Contact;
use App\Models\District;
use App\Models\Education;
use App\Models\House;
use App\Models\Income;
use App\Models\Marital;
use App\Models\National;
use App\Models\Occupation;
use App\Models\Problem;
use App\Models\Project;
use App\Models\Province;
use App\Models\Religion;
use App\Models\Status;
use App\Models\SubDistrict;
use App\Models\Target;
use App\Models\Title;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClientController extends Controller
{
    /**
     * ดึง client ที่ผู้ใช้มีสิทธิ์เท่านั้น
     */
    protected function findAuthorizedClient($id): Client
    {
        return Client::forUser(auth()->user())->findOrFail($id);
    }

    protected function isAdminOrExecutive($user): bool
    {
        if (!$user) {
            return false;
        }

        return
            (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'isExecutive') && $user->isExecutive())
            || in_array(($user->role ?? null), ['admin', 'executive'], true);
    }

    /**
     * ดึงรายการรหัสบ้านที่ผู้ใช้มีสิทธิ์
     */
    protected function getAuthorizedHouseIds(): array
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        if ($this->isAdminOrExecutive($user)) {
            return House::query()
                ->pluck('id')
                ->map(static fn ($id) => (int) $id)
                ->all();
        }

        if (method_exists($user, 'houses')) {
            return $user->houses()
                ->pluck('houses.id')
                ->map(static fn ($id) => (int) $id)
                ->all();
        }

        if (!empty($user->house_id)) {
            return [(int) $user->house_id];
        }

        return [];
    }

    /**
     * ดึงบ้านตามสิทธิ์เพื่อใช้ในฟอร์ม
     */
    protected function getAuthorizedHouses(): Collection
    {
        $houseIds = $this->getAuthorizedHouseIds();

        if ($houseIds === []) {
            return collect();
        }

        return House::query()
            ->select(['id', 'house_name'])
            ->whereIn('id', $houseIds)
            ->orderBy('house_name')
            ->get();
    }

    /**
     * ตรวจว่า house_id ที่ส่งมาอยู่ในสิทธิ์ผู้ใช้หรือไม่
     */
    protected function ensureAuthorizedHouseId($houseId): void
    {
        abort_unless(
            in_array((int) $houseId, $this->getAuthorizedHouseIds(), true),
            Response::HTTP_FORBIDDEN,
            'คุณไม่มีสิทธิ์เข้าถึงบ้านนี้'
        );
    }

    /**
     * บันทึกและลดขนาดรูปผู้รับบริการสำหรับ Shared Hosting
     */
    protected function saveClientImage($file): string
    {
        $destinationPath = storage_path('app/private/client_images');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $filename = Str::uuid()->toString() . '.jpg';
        $manager = new ImageManager(new Driver());

        $image = $manager
            ->read($file->getRealPath())
            ->orient()
            ->scaleDown(width: 1000);

        $image
            ->toJpeg(quality: 70, progressive: true)
            ->save($destinationPath . DIRECTORY_SEPARATOR . $filename);

        // CLIENT_IMAGE_THUMBNAIL_ON_UPLOAD_V5
        $this->ensureClientListThumbnail($filename);

        return $filename;
    }

    protected function deleteClientImage(?string $filename): void
    {
        if (empty($filename)) {
            return;
        }

        $safeFilename = basename($filename);
        $paths = [
            storage_path('app/private/client_images/' . $safeFilename),
            // รองรับไฟล์เก่าก่อนย้ายขึ้น private storage
            public_path('upload/client_images/' . $safeFilename),
            // CLIENT_IMAGE_THUMBNAIL_DELETE_V5
            storage_path('app/private/client_thumbnails/' . sha1($safeFilename) . '.jpg'),
        ];

        foreach ($paths as $path) {
            if (File::isFile($path)) {
                File::delete($path);
            }
        }
    }

    /**
     * CLIENT_IMAGE_BATCH_V5
     * คืน path ของรูปผู้รับบริการ โดยคง private storage เป็นลำดับแรก
     */
    protected function resolveClientImagePath(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }

        $safeFilename = basename((string) $filename);
        $candidates = [
            storage_path('app/private/client_images/' . $safeFilename),
            public_path('upload/client_images/' . $safeFilename),
        ];

        foreach ($candidates as $candidate) {
            if (File::isFile($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * สร้าง thumbnail สำหรับหน้า list เท่านั้น ลด bandwidth/decode cost
     * ไฟล์ต้นฉบับไม่ถูกแก้ไข
     */
    protected function ensureClientListThumbnail(?string $filename): ?string
    {
        $sourcePath = $this->resolveClientImagePath($filename);
        if (!$sourcePath) {
            return null;
        }

        $safeFilename = basename((string) $filename);
        $thumbnailDir = storage_path('app/private/client_thumbnails');
        $thumbnailPath = $thumbnailDir . DIRECTORY_SEPARATOR . sha1($safeFilename) . '.jpg';

        try {
            if (!File::exists($thumbnailDir)) {
                File::makeDirectory($thumbnailDir, 0755, true);
            }

            $sourceMtime = File::lastModified($sourcePath);
            if (File::isFile($thumbnailPath) && File::lastModified($thumbnailPath) >= $sourceMtime) {
                return $thumbnailPath;
            }

            $manager = new ImageManager(new Driver());
            $image = $manager
                ->read($sourcePath)
                ->orient()
                ->scaleDown(width: 96);

            $image
                ->toJpeg(quality: 68, progressive: true)
                ->save($thumbnailPath);

            return File::isFile($thumbnailPath) ? $thumbnailPath : null;
        } catch (Throwable $e) {
            Log::warning('Client thumbnail generation failed', [
                'filename' => $safeFilename,
                'exception' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Batch thumbnail endpoint ใช้ route client.image เดิมด้วย ?batch=1&ids=...
     * ตรวจสิทธิ์ผู้ใช้เพียง query เดียว และไม่ cache response ข้าม session
     */
    protected function clientImageBatch(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(static fn ($id) => trim($id))
            ->filter(static fn ($id) => $id !== '' && ctype_digit($id) && (int) $id > 0)
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->take(100)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json(['images' => []], 200, [
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $clients = Client::forUser(auth()->user())
            ->select(['clients.id', 'clients.image'])
            ->whereIn('clients.id', $ids->all())
            ->get()
            ->keyBy('id');

        $images = [];

        foreach ($ids as $id) {
            $client = $clients->get($id);
            if (!$client || empty($client->image)) {
                continue;
            }

            $thumbnailPath = $this->ensureClientListThumbnail((string) $client->image);
            if (!$thumbnailPath || !File::isFile($thumbnailPath)) {
                continue;
            }

            $bytes = File::get($thumbnailPath);
            if ($bytes === false) {
                continue;
            }

            $images[(string) $id] = 'data:image/jpeg;base64,' . base64_encode($bytes);
        }

        return response()->json(['images' => $images], 200, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
    /**
     * แสดงรูปผู้รับบริการผ่าน route ที่ตรวจสิทธิ์และ scope ของผู้ใช้
     * แทนการเปิดไฟล์ส่วนบุคคลจาก public URL โดยตรง
     */
    public function ClientImage(Request $request, $id)
    {
        // CLIENT_IMAGE_BATCH_ENTRY_V5
        if ($request->boolean('batch')) {
            return $this->clientImageBatch($request);
        }
// CLIENT_IMAGE_CACHE_V3
        // Route รูปยังตรวจสิทธิ์ด้วย forUser() เหมือนเดิม แต่เลือกเฉพาะคอลัมน์ที่จำเป็น
        // เพื่อลด payload/หน่วยความจำของทุก image request
        $client = Client::forUser(auth()->user())
            ->select(['clients.id', 'clients.image'])
            ->findOrFail($id);
        $safeFilename = !empty($client->image)
            ? basename((string) $client->image)
            : '';

        $candidates = [];

        if ($safeFilename !== '') {
            $candidates[] = storage_path('app/private/client_images/' . $safeFilename);
            // Legacy fallback: อ่านไฟล์เดิมได้ แต่ public/.htaccess ของโฟลเดอร์จะกัน direct access
            $candidates[] = public_path('upload/client_images/' . $safeFilename);
        }

        $path = collect($candidates)->first(static fn ($candidate) => File::isFile($candidate));

        if (!$path) {
            $fallback = public_path('upload/no_image.jpg');
            abort_unless(File::isFile($fallback), 404);
            $path = $fallback;
        }

        $mime = File::mimeType($path) ?: 'image/jpeg';
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true), 404);

        $lastModified = File::lastModified($path);
        $fileSize = File::size($path);
        $etag = sha1(basename($path) . '|' . $lastModified . '|' . $fileSize);

        $response = response()->file($path, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);

        // รูปยังเป็น private และผ่าน authorization เช่นเดิม
        // อนุญาต Browser cache ระยะสั้นเพื่อลดการโหลดรูปซ้ำทุกครั้งที่ refresh / live search
        // CLIENT_IMAGE_CACHE_V4
        // URL รูปถูก version ด้วยชื่อไฟล์ + session อยู่แล้ว จึงไม่ใช้ Vary: Cookie
        // เพื่อป้องกัน cache miss เมื่อ Cookie อื่นเปลี่ยนระหว่างการใช้งาน
        // private = ไม่ให้ shared/proxy cache เก็บรูปส่วนบุคคล
        // 6 ชั่วโมงครอบคลุมการใช้งานต่อเนื่องในหนึ่งช่วงงาน โดยไม่ต้อง revalidate รูปซ้ำ
        $response->setPrivate();
        $response->setMaxAge(21600);
        $response->headers->addCacheControlDirective('immutable');
        $response->setEtag($etag);
        $response->setLastModified(new \DateTimeImmutable('@' . $lastModified));

        // หลัง cache หมดอายุ Browser ส่ง ETag/Last-Modified กลับมา
        // Laravel จะตอบ 304 เมื่อรูปยังไม่เปลี่ยน ลดทั้ง bandwidth และเวลาถอดรหัสรูป
        $response->isNotModified($request);

        return $response;
    }

    /**
     * หน้าแสดงรายการผู้รับบริการ
     * ใช้ค้นหาและแบ่งหน้าฝั่งฐานข้อมูล เพื่อรองรับข้อมูลจำนวนมาก
     */
    public function ClientShow(Request $request)
    {
        $user = auth()->user();
        $canFilterProjects = $this->isAdminOrExecutive($user);
        $projectId = $canFilterProjects
            ? $request->input('project_id', 'all')
            : 'all';

        $search = Str::substr(trim((string) $request->input('search', '')), 0, 100);
        $perPage = $this->resolvePerPage($request->input('per_page'));

        $clientsQuery = Client::forUser($user)
            ->select([
                'clients.id',
                'clients.project_id',
                'clients.house_id',
                'clients.register_number',
                'clients.title_id',
                'clients.first_name',
                'clients.last_name',
                'clients.arrival_date',
                'clients.birth_date',
                'clients.image',
                'clients.release_status',
                'clients.created_at',
            ])
            ->with([
                'title:id,title_name',
                'problems' => static function ($query) {
                    $query->select(['problems.id', 'problems.problem_name'])
                        ->orderBy('problems.problem_name');
                },
            ])
            ->where(function ($query) {
                $query
                    ->where('release_status', 'show')
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->where('release_status', 'pending_refer')
                            ->whereHas('refers', static function ($referQuery) {
                                $referQuery->where('approve_status', 'pending');
                            });
                    });
            });

        $this->applyClientSearch($clientsQuery, $search);

        if ($canFilterProjects && !empty($projectId) && $projectId !== 'all') {
            $clientsQuery->where('project_id', (int) $projectId);
        }

        $clients = $clientsQuery
            ->latest('clients.created_at')
            ->paginate($perPage)
            ->withQueryString();

        $projects = $canFilterProjects
            ? Project::query()
                ->select(['id', 'project_name'])
                ->orderBy('project_name')
                ->get()
            : collect();

        return view('backend.client.client_show', compact(
            'clients',
            'projects',
            'projectId',
            'canFilterProjects',
            'search',
            'perPage'
        ));
    }

    /**
     * หน้าเมนูหลักของผู้รับบริการ
     * คง eager loading เดิมไว้เพื่อไม่กระทบส่วนย่อยที่อาจเรียก relation จาก layout
     */
    public function ClientIndex($id)
    {
        $client = Client::forUser(auth()->user())
            ->with([
                'educationRecords',
                'problems',
                'house',
                'title',
                'national',
                'religion',
                'marital',
                'occupation',
                'income',
                'education',
                'contact',
                'status',
                'project',
                'target',
                'province',
                'district',
                'sub_district',
                'originProvince',
                'originDistrict',
                'originSubDistrict',
                'father',
                'mother',
                'spouse',
                'relative',
                'members',
                'files',
                'vaccinations',
                'refers',
            ])
            ->findOrFail($id);

        return view('admin_client.index.client_index', compact('client'));
    }

    public function ClientAdd()
    {
        $formOptions = $this->getClientFormOptions();

        $currentProvinceId = old('province_id');
        $currentDistrictId = old('district_id');
        $originProvinceId = old('origin_province_id');
        $originDistrictId = old('origin_district_id');

        $provinces = $this->provinceOptions();
        $districts = $this->districtOptions($currentProvinceId);
        $sub_districts = $this->subDistrictOptions($currentDistrictId);

        // ใช้ collection จังหวัดชุดเดียวกัน ลด query ซ้ำ
        $origin_provinces = $provinces;
        $origin_districts = $this->districtOptions($originProvinceId);
        $origin_sub_districts = $this->subDistrictOptions($originDistrictId);

        $houses = $this->getAuthorizedHouses();

        return view('backend.client.client_add', array_merge(
            $formOptions,
            compact(
                'provinces',
                'districts',
                'sub_districts',
                'houses',
                'origin_provinces',
                'origin_districts',
                'origin_sub_districts'
            )
        ));
    }

    public function getDistricts($province_id)
    {
        return response()->json(
            $this->districtOptions($province_id)->values()
        );
    }

    public function getSubdistricts($district_id)
    {
        return response()->json(
            $this->subDistrictOptions($district_id)->values()
        );
    }

    public function getZipcode($subdistrict_id)
    {
        $zipcode = SubDistrict::query()
            ->whereKey((int) $subdistrict_id)
            ->value('zipcode');

        return response()->json(['zipcode' => $zipcode]);
    }

    public function getOriginDistricts($province_id)
    {
        return $this->getDistricts($province_id);
    }

    public function getOriginSubdistricts($district_id)
    {
        return $this->getSubdistricts($district_id);
    }

    public function getOriginZipcode($subdistrict_id)
    {
        $zipcode = SubDistrict::query()
            ->whereKey((int) $subdistrict_id)
            ->value('zipcode');

        return response()->json([
            'origin_zipcode' => $zipcode,
            'zipcode' => $zipcode,
        ]);
    }

    public function ClientStore(Request $request)
    {
        $validated = $request->validate(
            $this->clientValidationRules(),
            $this->clientValidationMessages()
        );

        $validated = $this->forceAuthorizedProject($validated);
        $this->ensureAuthorizedHouseId($validated['house_id']);
        $this->validateTitleForClient($validated);

        $problems = array_values(array_unique($validated['problems'] ?? []));
        unset($validated['problems']);

        $validated['release_status'] = 'show';
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $this->saveClientImage($request->file('image'));
                $validated['image'] = $newImage;
            }

            $client = DB::transaction(function () use ($validated, $problems) {
                $client = Client::create($validated);

                if ($problems !== []) {
                    $client->problems()->sync($problems);
                }

                CaseActivity::record([
                    'client_id' => $client->id,
                    'module' => 'client',
                    'type' => 'success',
                    'title' => 'รับผู้รับบริการเข้าสู่ระบบ',
                    'description' =>
                        'ชื่อ: ' . $client->first_name . ' ' . $client->last_name
                        . ' | เลขทะเบียน: ' . ($client->register_number ?? '-')
                        . ' | วันที่เข้ารับบริการ: ' . ($validated['arrival_date'] ?? '-'),
                    'occurred_at' => now('Asia/Bangkok'),
                    'icon' => 'bi-person-plus',
                    'url' => route('admin.index', $client->id),
                ]);

                return $client;
            }, 3);
        } catch (Throwable $e) {
            $this->deleteClientImage($newImage);
            Log::error('ClientStore failed', [
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }

        return redirect()
            ->route('client.edit', $client->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function ClientEdit(Request $request, $id)
    {
        $client = $this->findAuthorizedClient($id);
        $tab = $request->get('tab', 'profile');
        $allowedTabs = ['profile', 'detail', 'client', 'information', 'family', 'guardian', 'member'];

        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'profile';
        }

        // กำหนดค่าเริ่มต้นทุกตัวแปร ป้องกัน Undefined variable
        $problems = collect();
        $provinces = collect();
        $districts = collect();
        $sub_districts = collect();
        $nations = collect();
        $religions = collect();
        $maritals = collect();
        $occupations = collect();
        $incomes = collect();
        $educations = collect();
        $contacts = collect();
        $projects = collect();
        $statuses = collect();
        $houses = collect();
        $targets = collect();
        $titles = collect();
        $origin_provinces = collect();
        $origin_districts = collect();
        $origin_sub_districts = collect();
        $documents = collect();

        // หน้า profile เท่านั้นที่ใช้ master data ชุดใหญ่
        if (in_array($tab, ['profile', 'detail', 'client', 'information'], true)) {
            $formOptions = $this->getClientFormOptions();

            foreach ($formOptions as $name => $value) {
                ${$name} = $value;
            }

            $houses = $this->getAuthorizedHouses();
            $provinces = $this->provinceOptions();

            $currentProvinceId = old('province_id', $client->province_id);
            $currentDistrictId = old('district_id', $client->district_id);
            $originProvinceId = old('origin_province_id', $client->origin_province_id);
            $originDistrictId = old('origin_district_id', $client->origin_district_id);

            $districts = $this->districtOptions($currentProvinceId);
            $sub_districts = $this->subDistrictOptions($currentDistrictId);

            $origin_provinces = $provinces;
            $origin_districts = $this->districtOptions($originProvinceId);
            $origin_sub_districts = $this->subDistrictOptions($originDistrictId);
        }

        return view('backend.client.client_edit', compact(
            'client',
            'tab',
            'problems',
            'provinces',
            'districts',
            'sub_districts',
            'nations',
            'religions',
            'maritals',
            'occupations',
            'incomes',
            'educations',
            'contacts',
            'projects',
            'statuses',
            'houses',
            'targets',
            'titles',
            'origin_provinces',
            'origin_districts',
            'origin_sub_districts',
            'documents'
        ));
    }

    public function ClientUpdate(Request $request)
    {
        $id = (int) $request->input('id');
        $client = $this->findAuthorizedClient($id);

        $validated = $request->validate(
            $this->clientValidationRules($client->id),
            $this->clientValidationMessages()
        );

        $validated = $this->forceAuthorizedProject($validated);
        $this->ensureAuthorizedHouseId($validated['house_id']);
        $this->validateTitleForClient($validated);

        $problems = array_values(array_unique($validated['problems'] ?? []));
        unset($validated['problems']);

        $validated['release_status'] = 'show';
        $oldImage = $client->image;
        $newImage = null;

        try {
            if ($request->hasFile('image')) {
                $newImage = $this->saveClientImage($request->file('image'));
                $validated['image'] = $newImage;
            }

            DB::transaction(function () use ($client, $validated, $problems) {
                $client->update($validated);
                $client->problems()->sync($problems);

                CaseActivity::query()
                    ->where('client_id', $client->id)
                    ->where('module', 'client')
                    ->delete();

                CaseActivity::record([
                    'client_id' => $client->id,
                    'module' => 'client',
                    'type' => 'success',
                    'title' => 'แก้ไขข้อมูลผู้รับบริการ',
                    'description' =>
                        'ชื่อ: ' . $client->first_name . ' ' . $client->last_name
                        . ' | เลขทะเบียน: ' . ($client->register_number ?? '-'),
                    'occurred_at' => now('Asia/Bangkok'),
                    'icon' => 'bi-person-check',
                    'url' => route('client.edit', $client->id),
                ]);
            }, 3);
        } catch (Throwable $e) {
            $this->deleteClientImage($newImage);
            Log::error('ClientUpdate failed', [
                'client_id' => $client->id,
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถแก้ไขข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
        }

        if ($newImage && $oldImage && $newImage !== $oldImage) {
            $this->deleteClientImage($oldImage);
        }

        return redirect()
            ->back()
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    protected function forceAuthorizedProject(array $validated): array
    {
        $user = auth()->user();

        if (!$user) {
            abort(Response::HTTP_FORBIDDEN, 'กรุณาเข้าสู่ระบบ');
        }

        if ($this->isAdminOrExecutive($user)) {
            return $validated;
        }

        if (empty($user->project_id)) {
            abort(Response::HTTP_FORBIDDEN, 'บัญชีของคุณยังไม่ได้กำหนดหน่วยงาน');
        }

        $validated['project_id'] = (int) $user->project_id;

        return $validated;
    }

    public function ClientDelete($id)
    {
        if ((auth()->user()->role ?? null) !== 'admin') {
            abort(Response::HTTP_FORBIDDEN, 'คุณไม่มีสิทธิ์ลบข้อมูล');
        }

        $client = $this->findAuthorizedClient($id);
        $client->update(['release_status' => 'refer']);

        return redirect()->route('client.show')->with([
            'message' => 'เปลี่ยนสถานะเป็น refer เรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
    }

    public function ClientShowRefer(Request $request)
    {
        $user = auth()->user();
        $projectId = $request->input('project_id', 'all');
        $canFilterProjects = $this->isAdminOrExecutive($user);
        $search = Str::substr(trim((string) $request->input('search', '')), 0, 100);
        $perPage = $this->resolvePerPage($request->input('per_page'));

        if ($canFilterProjects) {
            $filterProjectIds = (!empty($projectId) && $projectId !== 'all')
                ? [(int) $projectId]
                : [];
        } else {
            $filterProjectIds = [];

            if (method_exists($user, 'projects')) {
                $filterProjectIds = $user->projects()
                    ->pluck('projects.id')
                    ->map(static fn ($id) => (int) $id)
                    ->all();
            }

            if ($filterProjectIds === [] && !empty($user->project_id)) {
                $filterProjectIds = [(int) $user->project_id];
            }
        }

        $query = Client::query()
            ->select([
                'clients.id',
                'clients.project_id',
                'clients.register_number',
                'clients.title_id',
                'clients.first_name',
                'clients.last_name',
                'clients.arrival_date',
                'clients.birth_date',
                'clients.image',
                'clients.release_status',
                'clients.created_at',
            ])
            ->with([
                'title:id,title_name',
                'problems' => static function ($problemQuery) {
                    $problemQuery->select(['problems.id', 'problems.problem_name'])
                        ->orderBy('problems.problem_name');
                },
            ])
            ->whereIn('release_status', [
                'show',
                'refer',
                'pending_refer',
                'active',
            ]);

        $this->applyClientSearch($query, $search);

        if ($filterProjectIds !== []) {
            $query->where(function ($q) use ($filterProjectIds) {
                $q->whereIn('project_id', $filterProjectIds)
                    ->orWhereExists(function ($transferQuery) use ($filterProjectIds) {
                        $transferQuery
                            ->select(DB::raw(1))
                            ->from('client_transfers')
                            ->whereColumn('client_transfers.client_id', 'clients.id')
                            ->where(function ($transferProjectQuery) use ($filterProjectIds) {
                                $transferProjectQuery
                                    ->whereIn('from_project_id', $filterProjectIds)
                                    ->orWhereIn('to_project_id', $filterProjectIds);
                            });
                    });
            });
        } elseif (!$canFilterProjects) {
            $query->whereRaw('1 = 0');
        }

        $clients = $query
            ->latest('clients.created_at')
            ->paginate($perPage)
            ->withQueryString();

        $projects = $canFilterProjects
            ? Project::query()
                ->select(['id', 'project_name'])
                ->orderBy('project_name')
                ->get()
            : collect();

        return view('backend.client.client_show_refer', compact(
            'clients',
            'projects',
            'projectId',
            'canFilterProjects',
            'search',
            'perPage'
        ));
    }

    public function changeStatus($id)
    {
        $client = $this->findAuthorizedClient($id);

        if ($client->release_status === 'refer') {
            $client->update(['release_status' => 'show']);
        }

        return redirect()
            ->back()
            ->with('success', 'ปรับสถานะเรียบร้อยแล้ว')
            ->with('alert', 'สถานะถูกเปลี่ยนจาก Refer เป็น Show');
    }

    /**
     * จำกัดจำนวนรายการต่อหน้า ป้องกันการขอข้อมูลจำนวนมากเกินไป
     */
    private function resolvePerPage($value): int
    {
        $allowed = [15, 30, 50, 100];
        $perPage = (int) $value;

        return in_array($perPage, $allowed, true) ? $perPage : 30;
    }

    /**
     * ค้นหาผู้รับบริการจากฐานข้อมูล ไม่โหลดข้อมูลทั้งหมดเข้าหน้าเว็บ
     */
    private function applyClientSearch($query, string $search): void
    {
        if ($search === '') {
            return;
        }

        // Escape wildcard ของ LIKE เพื่อให้ค้นหาเครื่องหมาย % และ _ ได้อย่างถูกต้อง
        $escapedSearch = addcslashes($search, '\\%_');
        $keyword = '%' . $escapedSearch . '%';

        $query->where(function ($searchQuery) use ($keyword) {
            $searchQuery
                ->where('clients.register_number', 'like', $keyword)
                ->orWhere('clients.first_name', 'like', $keyword)
                ->orWhere('clients.last_name', 'like', $keyword)
                ->orWhereRaw(
                    "CONCAT_WS(' ', clients.first_name, clients.last_name) LIKE ?",
                    [$keyword]
                )
                ->orWhereHas('problems', static function ($problemQuery) use ($keyword) {
                    $problemQuery->where('problems.problem_name', 'like', $keyword);
                });
        });
    }

    /**
     * ตัวเลือก master data สำหรับหน้าเพิ่ม/แก้ไข
     */
    private function getClientFormOptions(): array
    {
        $user = auth()->user();

        return [
            'problems' => Problem::query()
                ->select(['id', 'problem_name'])
                ->orderBy('problem_name')
                ->get(),
            'nations' => National::query()
                ->select(['id', 'national_name'])
                ->orderBy('national_name')
                ->get(),
            'religions' => Religion::query()
                ->select(['id', 'religion_name'])
                ->orderBy('religion_name')
                ->get(),
            'maritals' => Marital::query()
                ->select(['id', 'marital_name'])
                ->orderBy('marital_name')
                ->get(),
            'occupations' => Occupation::query()
                ->select(['id', 'occupation_name'])
                ->orderBy('occupation_name')
                ->get(),
            'incomes' => Income::query()
                ->select(['id', 'income_name'])
                ->orderBy('id')
                ->get(),
            'educations' => Education::query()
                ->select(['id', 'education_name'])
                ->orderBy('id')
                ->get(),
            'contacts' => Contact::query()
                ->select(['id', 'contact_name'])
                ->orderBy('contact_name')
                ->get(),
            'projects' => $this->isAdminOrExecutive($user)
                ? Project::query()
                    ->select(['id', 'project_name'])
                    ->orderBy('project_name')
                    ->get()
                : collect(),
            'statuses' => Status::query()
                ->select(['id', 'status_name'])
                ->orderBy('id')
                ->get(),
            'targets' => Target::query()
                ->select(['id', 'target_name'])
                ->orderBy('target_name')
                ->get(),
            'titles' => Title::query()
                ->select(['id', 'title_name'])
                ->orderBy('id')
                ->get(),
        ];
    }

    private function provinceOptions(): Collection
    {
        return Province::query()
            ->select(['id', 'prov_name'])
            ->orderBy('prov_name')
            ->get();
    }

    private function districtOptions($provinceId): Collection
    {
        if (empty($provinceId)) {
            return collect();
        }

        return District::query()
            ->select(['id', 'province_id', 'dist_name'])
            ->where('province_id', (int) $provinceId)
            ->orderBy('dist_name')
            ->get();
    }

    private function subDistrictOptions($districtId): Collection
    {
        if (empty($districtId)) {
            return collect();
        }

        return SubDistrict::query()
            ->select(['id', 'district_id', 'subd_name', 'zipcode'])
            ->where('district_id', (int) $districtId)
            ->orderBy('subd_name')
            ->get();
    }

    private function clientValidationRules(?int $clientId = null): array
    {
        return [
            'register_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(Client::class, 'register_number')->ignore($clientId),
            ],
            'title_id' => ['required', 'integer', Rule::exists(Title::class, 'id')],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'nick_name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'arrival_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'id_card' => [
                'nullable',
                'regex:/^[0-9]{1}-[0-9]{4}-[0-9]{5}-[0-9]{2}-[0-9]{1}$/',
                Rule::unique(Client::class, 'id_card')->ignore($clientId),
            ],
            'national_id' => ['required', 'integer', Rule::exists(National::class, 'id')],
            'religion_id' => ['required', 'integer', Rule::exists(Religion::class, 'id')],
            'marital_id' => ['required', 'integer', Rule::exists(Marital::class, 'id')],
            'occupation_id' => ['required', 'integer', Rule::exists(Occupation::class, 'id')],
            'income_id' => ['required', 'integer', Rule::exists(Income::class, 'id')],
            'education_id' => ['required', 'integer', Rule::exists(Education::class, 'id')],
            'scholl' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'moo' => ['nullable', 'string', 'max:255'],
            'soi' => ['nullable', 'string', 'max:255'],
            'road' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'integer', Rule::exists(Province::class, 'id')],
            'district_id' => ['nullable', 'integer', Rule::exists(District::class, 'id')],
            'sub_district_id' => ['nullable', 'integer', Rule::exists(SubDistrict::class, 'id')],
            'zipcode' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:50'],
            'origin_address' => ['nullable', 'string', 'max:255'],
            'origin_moo' => ['nullable', 'string', 'max:255'],
            'origin_soi' => ['nullable', 'string', 'max:255'],
            'origin_road' => ['nullable', 'string', 'max:255'],
            'origin_village' => ['nullable', 'string', 'max:255'],
            'origin_province_id' => ['nullable', 'integer', Rule::exists(Province::class, 'id')],
            'origin_district_id' => ['nullable', 'integer', Rule::exists(District::class, 'id')],
            'origin_sub_district_id' => ['nullable', 'integer', Rule::exists(SubDistrict::class, 'id')],
            'origin_zipcode' => ['nullable', 'string', 'max:20'],
            'origin_phone' => ['nullable', 'string', 'max:50'],
            'target_id' => ['required', 'integer', Rule::exists(Target::class, 'id')],
            'contact_id' => ['required', 'integer', Rule::exists(Contact::class, 'id')],
            'project_id' => ['required', 'integer', Rule::exists(Project::class, 'id')],
            'house_id' => ['required', 'integer', Rule::exists(House::class, 'id')],
            'status_id' => ['required', 'integer', Rule::exists(Status::class, 'id')],
            'case_resident' => ['required', Rule::in(['Active', 'Inactive'])],
            'problems' => ['nullable', 'array'],
            'problems.*' => ['integer', 'distinct', Rule::exists(Problem::class, 'id')],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];
    }

    private function clientValidationMessages(): array
    {
        return [
            'register_number.unique' => 'เลขทะเบียนนี้ถูกใช้แล้ว',
            'register_number.string' => 'เลขทะเบียนต้องเป็นข้อความ',
            'register_number.max' => 'เลขทะเบียนต้องไม่เกิน 255 ตัวอักษร',
            'id_card.unique' => 'เลขบัตรประชาชนนี้ถูกใช้แล้ว',
            'id_card.regex' => 'เลขบัตรประชาชนต้องอยู่ในรูปแบบ 0-0000-00000-00-0',
            'title_id.required' => 'กรุณาเลือกคำนำหน้า',
            'title_id.exists' => 'ไม่พบคำนำหน้าที่เลือกในระบบ',
            'gender.required' => 'กรุณาเลือกเพศ',
            'gender.in' => 'ข้อมูลเพศไม่ถูกต้อง',
            'first_name.required' => 'กรุณากรอกชื่อ',
            'last_name.required' => 'กรุณากรอกนามสกุล',
            'birth_date.required' => 'กรุณากรอกวันเกิด',
            'birth_date.date' => 'รูปแบบวันเกิดไม่ถูกต้อง',
            'birth_date.before_or_equal' => 'วันเกิดต้องไม่เกินวันปัจจุบัน',
            'arrival_date.required' => 'กรุณากรอกวันที่รับเข้า',
            'arrival_date.date' => 'รูปแบบวันที่รับเข้าไม่ถูกต้อง',
            'arrival_date.before_or_equal' => 'วันที่รับเข้าต้องไม่เกินวันปัจจุบัน',
            'national_id.required' => 'กรุณาเลือกสัญชาติ',
            'religion_id.required' => 'กรุณาเลือกศาสนา',
            'marital_id.required' => 'กรุณาเลือกสถานภาพการสมรส',
            'occupation_id.required' => 'กรุณาเลือกอาชีพ',
            'income_id.required' => 'กรุณาเลือกรายได้เฉลี่ย/เดือน',
            'education_id.required' => 'กรุณาเลือกระดับการศึกษา',
            'target_id.required' => 'กรุณาเลือกกลุ่มเป้าหมาย',
            'contact_id.required' => 'กรุณาเลือกวิธีการติดต่อ',
            'project_id.required' => 'กรุณาเลือกหน่วยงาน',
            'house_id.required' => 'กรุณาเลือกสถานที่พักอาศัย',
            'status_id.required' => 'กรุณาเลือกสถานะผู้เข้ารับ',
            'case_resident.required' => 'กรุณาเลือกสถานะการอยู่อาศัย',
            'case_resident.in' => 'สถานะการอยู่อาศัยไม่ถูกต้อง',
            'problems.*.exists' => 'มีรายการปัญหาบางรายการที่ไม่อยู่ในระบบ',
            'image.image' => 'ไฟล์ที่เลือกต้องเป็นรูปภาพ',
            'image.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, jpg, png, gif หรือ webp',
            'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2MB',
        ];
    }

    /**
     * ตรวจความสัมพันธ์ระหว่างคำนำหน้า เพศ และอายุ
     */
    private function validateTitleForClient(array $validated): void
    {
        $title = Title::query()
            ->select(['id', 'title_name'])
            ->find($validated['title_id']);

        if (!$title) {
            throw ValidationException::withMessages([
                'title_id' => 'ไม่พบคำนำหน้าที่เลือกในระบบ',
            ]);
        }

        $age = Carbon::parse($validated['birth_date'])->age;
        $maleTitles = ['นาย', 'ด.ช.', 'เด็กชาย'];
        $femaleTitles = ['นาง', 'นางสาว', 'ด.ญ.', 'เด็กหญิง'];
        $childTitles = ['ด.ช.', 'เด็กชาย', 'ด.ญ.', 'เด็กหญิง'];
        $adultTitles = ['นาย', 'นาง', 'นางสาว'];

        if ($validated['gender'] === 'male' && !in_array($title->title_name, $maleTitles, true)) {
            throw ValidationException::withMessages([
                'title_id' => 'คำนำหน้าที่เลือกไม่ตรงกับเพศชาย',
            ]);
        }

        if ($validated['gender'] === 'female' && !in_array($title->title_name, $femaleTitles, true)) {
            throw ValidationException::withMessages([
                'title_id' => 'คำนำหน้าที่เลือกไม่ตรงกับเพศหญิง',
            ]);
        }

        if ($age >= 15 && in_array($title->title_name, $childTitles, true)) {
            throw ValidationException::withMessages([
                'title_id' => 'อายุ 15 ปีขึ้นไป ไม่สามารถใช้คำนำหน้า ' . $title->title_name . ' ได้',
            ]);
        }

        if ($age < 15 && in_array($title->title_name, $adultTitles, true)) {
            throw ValidationException::withMessages([
                'title_id' => 'อายุต่ำกว่า 15 ปี ไม่ควรใช้คำนำหน้า ' . $title->title_name,
            ]);
        }
    }
}