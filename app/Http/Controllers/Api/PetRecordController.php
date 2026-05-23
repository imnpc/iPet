<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PetRecordResource;
use App\Models\Pet;
use App\Models\PetRecord;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;

#[Group('医疗记录', description: '宠物医疗记录管理', weight: 25)]
#[Prefix('records')]
class PetRecordController extends Controller
{
    #[Get('', middleware: ['auth:sanctum'])]
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
        ]);

        $pet = Pet::findOrFail($request->input('pet_id'));
        $isOwner = $request->user()->id === $pet->user_id;

        $records = $pet->records()
            ->when(! $isOwner, fn ($query) => $query->where('is_public', true))
            ->orderBy('visit_date', 'desc')
            ->paginate($request->input('per_page', 20));

        return $this->success(PetRecordResource::collection($records));
    }

    #[Post('', middleware: ['auth:sanctum'])]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|in:vaccine,checkup,illness,medication,surgery,grooming,other',
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
            'is_public' => 'nullable|boolean',
        ]);

        $pet = $request->user()->pets()->findOrFail($validated['pet_id']);
        $record = $pet->records()->create($validated);

        return $this->success(new PetRecordResource($record), '记录添加成功', 201);
    }

    #[Get('{id}', middleware: ['auth:sanctum'])]
    public function show(Request $request, int $id): JsonResponse
    {
        $record = PetRecord::findOrFail($id);
        $isOwner = $record->pet->user_id === $request->user()->id;

        // 非主人只能查看公开记录
        if (! $isOwner && ! $record->is_public) {
            abort(403, '无权查看此记录');
        }

        return $this->success(new PetRecordResource($record));
    }

    #[Put('{id}', middleware: ['auth:sanctum'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $record = PetRecord::whereHas('pet', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|required|in:vaccine,checkup,illness,medication,surgery,grooming,other',
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
            'is_public' => 'nullable|boolean',
        ]);

        $record->update($validated);

        return $this->success(new PetRecordResource($record), '记录更新成功');
    }

    #[Delete('{id}', middleware: ['auth:sanctum'])]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $record = PetRecord::whereHas('pet', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);
        $record->delete();

        return $this->success(null, '记录删除成功');
    }

    #[Get('weight-trend', middleware: ['auth:sanctum'])]
    public function weightTrend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $pet = $request->user()->pets()->findOrFail($validated['pet_id']);
        $limit = $validated['limit'] ?? 50;

        $records = $pet->records()
            ->whereNotNull('weight')
            ->orderBy('visit_date', 'asc')
            ->limit($limit)
            ->get(['visit_date', 'weight']);

        return $this->success([
            'pet' => [
                'id' => $pet->id,
                'name' => $pet->name,
            ],
            'data' => $records->map(fn ($r) => [
                'date' => $r->visit_date->format('Y-m-d'),
                'weight' => (float) $r->weight,
            ]),
        ]);
    }
}
