<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\IndexCollegeRequest;
use App\Http\Requests\Admin\College\StoreCollegeImageRequest;
use App\Http\Requests\Admin\College\StoreCollegeLocationRequest;
use App\Http\Requests\Admin\College\StoreCollegeRequest;
use App\Http\Resources\Admin\College\CollegeDetailResource;
use App\Http\Resources\Admin\College\CollegeResource;
use App\Models\College\College;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Management')]
class CollegeController extends Controller
{
    /**
     * Display a listing of the colleges.
     */
    public function index(IndexCollegeRequest $request)
    {
        $query = College::query()->select([
            'id',
            'name',
            'description',
            'type',
            'slug',
            'thumbnail_url',
            'established_year',
            'accreditation_body',
            'accreditation_grade',
            'created_at',
            'updated_at',
        ]);

        $query->when(
            $request->has('deleted'),
            fn($query) => $request->boolean('deleted') ? $query->onlyTrashed() : $query->whereNull('deleted_at'),
        );
        $query->when($request->has('name'), fn($query) => $query->where('name', 'like', "%{$request->name}%"));
        $query->when($request->has('type'), fn($query) => $query->where('type', $request->type));

        $query->when(
            $request->has('established_year'),
            fn($query) => $query->where('established_year', $request->established_year),
        );

        $query->when(
            $request->has('accreditation_grade'),
            fn($query) => $query->where('accreditation_grade', $request->accreditation_grade),
        );

        $query->orderBy('created_at', $request->string('order', 'desc'));
        $colleges = $query->paginate($request->integer('per_page', 25))->withQueryString();

        return CollegeResource::collection($colleges);
    }

    /**
     * Store a newly created college in storage.
     */
    public function store(StoreCollegeRequest $request)
    {
        $validated = $request->validated();
        $college = College::create($validated);

        return response()->json(
            [
                'message' => 'College created.',
                'college' => $college,
            ],
            201,
        );
    }

    /**
     * Display the specified college.
     */
    public function show(College $college)
    {
        $college->load(['location', 'images']);
        return CollegeDetailResource::make($college);
    }

    /**
     * Update the specified college in storage.
     */
    public function update(StoreCollegeRequest $request, College $college)
    {
        $validated = $request->validated();
        $college->update($validated);

        return response()->noContent();
    }

    /**
     * Verify the specified college in storage.
     */
    public function verify(College $college)
    {
        $college->update(['verified_at' => now()]);

        return response()->noContent();
    }

    /**
     * Unverify the specified college in storage.
     */
    public function unverify(College $college)
    {
        $college->update(['verified_at' => null]);

        return response()->noContent();
    }

    /**
     * Remove the specified college from storage.
     */
    public function destroy(College $college)
    {
        $college->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified college from storage.
     */
    public function restore(College $college)
    {
        $college->restore();

        return response()->noContent();
    }

    /**
     * Add the given image to the specified college.
     */
    public function add_image_to_college(StoreCollegeImageRequest $request, College $college)
    {
        $validated = $request->validated();
        $college->images()->create($validated);

        return response()->noContent();
    }

    /**
     * Add the given location to the specified college.
     */
    public function add_location_to_college(StoreCollegeLocationRequest $request, College $college)
    {
        if ($college->location()) {
            return response()->json(
                [
                    'message' => 'Address already exists for this college. Please update the existing address instead.',
                ],
                409,
            );
        }

        $validated = $request->validated();
        $college->location()->create($validated);

        return response()->noContent();
    }
}
