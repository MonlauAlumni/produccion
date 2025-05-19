<?php

namespace App\Http\Controllers\Social;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;

class PostController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $sanitizedContent = Purifier::clean($request->content);

        $sanitizedContent = preg_replace('/<a\s+href="([^"]+)"/i', '<a href="$1" style="text-decoration: underline; color: #2563eb;"', $sanitizedContent);

        $post = Post::create([
            'user_id' => Auth::id(),
            'group_id' => $request->group_id,
            'content' => $sanitizedContent,
        ]);

        if ($request->hasFile('images')) {
            $order = 0;
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('posts/images', 'public');
                
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => '/storage/' . $imagePath,
                    'order' => $order++
                ]);
            }
        }

        return redirect()->back()->with('success', 'Post creado con éxito!');

    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este post.');
        }
        $post->images()->each(function ($image) {
            $imagePath = public_path($image->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $image->delete();
        });
        foreach ($post->comments as $comment) {
            $commentToDelete = PostComment::find($comment->id);
            if ($commentToDelete) {
                $commentToDelete->delete();
            }

        }
        $post->delete();
        return redirect()->back()->with('success', 'Post eliminado con éxito!');
    }

    public function storeInGroup(Request $request, $groupId)
    {
        $request->merge(['group_id' => $groupId]);
        return $this->store($request);
    }

    public function addComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
            'comment' => 'sometimes|required|string', 
        ]);
        
        $commentContent = $request->input('content', $request->input('comment'));
        $sanitizedContent = Purifier::clean($commentContent);

        if (empty($sanitizedContent)) {
            return redirect()->back()->with('error', 'El comentario no puede estar vacío.');
        }
        
        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $sanitizedContent,
        ]);
        
        $post->increment('comments_count');
        
        return redirect()->back()->with('success', 'Comentario añadido con éxito!');
    }


    public function addCommentInGroup(Request $request, $groupId, $postId)
    {
        $post = Post::findOrFail($postId);
        
        if ($post->group_id != $groupId) {
            return redirect()->back()->with('error', 'El post no pertenece a este grupo.');
        }
        
        return $this->addComment($request, $post);
    }

    public function toggleLike(Post $post)
    {
        $user = Auth::user();
        
        if ($post->isLikedBy($user)) {
            $post->likes()->where('user_id', $user->id)->delete();
            $post->decrement('likes_count');
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $post->increment('likes_count');
        }
        
        return redirect()->back();
    }
}