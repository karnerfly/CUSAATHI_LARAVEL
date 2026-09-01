<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuditRequest;
use App\Http\Resources\Audit\ActivityDetailResource;
use App\Http\Resources\Audit\ActivityResource;
use Dedoc\Scramble\Attributes\Group;
use OwenIt\Auditing\Models\Audit;

#[Group('Audit Management')]
class AuditController extends Controller
{
    /**
     * Display a listing of the audits.
     */
    public function index(AuditRequest $request)
    {
        $validated = $request->validated();

        $query = Audit::query()->with(['user', 'auditable']);
        $query->select([
            'id',
            'event',
            'actor_type',
            'actor_id',
            'auditable_type',
            'auditable_id',
            'created_at',
            'updated_at',
        ]);

        if (!empty($validated['query'])) {
            $q = $validated['query'];

            $query->whereHas('user', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            });
        }

        if (!empty($validated['event'])) {
            $events = explode(',', $validated['event']);
            $query->whereIn('event', $events);
        }

        if (!empty($validated['actor_type'])) {
            $query->where('actor_type', $validated['actor_type']);
        }

        if (isset($validated['actor_id'])) {
            $query->where('actor_id', $validated['actor_id']);
        }

        if (!empty($validated['auditable_type'])) {
            $query->where('auditable_type', $validated['auditable_type']);
        }

        if (isset($validated['auditable_id'])) {
            $query->where('auditable_id', $validated['auditable_id']);
        }

        if (!empty($validated['from'])) {
            $query->whereDate('created_at', '>=', $validated['from']);
        }

        if (!empty($validated['to'])) {
            $query->whereDate('created_at', '<=', $validated['to']);
        }

        $sort = $validated['sort'] ?? 'created_at';
        $order = $validated['order'] ?? 'desc';
        $per_page = $validated['per_page'] ?? 25;

        $audits = $query->orderBy($sort, $order)->paginate($per_page);

        return ActivityResource::collection($audits);
    }

    /**
     * Display detailed audit.
     */
    public function show(Audit $audit)
    {
        return new ActivityDetailResource($audit);
    }
}
