<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetResource;
use App\Http\Resources\PostResource;
use App\Models\Pet;
use App\Models\Post;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Group('搜索', description: '全局搜索', weight: 50)]
#[Prefix('search')]
class SearchController extends Controller
{
    #[Get('')]
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|max:100',
            'type' => 'in:all,posts,pets',
        ]);

        $query = $validated['q'];
        $type = $validated['type'] ?? 'all';

        $results = [];

        if (in_array($type, ['all', 'posts'])) {
            $posts = Post::with(['user', 'pet', 'media'])
                ->where('visibility', 'public')
                ->whereNotNull('published_at')
                ->where(function ($q) use ($query) {
                    $q->where('content', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%");
                })
                ->orderBy('published_at', 'desc')
                ->limit(20)
                ->get();

            $results['posts'] = PostResource::collection($posts);
        }

        if (in_array($type, ['all', 'pets'])) {
            $pets = Pet::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('species', 'like', "%{$query}%")
                    ->orWhere('breed', 'like', "%{$query}%");
            })
                ->limit(20)
                ->get();

            $results['pets'] = PetResource::collection($pets);
        }

        return $this->success($results);
    }
}
