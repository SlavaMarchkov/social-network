<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CommentRequest;
use App\Http\Requests\Post\RepostRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Comment;
use App\Models\LikedPost;
use App\Models\Post;
use App\Models\PostImage;
use Exception;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::where('user_id', auth()->id())
            ->withCount('repostedByPosts')
            ->latest()
            ->get();

        // dd($posts);

        // вытаскиваем посты, которые пролайкал текущий пользователь
        $likedPostsIds = LikedPost::where('user_id', auth()->id())
            ->get('post_id')
            ->pluck('post_id')
            ->toArray();

        foreach ($posts as $post) {
            if (in_array($post->id, $likedPostsIds)) {
                $post->is_liked = true;
            }
        }

        return PostResource::collection($posts);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $data['user_id'] = auth()->user()->id;
            $imageId = $data['image_id'];
            unset($data['image_id']);

            $post = Post::create($data);

            $this->processImage($post, $imageId);

            PostImage::clearStorage();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'error' => $exception->getMessage(),
            ]);
        }

        return new PostResource($post);
    }

    public function repost(RepostRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['reposted_id'] = $post->id;

        Post::create($data);
    }

    public function comment(CommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['post_id'] = $post->id;

        $comment = Comment::create($data);

        return new CommentResource($comment);
    }

    private function processImage($post, $imageId)
    {
        if (isset($imageId)) {
            $image = PostImage::find($imageId);
            $image->update([
                'status' => true,
                'post_id' => $post->id
            ]);
        }

        return true;
    }


    public function toggleLike(Post $post)
    {
        $res = auth()->user()->likedPosts()->toggle($post->id);
        $data['is_liked'] = count($res['attached']) > 0;
        $data['likes_count'] = $post->likedUsers()->count();

        return $data;
    }

    public function getComments(Post $post)
    {
        $comments = $post->comments()->latest()->get();

        return CommentResource::collection($comments);
    }
}
