<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StatisticsRequest;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use App\Models\LikedPost;
use App\Models\Post;
use App\Models\SubscriberFollowing;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::whereNot('id', auth()->id())->get();

        // вытаскиваем тех, на кого подписан текущий пользователь
        $followingIds = SubscriberFollowing::where('subscriber_id', auth()->id())
            ->get('following_id')
            ->pluck('following_id')
            ->toArray();

        foreach ($users as $user) {
            if (in_array($user->id, $followingIds)) {
                $user->is_followed = true;
            }
        }

        return UserResource::collection($users);
    }

    public function posts(User $user)
    {
        $posts = $user
            ->posts()
            ->withCount('repostedByPosts')
            ->latest()
            ->get();

        $posts = $this->prepareLikedPosts($posts);

        return PostResource::collection($posts);
    }

    public function toggleFollowing(User $user)
    {
        $res = auth()->user()->followings()->toggle($user->id);
        $data['is_followed'] = count($res['attached']) > 0;

        return $data;
    }

    public function feed()
    {
        $followedUsersIds = auth()
            ->user()
            ->followings()
            ->get()
            ->pluck('id')
            ->toArray();

        $likedPostsIds = LikedPost::where('user_id', auth()->id())
            ->get('post_id')
            ->pluck('post_id')
            ->toArray();

        $posts = Post::whereIn('user_id', $followedUsersIds)
            ->whereNotIn('id', $likedPostsIds)
            ->withCount('repostedByPosts')
            ->latest()
            ->get();

        return PostResource::collection($posts);
    }

    private function prepareLikedPosts($posts)
    {
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

        return $posts;
    }

    public function getStatistics(StatisticsRequest $request)
    {
        $data = $request->validated();
        $userId = isset($data['user_id']) ? $data['user_id'] : auth()->id();

        $postIds = Post::where('user_id', $userId)->get('id')->pluck('id')->toArray();

        $result = [
            'subscribers_count' => SubscriberFollowing::where('following_id', $userId)->count(),
            'followings_count' => SubscriberFollowing::where('subscriber_id', $userId)->count(),
            'likes_count' => LikedPost::whereIn('post_id', $postIds)->count(),
            'posts_count' => count($postIds),
        ];

        return response()->json([
            'data' => $result,
        ]);
    }
}
