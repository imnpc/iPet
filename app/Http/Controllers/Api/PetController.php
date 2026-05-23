<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

#[Group('宠物', description: '宠物管理', weight: 20)]
#[Prefix('pets')]
class PetController extends Controller
{
    #[Get('', middleware: ['auth:sanctum'])]
    public function index(Request $request): JsonResponse
    {
        $pets = $request->user()
            ->pets()
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->get();

        return $this->success(PetResource::collection($pets));
    }

    #[Post('', middleware: ['auth:sanctum'])]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'species' => 'required|string|max:30',
            'breed' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,unknown',
            'birthday' => 'nullable|date',
            'adoption_date' => 'nullable|date',
            'avatar' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'is_default' => 'boolean',
        ]);

        $user = $request->user();
        $pet = $user->pets()->create($validated);

        if ($validated['is_default'] ?? false) {
            $user->pets()->where('id', '!=', $pet->id)->update(['is_default' => false]);
        }

        return $this->success(new PetResource($pet), '宠物添加成功', 201);
    }

    #[Get('{id}', middleware: ['auth:sanctum'])]
    public function show(Request $request, int $id): JsonResponse
    {
        $pet = $request->user()->pets()->findOrFail($id);

        return $this->success(new PetResource($pet));
    }

    #[Put('{id}', middleware: ['auth:sanctum'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $pet = $request->user()->pets()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'species' => 'sometimes|required|string|max:30',
            'breed' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,unknown',
            'birthday' => 'nullable|date',
            'adoption_date' => 'nullable|date',
            'avatar' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'is_default' => 'boolean',
            'status' => 'nullable|in:active,archived,deceased',
        ]);

        $pet->update($validated);

        if ($validated['is_default'] ?? false) {
            $request->user()->pets()->where('id', '!=', $pet->id)->update(['is_default' => false]);
        }

        return $this->success(new PetResource($pet), '宠物更新成功');
    }

    #[Delete('{id}', middleware: ['auth:sanctum'])]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $pet = $request->user()->pets()->findOrFail($id);
        $pet->delete();

        return $this->success(null, '宠物删除成功');
    }

    #[Put('{id}/default', middleware: ['auth:sanctum'])]
    public function setDefault(Request $request, int $id): JsonResponse
    {
        $pet = $request->user()->pets()->findOrFail($id);

        DB::transaction(function () use ($request, $pet): void {
            $request->user()->pets()->where('id', '!=', $pet->id)->update(['is_default' => false]);
            $pet->update(['is_default' => true]);
        });

        return $this->success(new PetResource($pet), '已设为默认宠物');
    }
}
