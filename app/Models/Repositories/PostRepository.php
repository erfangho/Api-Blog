<?php

namespace App\Models\Repositories;

use App\Models\Post;
use App\Models\Repositories\PostRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class PostRepository implements PostRepositoryInterface
{
    public function postFilter($data)
    {
        $posts = new Post;
        if($data->has('author_id')){
            $posts = $posts->where('author_id', $data->author_id);
        }
        if($data->has('date')){
            $datetime = new Carbon($data->date.' 00:00:00');
            $posts = $posts->whereDate('created_at', $datetime);
        }
        if($data->has('from')){
            $from = new Carbon($data->from.' 00:00:00');
            $to = new Carbon($data->to.' 00:00:00');
            $posts = $posts->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        }
        return Response()->json($posts->get(), HttpFoundationResponse::HTTP_OK);
    }

    public function createPost($data)
    {
        $upload_image = $data->file('image')->store('uploads/images', 'public');
        $upload_thumbnail = $data->file('thumbnail')->store('uploads/thumbnails', 'public');
        $post = Post::create([
            "title" => $data->title,
            "author_id" => auth()->user()->id,
            "image" => asset("storage/{$upload_image}"),
            "thumbnail" => asset("storage/{$upload_thumbnail}"),
            "publish_time" => Carbon::now()->format('Y-m-d H:i:s'),
            "body" => $data->body,
        ]);
        return Response()->json(["message" => __("messages.done")], HttpFoundationResponse::HTTP_OK);
    }

    public function showPostById($id)
    {

    }

    public function updatePostById($id, $data)
    {
        $post = Post::findOrFail($id);
        $upload_image = $data->file('image')->store('uploads/images');
        $upload_thumbnail = $data->file('thumbnail')->store('uploads/thumbnails');
        File::delete("storage/uploads/images/".basename($post->image));
        File::delete("storage/uploads/thumbnails/".basename($post->thumbnail));
        $post->title = $data->title;
        $post->image = asset("storage/{$upload_image}");
        $post->thumbnail  = asset("storage/{$upload_thumbnail}");
        $post->body  = $data->body;
        $post->save();
        return Response()->json(["message" => __("messages.done")], HttpFoundationResponse::HTTP_OK);
    }

    public function deletePostById($id)
    {

    }
}
