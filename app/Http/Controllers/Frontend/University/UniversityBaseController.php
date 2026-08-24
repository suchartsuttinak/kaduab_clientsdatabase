<?php

namespace App\Http\Controllers\Frontend\University;

use App\Http\Controllers\Concerns\AuthorizesUniversityTracking;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\UniversityEnrollment;
use App\Models\UniversitySemesterRecord;

abstract class UniversityBaseController extends Controller
{
    use AuthorizesUniversityTracking;

    protected function scopedClient(int $clientId): Client
    {
        return Client::forUser(auth()->user())->findOrFail($clientId);
    }

    protected function scopedEnrollment(int $id): UniversityEnrollment
    {
        return UniversityEnrollment::query()
            ->with('client')
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);
    }

    protected function scopedSemesterRecord(int $id): UniversitySemesterRecord
    {
        return UniversitySemesterRecord::query()
            ->with(['enrollment.client', 'semester', 'educationRecord'])
            ->whereHas('enrollment.client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);
    }
}
