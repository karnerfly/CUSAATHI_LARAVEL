<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\IndexCollegeRequest;
use App\Http\Requests\Admin\College\StoreCollegeFacilityRequest;
use App\Http\Requests\Admin\College\StoreCollegeImageRequest;
use App\Http\Requests\Admin\College\StoreCollegeLocationRequest;
use App\Http\Requests\Admin\College\StoreCollegeRequest;
use App\Http\Requests\Admin\College\StoreCollegeStreamRequest;
use App\Http\Requests\Admin\College\UpdateCollegeStreamRequest;
use App\Http\Requests\Admin\College\UploadCollegeThumbnailRequest;
use App\Http\Resources\Admin\College\CollegeDetailResource;
use App\Http\Resources\Admin\College\CollegeResource;
use App\Models\College\College;
use App\Models\College\CollegeStream;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\DB;

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
        $college->load(['location', 'images', 'facilities', 'streams']);
        $college->streams->each(function ($stream) {
            $stream->pivot->load(['fee_structure', 'cutoffs']);
        });

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
     * Upload college thumbnail.
     */
    public function upload_thumbnail(UploadCollegeThumbnailRequest $request, College $college)
    {
        $file = $request->file('file');
        $path = $file->store('college-thumbnails', 'public');

        $college->thumbnail_url = $path;
        $college->save();

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
     * Add the given images to the specified college.
     */
    public function add_images_to_college(StoreCollegeImageRequest $request, College $college)
    {
        $validated = $request->validated();
        $images = $college->images()->createMany($validated);

        return response()->json(
            [
                'message' => 'Images added.',
                'images' => $images,
            ],
            201,
        );
    }

    /**
     * Add the given location to the specified college.
     */
    public function add_location_to_college(StoreCollegeLocationRequest $request, College $college)
    {
        if ($college->location()->exists()) {
            return response()->json(
                [
                    'message' => 'Address already exists for this college. Please update the existing address instead.',
                ],
                409,
            );
        }

        $validated = $request->validated();
        $location = $college->location()->create($validated);

        return response()->json(
            [
                'message' => 'Location added.',
                'location' => $location,
            ],
            201,
        );
    }

    /**
     * Add the given facility to the specified college.
     */
    public function add_facility_to_college(StoreCollegeFacilityRequest $request, College $college)
    {
        $facility_id = $request->input('facility_id');
        if ($college->facilities()->where('facility_id', $facility_id)->exists()) {
            return response()->json(
                [
                    'message' => 'Facility already exists for this college.',
                ],
                409,
            );
        }

        $college->facilities()->attach($facility_id);

        return response()->noContent();
    }

    /**
     * Remove the given facility to the specified college.
     */
    public function remove_facility_from_college(StoreCollegeFacilityRequest $request, College $college)
    {
        $facility_id = $request->input('facility_id');
        if (!$college->facilities()->where('facility_id', $facility_id)->exists()) {
            return response()->json(
                [
                    'message' => 'Facility does not belong to this college.',
                ],
                422,
            );
        }

        $college->facilities()->detach($facility_id);

        return response()->noContent();
    }

    /**
     * Add the given stream to the specified college.
     */
    public function add_stream_to_college(StoreCollegeStreamRequest $request, College $college)
    {
        $validated = $request->validated();

        $college_stream = DB::transaction(function () use ($college, $validated) {
            $college->streams()->attach($validated['stream_id'], [
                'eligibility' => $validated['eligibility'],
                'duration' => $validated['duration'],
            ]);

            $college_stream = CollegeStream::query()
                ->where('college_id', $college->id)
                ->where('stream_id', $validated['stream_id'])
                ->firstOrFail();

            $college_stream->fee_structure()->create($validated['fee_structure']);
            $college_stream->cutoffs()->createMany($validated['cutoffs']);

            return $college_stream;
        });

        return response()->json(
            [
                'message' => 'College stream added.',
                'college_stream' => $college_stream,
            ],
            201,
        );
    }

    /**
     * Add the given cutoffs data to specified college stream.
     */
    // public function add_cutoff_to_college_stream(
    //     StoreCollegeStreamCutoffRequest $request,
    //     CollegeStream $college_stream,
    // ) {
    //     $validated = $request->validated();
    //     $cutoffs = $college_stream->cutoffs()->createMany($validated);

    //     return response()->json(
    //         [
    //             'message' => 'Cutoffs added.',
    //             'cutoffs' => $cutoffs,
    //         ],
    //         201,
    //     );
    // }

    /**
     * Update the given college stream.
     */
    public function update_college_stream(UpdateCollegeStreamRequest $request, CollegeStream $college_stream)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($college_stream, $validated) {
            $college_stream->update([
                'eligibility' => $validated['eligibility'],
                'duration' => $validated['duration'],
            ]);

            $college_stream->fee_structure()->updateOrCreate(
                [
                    'college_stream_id' => $college_stream->id,
                ],
                [
                    'fee_year' => $validated['fee_structure']['fee_year'],
                    'admission_fee' => $validated['fee_structure']['admission_fee'],
                    'total_fee' => $validated['fee_structure']['total_fee'],
                    'verified_at' => $validated['fee_structure']['verified_at'] ?? null,
                ],
            );

            // foreach ($validated['cutoffs'] as $cutoff) {
            //     $college_stream
            //         ->cutoffs()
            //         ->whereKey($cutoff['id'])
            //         ->update([
            //             'category' => $cutoff['category'],
            //             'marks' => $cutoff['marks'],
            //             'published_at' => $cutoff['published_at'] ?? null,
            //         ]);
            // }

            $college_stream->cutoffs()->delete();
            $college_stream->cutoffs()->createMany($validated['cutoffs']);
        });

        return response()->noContent();
    }
}
