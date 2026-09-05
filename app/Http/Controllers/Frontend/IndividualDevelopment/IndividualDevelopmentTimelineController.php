<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\IndividualDevelopment\IndividualDevelopmentTimelineService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndividualDevelopmentTimelineController extends Controller
{
    public function __construct(private readonly IndividualDevelopmentTimelineService $timeline)
    {
    }

    public function index(Request $request, int $client): View
    {
        $user = auth()->user();
        abort_unless($user && (
            (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && $user->hasFormPermission('individual_development', 'view'))
        ), 403);

        $canViewAcrossHouses = (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && $user->hasFormPermission('individual_development_center', 'view'));
        $clientQuery = Client::forUser($user);
        $clientModel = $clientQuery->with(['house', 'project'])->findOrFail($client);
        $timeline = $this->timeline->forClient($clientModel->id);

        $group = trim((string) $request->query('group', ''));
        if ($group !== '') {
            $timeline = $timeline->where('group', $group)->values();
        }

        return view('frontend.client.individual_development.timeline.index', [
            'client' => $clientModel,
            'timeline' => $timeline,
            'selectedGroup' => $group,
        ]);
    }
}
