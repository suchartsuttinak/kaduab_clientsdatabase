<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CaseActivity;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class AuditMutationContext
{
    private const REQUEST_ATTRIBUTE = '_audit_mutation_context';

    /**
     * เก็บบริบทของ Eloquent mutation ภายใน Request ปัจจุบันเท่านั้น
     *
     * ไม่เก็บค่าข้อมูลเดิม/ใหม่ และไม่เขียนฐานข้อมูลในขั้นตอนนี้
     */
    public static function capture(string $action, Model $model): void
    {
        if (
            app()->runningInConsole()
            || !app()->bound('request')
            || $model instanceof AuditLog
            || $model instanceof CaseActivity
        ) {
            return;
        }

        $action = Str::lower(trim($action));

        if (!in_array($action, ['create', 'update', 'delete'], true)) {
            return;
        }

        try {
            /** @var Request $request */
            $request = request();

            $subjectId = $model->getKey();

            if (!is_numeric($subjectId)) {
                return;
            }

            $entries = $request->attributes->get(self::REQUEST_ATTRIBUTE, []);

            if (!is_array($entries)) {
                $entries = [];
            }

            $changedFields = [];

            if ($action === 'update') {
                $changedFields = array_values(array_filter(
                    array_keys($model->getChanges()),
                    static fn (string $field): bool => !in_array(
                        $field,
                        ['created_at', 'updated_at', 'deleted_at'],
                        true
                    )
                ));
            }

            $entries[] = [
                'action' => $action,
                'subject_type' => (string) $model->getMorphClass(),
                'subject_class' => $model::class,
                'subject_id' => (int) $subjectId,
                'client_id' => self::resolveClientId($model),
                'changed_fields' => $changedFields,
            ];

            // ป้องกัน Request ผิดปกติสร้างรายการย่อยจำนวนมากเกินจำเป็น
            if (count($entries) > 40) {
                $entries = array_slice($entries, -40);
            }

            $request->attributes->set(self::REQUEST_ATTRIBUTE, $entries);
        } catch (Throwable) {
            // Context เป็นข้อมูลเสริม ห้ามทำให้ระบบหลักสะดุด
        }
    }

    /**
     * เลือก Model หลักที่สอดคล้องกับ Route/Controller มากที่สุด
     * จาก mutation ที่เกิดขึ้นจริงใน Request เดียวกัน
     */
    public static function primary(Request $request, string $action): ?array
    {
        $entries = $request->attributes->get(self::REQUEST_ATTRIBUTE, []);

        if (!is_array($entries) || $entries === []) {
            return null;
        }

        $action = Str::lower(trim($action));

        $entries = array_values(array_filter(
            $entries,
            static fn ($entry): bool =>
                is_array($entry)
                && ($entry['action'] ?? null) === $action
        ));

        if ($entries === []) {
            return null;
        }

        $routeName = Str::lower((string) ($request->route()?->getName() ?? ''));
        $routeKey = Str::of($routeName)
            ->replace(['.', '-', '/'], '_')
            ->toString();

        $controllerClass = (string) ($request->route()?->getControllerClass() ?? '');
        $controllerBase = $controllerClass !== ''
            ? Str::snake(Str::beforeLast(class_basename($controllerClass), 'Controller'))
            : '';

        $best = null;
        $bestScore = PHP_INT_MIN;

        foreach ($entries as $index => $entry) {
            $subjectClass = (string) ($entry['subject_class'] ?? '');
            $subjectBase = $subjectClass !== ''
                ? Str::snake(class_basename($subjectClass))
                : '';

            $score = 0;

            if ($subjectBase !== '' && $controllerBase !== '') {
                if ($subjectBase === $controllerBase) {
                    $score += 120;
                } elseif (
                    Str::contains($controllerBase, $subjectBase)
                    || Str::contains($subjectBase, $controllerBase)
                ) {
                    $score += 70;
                }
            }

            if ($subjectBase !== '' && $routeKey !== '') {
                if (Str::contains($routeKey, $subjectBase)) {
                    $score += 100;
                } else {
                    $routeWords = array_values(array_filter(explode('_', $routeKey)));
                    $subjectWords = array_values(array_filter(explode('_', $subjectBase)));
                    $matchedWords = count(array_intersect($routeWords, $subjectWords));
                    $score += $matchedWords * 15;
                }
            }

            if (!empty($entry['client_id'])) {
                $score += 10;
            }

            // เมื่อคะแนนเท่ากัน ให้ mutation ที่เกิดภายหลังมีสิทธิ์สูงกว่าเล็กน้อย
            $score += $index / 1000;

            if ($score >= $bestScore) {
                $bestScore = $score;
                $best = $entry;
            }
        }

        return is_array($best) ? $best : null;
    }

    /**
     * หา client_id จาก Model โดยไม่เก็บข้อมูลส่วนบุคคลซ้ำใน audit_logs
     */
    private static function resolveClientId(Model $model, int $depth = 0): ?int
    {
        if ($depth > 3) {
            return null;
        }

        if ($model instanceof Client) {
            return is_numeric($model->getKey())
                ? (int) $model->getKey()
                : null;
        }

        $directClientId = $model->getAttribute('client_id');

        if (is_numeric($directClientId) && (int) $directClientId > 0) {
            return (int) $directClientId;
        }

        /*
         * Model ลูกบางชนิดไม่มี client_id โดยตรง แต่ชี้ไปยัง Model แม่
         * รายการนี้จำกัดเฉพาะความสัมพันธ์ที่ใช้จริงในระบบ เพื่อไม่เดา FK ทั่วไป
         */
        $parentKeys = [
            'escape_id',
            'observe_id',
            'help_session_id',
            'behavior_screening_id',
            'depression_screening_id',
            'snap_iv_screening_id',
            'estimate_id',
        ];

        foreach ($parentKeys as $foreignKey) {
            $parentId = $model->getAttribute($foreignKey);

            if (!is_numeric($parentId) || (int) $parentId <= 0) {
                continue;
            }

            $baseName = Str::beforeLast($foreignKey, '_id');
            $parentClass = 'App\\Models\\' . Str::studly(Str::singular($baseName));

            if (!class_exists($parentClass) || !is_subclass_of($parentClass, Model::class)) {
                continue;
            }

            try {
                /** @var Model|null $parent */
                $parent = $parentClass::query()->find((int) $parentId);

                if ($parent) {
                    $resolved = self::resolveClientId($parent, $depth + 1);

                    if ($resolved !== null) {
                        return $resolved;
                    }
                }
            } catch (Throwable) {
                // ความสัมพันธ์นี้เป็นข้อมูลเสริม ข้ามได้โดยไม่กระทบ Request หลัก
            }
        }

        return null;
    }
}
