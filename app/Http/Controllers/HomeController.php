<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessVideoJob;
use App\Models\Comment;
use App\Models\Pet;
use App\Models\PetRecord;
use App\Models\PetSpecies;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'pet', 'media', 'tags'])
            ->whereNotNull('published_at')
            ->where(function ($query) use ($request) {
                $query->where('visibility', 'public');

                if ($request->user() instanceof User) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            });

        if ($request->filled('tag')) {
            $query->withAnyTags([$request->input('tag')]);
        }

        if ($request->filled('species')) {
            $query->whereHas('pet.species', function ($speciesQuery) use ($request) {
                $speciesQuery->where('name', $request->string('species')->value());
            });
        }

        if ($request->filled('q')) {
            $keyword = $request->string('q')->trim()->value();
            $query->where(function ($searchQuery) use ($keyword): void {
                $searchQuery->where('content', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%")
                    ->orWhereHas('pet', function ($petQuery) use ($keyword): void {
                        $petQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhereHas('species', function ($speciesQuery) use ($keyword): void {
                                $speciesQuery->where('name', 'like', "%{$keyword}%");
                            })
                            ->orWhere('breed', 'like', "%{$keyword}%");
                    });
            });
        }

        $posts = $query->orderByPublishedAtDesc()
            ->when(
                $request->user() instanceof User,
                fn ($postQuery) => $postQuery->withExists([
                    'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', $request->user()->id),
                ]),
            )
            ->paginate(20)
            ->withQueryString();

        $speciesOptions = PetSpecies::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->pluck('name');

        return view('posts.index', compact('posts', 'speciesOptions'));
    }

    public function pets()
    {
        $user = Auth::user();

        $pets = $user instanceof User
            ? $user->pets()->withCount(['records', 'posts'])->orderBy('sort_order')->get()
            : collect();

        return view('pets.index', compact('pets'));
    }

    public function petShow(Request $request, Pet $pet)
    {
        $isOwner = $request->user()?->id === $pet->user_id;

        $pet->loadCount('posts');

        $petPosts = $pet->posts()
            ->published()
            ->orderByPublishedAtDesc()
            ->with(['user', 'pet', 'media', 'tags'])
            ->paginate(5)
            ->withQueryString();

        if ($request->user() instanceof User) {
            $petPosts->getCollection()->loadExists([
                'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', $request->user()->id),
            ]);
        }

        $petRecords = $pet->records()
            ->when(! $isOwner, fn ($q) => $q->where('is_public', true))
            ->orderBy('visit_date', 'desc')
            ->paginate(5)
            ->withQueryString();

        $activeTab = $request->input('tab', 'posts');

        return view('pets.show', compact('pet', 'petPosts', 'petRecords', 'isOwner', 'activeTab'));
    }

    public function petEdit(Request $request, Pet $pet)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);

        return view('pets.edit', compact('pet'));
    }

    public function petUpdate(Request $request, Pet $pet)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'pet_species_id' => 'required|exists:pet_species,id',
            'breed' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,unknown',
            'birthday' => 'nullable|date',
            'adoption_date' => 'nullable|date',
            'avatar' => 'nullable|image|max:5120',
            'is_default' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('pets/avatars', 'public');
            $validated['avatar'] = asset('storage/'.$path);
        }

        $pet->update($validated);

        if ($validated['is_default'] ?? false) {
            $request->user()->pets()->where('id', '!=', $pet->id)->update(['is_default' => false]);
        }

        return redirect()->route('pets.show', $pet)->with('success', '宠物信息已更新');
    }

    public function petDestroy(Request $request, Pet $pet)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);

        $pet->delete();

        return redirect()->route('pets.index')->with('success', '宠物已删除');
    }

    public function petRecordCreate(Request $request, Pet $pet)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);

        return view('pets.records.create', compact('pet'));
    }

    public function petRecordStore(Request $request, Pet $pet)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);

        $validated = $request->validate([
            'pet_record_type_id' => 'required|exists:pet_record_types,id',
            'title' => 'required|string|max:200',
            'visit_date' => 'required|date',
            'next_visit_date' => 'nullable|date',
            'hospital_name' => 'nullable|string|max:200',
            'vet_name' => 'nullable|string|max:100',
            'hospital_phone' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'is_public' => 'boolean',
        ]);

        $pet->records()->create($validated);

        return redirect()->route('pets.show', $pet)->with('success', '医疗记录添加成功');
    }

    public function petRecordEdit(Request $request, Pet $pet, PetRecord $record)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);
        abort_unless($record->pet_id === $pet->id, 404);

        return view('pets.records.edit', compact('pet', 'record'));
    }

    public function petRecordUpdate(Request $request, Pet $pet, PetRecord $record)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);
        abort_unless($record->pet_id === $pet->id, 404);

        $validated = $request->validate([
            'pet_record_type_id' => 'sometimes|required|exists:pet_record_types,id',
            'title' => 'sometimes|required|string|max:200',
            'visit_date' => 'sometimes|required|date',
            'next_visit_date' => 'nullable|date',
            'hospital_name' => 'nullable|string|max:200',
            'vet_name' => 'nullable|string|max:100',
            'hospital_phone' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'is_public' => 'boolean',
        ]);

        $record->update($validated);

        return redirect()->route('pets.show', $pet)->with('success', '医疗记录更新成功');
    }

    public function petRecordDestroy(Request $request, Pet $pet, PetRecord $record)
    {
        abort_unless($request->user()?->id === $pet->user_id, 403);
        abort_unless($record->pet_id === $pet->id, 404);

        $record->delete();

        return redirect()->route('pets.show', $pet)->with('success', '医疗记录已删除');
    }

    public function petCreate()
    {
        return view('pets.create');
    }

    public function petStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'pet_species_id' => 'required|exists:pet_species,id',
            'breed' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,unknown',
            'birthday' => 'nullable|date',
            'adoption_date' => 'nullable|date',
            'avatar' => 'nullable|image|max:5120',
            'is_default' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('pets/avatars', 'public');
            $validated['avatar'] = asset('storage/'.$path);
        }

        $pet = $request->user()->pets()->create($validated);

        if ($validated['is_default'] ?? false) {
            $request->user()->pets()->where('id', '!=', $pet->id)->update(['is_default' => false]);
        }

        return redirect()->route('pets.index')->with('success', '宠物添加成功');
    }

    public function postEdit(Request $request, Post $post)
    {
        abort_unless($request->user()?->id === $post->user_id, 403);

        $pets = $request->user()->pets()->get();

        return view('posts.edit', compact('post', 'pets'));
    }

    public function postUpdate(Request $request, Post $post)
    {
        abort_unless($request->user()?->id === $post->user_id, 403);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'pet_id' => [
                'nullable',
                Rule::exists('pets', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id)),
            ],
            'location' => 'nullable|string|max:200',
            'visibility' => 'in:public,followers,private',
            'image_files' => 'nullable|array',
            'image_files.*' => 'file|image|max:10240',
            'video_files' => 'nullable|array',
            'video_files.*' => 'file|mimetypes:video/mp4,video/quicktime,video/webm|max:102400',
            'keep_media_ids' => 'nullable|array',
            'keep_media_ids.*' => [
                'integer',
                Rule::exists('post_media', 'id')->where(fn ($query) => $query->where('post_id', $post->id)),
            ],
            'media_order' => 'nullable|array',
            'media_order.*' => [
                'integer',
                Rule::exists('post_media', 'id')->where(fn ($query) => $query->where('post_id', $post->id)),
            ],
            'tags' => 'nullable|string',
        ]);

        if (! empty($validated['tags'])) {
            $tagNames = array_filter(array_map('trim', explode(',', $validated['tags'])));
            if (! empty($tagNames)) {
                $post->syncTags($tagNames);
            } else {
                $post->detachTags($post->tags()->pluck('id')->toArray());
            }
        } else {
            $post->detachTags($post->tags()->pluck('id')->toArray());
        }

        $post->update([
            'content' => $validated['content'],
            'pet_id' => $validated['pet_id'] ?? null,
            'location' => $validated['location'] ?? null,
            'visibility' => $validated['visibility'] ?? 'public',
        ]);

        $existingMedia = $post->media()->orderBy('sort_order')->get();
        $keepMediaIds = collect($validated['keep_media_ids'] ?? [])->map(fn ($id) => (int) $id)->values();

        if ($keepMediaIds->isEmpty()) {
            $keepMediaIds = $existingMedia->pluck('id')->map(fn ($id) => (int) $id)->values();
        }

        $mediaToDelete = $existingMedia->whereNotIn('id', $keepMediaIds);
        foreach ($mediaToDelete as $mediaItem) {
            if (! empty($mediaItem->path)) {
                Storage::disk($mediaItem->disk)->delete($mediaItem->path);
            }

            if (! empty($mediaItem->thumbnail_path)) {
                Storage::disk($mediaItem->disk)->delete($mediaItem->thumbnail_path);
            }

            $mediaItem->delete();
        }

        $orderSource = collect($validated['media_order'] ?? [])->map(fn ($id) => (int) $id)->values();
        $orderedMediaIds = $orderSource->filter(fn ($id) => $keepMediaIds->contains($id))->values();
        $missingMediaIds = $keepMediaIds->diff($orderedMediaIds)->values();
        $finalOrderedMediaIds = $orderedMediaIds->merge($missingMediaIds)->values();

        foreach ($finalOrderedMediaIds as $sortOrder => $mediaId) {
            $post->media()->where('id', $mediaId)->update(['sort_order' => $sortOrder]);
        }

        $sortOrder = (int) $post->media()->max('sort_order') + 1;
        $defaultDisk = config('filesystems.default');

        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $imageFile) {
                $path = $imageFile->store('posts/images');

                $post->media()->create([
                    'type' => 'image',
                    'disk' => $defaultDisk,
                    'path' => $path,
                    'mime_type' => $imageFile->getClientMimeType(),
                    'size' => $imageFile->getSize(),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        if ($request->hasFile('video_files')) {
            foreach ($request->file('video_files') as $videoFile) {
                $path = $videoFile->store('posts/videos');

                $media = $post->media()->create([
                    'type' => 'video',
                    'disk' => $defaultDisk,
                    'path' => $path,
                    'mime_type' => $videoFile->getClientMimeType(),
                    'size' => $videoFile->getSize(),
                    'sort_order' => $sortOrder++,
                ]);

                ProcessVideoJob::dispatch($media);
            }
        }

        return redirect()->route('posts.show', $post)->with('success', '动态更新成功');
    }

    public function postDestroy(Request $request, Post $post)
    {
        abort_unless($request->user()?->id === $post->user_id, 403);

        $post->delete();

        return redirect()->route('posts.index')->with('success', '动态已删除');
    }

    public function posts(Request $request)
    {
        $query = Post::with(['user', 'pet', 'media', 'tags'])
            ->whereNotNull('published_at')
            ->where(function ($query) use ($request) {
                $query->where('visibility', 'public');

                if ($request->user() instanceof User) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            });

        if ($request->filled('tag')) {
            $query->withAnyTags([$request->input('tag')]);
        }

        if ($request->filled('species')) {
            $query->whereHas('pet.species', function ($speciesQuery) use ($request) {
                $speciesQuery->where('name', $request->string('species')->value());
            });
        }

        if ($request->filled('q')) {
            $keyword = $request->string('q')->trim()->value();
            $query->where(function ($searchQuery) use ($keyword): void {
                $searchQuery->where('content', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%")
                    ->orWhereHas('pet', function ($petQuery) use ($keyword): void {
                        $petQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhereHas('species', function ($speciesQuery) use ($keyword): void {
                                $speciesQuery->where('name', 'like', "%{$keyword}%");
                            })
                            ->orWhere('breed', 'like', "%{$keyword}%");
                    });
            });
        }

        $posts = $query->orderByPublishedAtDesc()
            ->when(
                $request->user() instanceof User,
                fn ($postQuery) => $postQuery->withExists([
                    'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', $request->user()->id),
                ]),
            )
            ->paginate(20)
            ->withQueryString();

        $speciesOptions = PetSpecies::query()
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->pluck('name');

        return view('posts.index', compact('posts', 'speciesOptions'));
    }

    public function postCreate()
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return view('posts.create', ['pets' => collect()]);
        }

        $pets = $user->pets()->get();

        return view('posts.create', compact('pets'));
    }

    public function postShow(Request $request, Post $post)
    {
        $viewDeduplicateMinutes = max((int) config('app.post_view_deduplicate_minutes', 10), 1);
        $viewerFingerprint = $request->user() instanceof User
            ? 'user:'.$request->user()->id
            : 'guest:'.sha1(($request->ip() ?? 'unknown').'|'.($request->userAgent() ?? 'unknown'));
        $viewCacheKey = sprintf('post:viewed:%d:%s', $post->id, $viewerFingerprint);

        if (Cache::add($viewCacheKey, true, now()->addMinutes($viewDeduplicateMinutes))) {
            $post->increment('view_count');
        }

        $commentSort = $request->string('comment_sort')->value();
        $commentSort = in_array($commentSort, ['hot', 'time'], true) ? $commentSort : 'time';

        $post->load([
            'user',
            'pet',
            'media',
            'tags',
            'comments' => fn ($query) => $query
                ->whereNull('parent_id')
                ->when(
                    $commentSort === 'hot',
                    fn ($sortQuery) => $sortQuery->orderByDesc('like_count')->orderByDesc('created_at'),
                    fn ($sortQuery) => $sortQuery->latest(),
                )
                ->with([
                    'user',
                    'children' => fn ($childQuery) => $childQuery
                        ->oldest()
                        ->with('user'),
                ]),
        ]);

        if ($request->user() instanceof User) {
            $post->loadExists([
                'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', $request->user()->id),
            ]);

            $post->comments->loadExists([
                'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', $request->user()->id),
            ]);

            $post->comments->each(function (Comment $comment) use ($request): void {
                $comment->children->loadExists([
                    'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', $request->user()->id),
                ]);
            });
        }

        return view('posts.show', [
            'post' => $post,
            'commentSort' => $commentSort,
        ]);
    }

    public function userShow(User $user)
    {
        $posts = $user->posts()
            ->with(['pet', 'media', 'tags'])
            ->published()
            ->where('visibility', 'public')
            ->when(
                request()->user() instanceof User,
                fn ($postQuery) => $postQuery->withExists([
                    'likes as is_liked' => fn ($likeQuery) => $likeQuery->where('user_id', request()->user()->id),
                ]),
            )
            ->orderByPublishedAtDesc()
            ->paginate(12);

        return view('users.show', compact('user', 'posts'));
    }

    public function commentStore(Request $request, Post $post)
    {
        if (($post->allow_comment ?? true) === false) {
            return redirect()->route('posts.show', $post)->with('error', '该动态已关闭评论');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => [
                'nullable',
                Rule::exists('comments', 'id')->where(fn ($query) => $query->where('post_id', $post->id)),
            ],
        ]);

        $parentId = $validated['parent_id'] ?? null;

        if ($parentId !== null) {
            $parentComment = $post->comments()->select(['id', 'parent_id'])->find($parentId);
            $parentId = $parentComment?->parent_id ?? $parentComment?->id;
        }

        $request->user()->comments()->create([
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'content' => $validated['content'],
        ]);

        $post->increment('comment_count');

        return redirect()->route('posts.show', ['post' => $post, 'comment_sort' => $request->string('comment_sort')->value() ?: null])->with('success', '评论发布成功');
    }

    public function postLike(Request $request, Post $post)
    {
        $like = $post->likes()->where([
            'user_id' => $request->user()->id,
        ])->first();

        if ($like) {
            $like->delete();
            $post->decrement('like_count');

            return redirect()->to($request->input('return_to', url()->previous()))->with('success', '已取消点赞');
        }

        $post->likes()->create([
            'user_id' => $request->user()->id,
            'created_at' => now(),
        ]);

        $post->increment('like_count');

        return redirect()->to($request->input('return_to', url()->previous()))->with('success', '点赞成功');
    }

    public function commentLike(Request $request, Post $post, Comment $comment)
    {
        abort_unless($comment->post_id === $post->id, 404);

        $like = $comment->likes()->where([
            'user_id' => $request->user()->id,
        ])->first();

        if ($like) {
            $like->delete();
            $comment->decrement('like_count');

            return redirect()->route('posts.show', ['post' => $post, 'comment_sort' => $request->string('comment_sort')->value() ?: null])->with('success', '已取消点赞');
        }

        $comment->likes()->create([
            'user_id' => $request->user()->id,
            'created_at' => now(),
        ]);

        $comment->increment('like_count');

        return redirect()->route('posts.show', ['post' => $post, 'comment_sort' => $request->string('comment_sort')->value() ?: null])->with('success', '点赞成功');
    }

    public function postStore(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'pet_id' => [
                'nullable',
                Rule::exists('pets', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id)),
            ],
            'location' => 'nullable|string|max:200',
            'visibility' => 'in:public,followers,private',
            'published_at' => 'nullable|date',
            'image_files' => 'nullable|array',
            'image_files.*' => 'file|image|max:10240',
            'video_files' => 'nullable|array',
            'video_files.*' => 'file|mimetypes:video/mp4,video/quicktime,video/webm|max:102400',
            'tags' => 'nullable|string',
        ]);

        $post = $request->user()->posts()->create([
            'content' => $validated['content'],
            'pet_id' => $validated['pet_id'] ?? null,
            'location' => $validated['location'] ?? null,
            'visibility' => $validated['visibility'] ?? 'public',
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        $sortOrder = 0;
        $defaultDisk = config('filesystems.default');

        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $imageFile) {
                $path = $imageFile->store('posts/images');

                $post->media()->create([
                    'type' => 'image',
                    'disk' => $defaultDisk,
                    'path' => $path,
                    'mime_type' => $imageFile->getClientMimeType(),
                    'size' => $imageFile->getSize(),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        if ($request->hasFile('video_files')) {
            foreach ($request->file('video_files') as $videoFile) {
                $path = $videoFile->store('posts/videos');

                $media = $post->media()->create([
                    'type' => 'video',
                    'disk' => $defaultDisk,
                    'path' => $path,
                    'mime_type' => $videoFile->getClientMimeType(),
                    'size' => $videoFile->getSize(),
                    'sort_order' => $sortOrder++,
                ]);

                ProcessVideoJob::dispatch($media);
            }
        }

        if (! empty($validated['tags'])) {
            $tagNames = array_filter(array_map('trim', explode(',', $validated['tags'])));
            if (! empty($tagNames)) {
                $post->attachTags($tagNames);
            }
        }

        return redirect()->route('posts.index')->with('success', '动态发布成功');
    }
}
