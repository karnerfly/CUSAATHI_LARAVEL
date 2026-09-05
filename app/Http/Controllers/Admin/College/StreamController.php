<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreStreamRequest;
use App\Http\Resources\Admin\College\StreamResource;
use App\Models\College\Stream;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Admin College Meta Management')]
class StreamController extends Controller
{
    /**
     * Display a listing of the streams.
     */
    public function index()
    {
        return StreamResource::collection(Stream::with('course')->get());
    }

    /**
     * Store a newly created stream in storage.
     */
    public function store(StoreStreamRequest $request)
    {
        $validated = $request->validated();
        $stream = Stream::create($validated);

        return response()->json(
            [
                'message' => 'Stream created.',
                'stream' => $stream,
            ],
            201,
        );
    }

    /**
     * Display the specified stream.
     */
    public function show(Stream $stream)
    {
        $stream->load('course');

        return StreamResource::make($stream);
    }

    /**
     * Update the specified stream in storage.
     */
    public function update(StoreStreamRequest $request, Stream $stream)
    {
        $validated = $request->validated();
        $stream->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified stream from storage.
     */
    public function destroy(Stream $stream)
    {
        $stream->delete();

        return response()->noContent();
    }
}
