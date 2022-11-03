<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\Image\StoreRequest;
use App\Http\Resources\Post\Image\ImageResource;
use App\Models\PostImage;
use Illuminate\Support\Facades\Storage;

class PostImagesController extends Controller
{
    public function store(StoreRequest $request)
    {
        $path = Storage::disk('public')->put('/images', $request['image']);

        $image = PostImage::create([
            'path' => $path,
            'user_id' => auth()->user()->id,
        ]);

        return new ImageResource($image);
    }
}
