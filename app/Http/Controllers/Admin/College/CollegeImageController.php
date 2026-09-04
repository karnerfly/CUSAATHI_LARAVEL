<?php

namespace App\Http\Controllers\Admin\College;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\College\StoreCollegeImageRequest;
use App\Models\College\Image;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin College Management')]
class CollegeImageController extends Controller
{
    /**
     * Display the specified college image.
     */
    public function show(Image $image)
    {
        return $image;
    }

    /**
     * Update the specified college image in storage.
     */
    public function update(StoreCollegeImageRequest $request, Image $image)
    {
        $validatd = $request->validated();
        $image->update($validatd);

        return response()->noContent();
    }

    /**
     * Remove the specified college image from storage.
     */
    public function destroy(Image $image)
    {
        $image->delete();

        return response()->noContent();
    }
}
