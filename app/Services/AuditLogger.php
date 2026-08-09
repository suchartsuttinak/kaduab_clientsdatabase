<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

class AuditLogger
{
    /**
     * ชื่อ attribute ที่ใช้เก็บ request_id ไว้ใน Request เดียวกัน
     */
    private const REQUEST_ID_ATTRIBUTE = '_audit_request_id';

    /**
     * บันทึก Audit Log
     *
     * หมายเหตุ:
     * - ห้ามส่งข้อมูลส่วนบุคคลลง $metadata
     * - changedFields ควรเป็น "ชื่อฟิลด์" เท่านั้น
     */
 public static function log(
    string $action,
    ?string $module = null,
    ?int $clientId = null,
    ?Model $subject = null,
    array $changedFields = [],
    string $result = 'success',
    ?int $statusCode = null,
    array $metadata = [],
    ?int $userId = null,
    ?string $subjectType = null,
    ?int $subjectId = null
): ?AuditLog {
        try {
            $request = request();

            $userAgent = (string) $request->userAgent();

            return AuditLog::create([
                'request_id' => self::requestId(),

               'user_id' => $userId ?? (
                    Auth::check()
                        ? Auth::id()
                        : null
                ),

                'action' => Str::upper(
                    Str::limit(trim($action), 50, '')
                ),

                'module' => filled($module)
                    ? Str::lower(Str::limit(trim($module), 100, ''))
                    : null,

                'client_id' => $clientId,

                'subject_type' => $subject
                    ? Str::limit($subject->getMorphClass(), 150, '')
                    : (filled($subjectType)
                        ? Str::limit(trim((string) $subjectType), 150, '')
                        : null),

                'subject_id' => $subject?->getKey()
                    ?? (is_numeric($subjectId) ? (int) $subjectId : null),

                'route_name' => $request->route()?->getName()
                ?? $request->route()?->uri()
                ?? $request->path(),
                'http_method' => $request->method(),

                'ip_address' => $request->ip(),

                'user_agent_hash' => $userAgent !== ''
                    ? hash('sha256', $userAgent)
                    : null,

                /*
                 * เก็บเฉพาะ "ชื่อฟิลด์"
                 * ไม่เก็บ old value / new value
                 */
                'changed_fields' => self::sanitizeChangedFields(
                    $changedFields
                ),

                'result' => self::normalizeResult($result),

                'status_code' => self::normalizeStatusCode(
                    $statusCode
                ),

                /*
                 * metadata ใช้เฉพาะข้อมูลเชิงเทคนิค
                 * ห้ามใส่ Request ทั้งก้อน
                 */
                'metadata' => self::sanitizeMetadata(
                    $metadata
                ),
            ]);
        } catch (Throwable $e) {
            /*
             * Audit Log ห้ามทำให้ระบบหลักล่ม
             *
             * ถ้าการบันทึก Log มีปัญหา
             * ให้ Laravel บันทึก error ไว้ใน laravel.log
             * แต่ transaction หลักของผู้ใช้ยังทำงานต่อ
             */
            report($e);

            return null;
        }
    }

    /**
     * Request เดียวกันใช้ request_id เดียวกัน
     */
    private static function requestId(): string
    {
        $request = request();

        $existing = $request->attributes->get(
            self::REQUEST_ID_ATTRIBUTE
        );

        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        $requestId = (string) Str::uuid();

        $request->attributes->set(
            self::REQUEST_ID_ATTRIBUTE,
            $requestId
        );

        return $requestId;
    }

    /**
     * เก็บเฉพาะชื่อ field
     */
    private static function sanitizeChangedFields(
        array $fields
    ): ?array {
        $fields = collect($fields)
            ->filter(fn ($field) => is_string($field))
            ->map(fn ($field) => trim($field))
            ->filter()
            ->reject(fn ($field) => self::isSensitiveKey($field))
            ->unique()
            ->values()
            ->take(100)
            ->all();

        return empty($fields)
            ? null
            : $fields;
    }

    /**
     * metadata อนุญาตเฉพาะข้อมูลเทคนิคที่ไม่อ่อนไหว
     */
    private static function sanitizeMetadata(
        array $metadata
    ): ?array {
        $clean = [];

        foreach ($metadata as $key => $value) {
            $key = (string) $key;

            if (
                $key === ''
                || self::isSensitiveKey($key)
            ) {
                continue;
            }

            /*
             * V1 รับเฉพาะ scalar/null
             * ไม่รับ array/object เพื่อป้องกันการส่ง
             * request payload ทั้งก้อนโดยไม่ได้ตั้งใจ
             */
            if (
                !is_null($value)
                && !is_scalar($value)
            ) {
                continue;
            }

            if (is_string($value)) {
                $value = Str::limit($value, 255, '');
            }

            $clean[$key] = $value;

            if (count($clean) >= 30) {
                break;
            }
        }

        return empty($clean)
            ? null
            : $clean;
    }

    /**
     * ชื่อ key ที่ไม่ควรปรากฏใน Audit Log
     */
    private static function isSensitiveKey(
        string $key
    ): bool {
        $key = Str::lower($key);

        $blocked = [
            'password',
            'password_confirmation',
            'token',
            'access_token',
            'refresh_token',
            'authorization',
            'cookie',
            'session',
            'secret',
            'api_key',
            'apikey',
            'remember_token',

            /*
             * ข้อมูลส่วนบุคคลที่ไม่ควรบันทึกซ้ำ
             */
            'citizen_id',
            'id_card',
            'national_id',
            'fullname',
            'first_name',
            'last_name',
            'address',
            'phone',
            'email',

            /*
             * ข้อมูลสุขภาพ/รายละเอียดอ่อนไหว
             */
            'diagnosis',
            'medical',
            'psychiatric',
            'health',
        ];

        foreach ($blocked as $word) {
            if (Str::contains($key, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * กำหนดผลลัพธ์ให้มีรูปแบบเดียวกัน
     */
    private static function normalizeResult(
        string $result
    ): string {
        $result = Str::lower(trim($result));

        return in_array(
            $result,
            ['success', 'failed', 'denied'],
            true
        )
            ? $result
            : 'success';
    }

    /**
     * HTTP status รองรับ 100-599
     */
    private static function normalizeStatusCode(
        ?int $statusCode
    ): ?int {
        if ($statusCode === null) {
            return null;
        }

        return $statusCode >= 100
            && $statusCode <= 599
                ? $statusCode
                : null;
    }
}