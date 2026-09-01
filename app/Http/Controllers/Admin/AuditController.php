<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexAuditRequest;
use App\Http\Resources\Audit\ActivityDetailResource;
use App\Http\Resources\Audit\ActivityResource;
use Dedoc\Scramble\Attributes\Group;
use OwenIt\Auditing\Models\Audit;

#[Group('Admin Audit Management')]
class AuditController extends Controller
{
    /**
     * Display a listing of the audits.
     */
    public function index(IndexAuditRequest $request)
    {
        $query = Audit::query()
            ->with(['user', 'auditable'])
            ->select([
                'id',
                'event',
                'actor_type',
                'actor_id',
                'auditable_type',
                'auditable_id',
                'created_at',
                'updated_at',
            ]);

        $query->when($request->filled('query'), function ($query) use ($request) {
            $q = $request->query;
            $query->whereHas('user', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                });
            });
        });

        $query->when($request->filled('event'), fn($query) => $query->whereIn('event', explode(',', $request->event)));
        $query->when($request->filled('actor_type'), fn($query) => $query->where('actor_type', $request->actor_type));
        $query->when($request->filled('actor_id'), fn($query) => $query->where('actor_id', $request->actor_id));

        $query->when(
            $request->filled('auditable_type'),
            fn($query) => $query->where('auditable_type', $request->auditable_type),
        );

        $query->when(
            $request->filled('auditable_id'),
            fn($query) => $query->where('auditable_id', $request->auditable_id),
        );

        $query->when($request->filled('from'), fn($query) => $query->whereDate('created_at', '>=', $request->from));
        $query->when($request->filled('to'), fn($query) => $query->whereDate('created_at', '<=', $request->to));
        $query->orderBy($request->string('sort', 'created_at'), $request->string('order', 'desc'));

        $audits = $query->paginate($request->integer('per_page', 25))->withQueryString();

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
