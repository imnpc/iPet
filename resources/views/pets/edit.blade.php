@extends('layouts.app')

@section('title', '编辑 ' . $pet->name . ' - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('pets.show', $pet) }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回宠物详情
        </a>
        <h1 class="ui-page-title">编辑 {{ $pet->name }}</h1>
        <p class="ui-page-subtitle">修改宠物档案信息</p>
    </div>

    <div class="ui-card ui-card-shadow-strong p-8 animate-fade-in-up delay-100">
        <form action="{{ route('pets.update', $pet) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="ui-label">宠物头像</label>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="h-20 w-20 overflow-hidden rounded-2xl bg-gradient-to-br from-primary-50 to-accent-50 ring-1 ring-warm-100">
                        @if($pet->avatar)
                            <img src="{{ $pet->avatar }}" alt="{{ $pet->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-3xl">🐾</div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="avatar" accept="image/*" class="ui-input @error('avatar') border-danger-300 @enderror">
                        <p class="mt-2 text-xs text-warm-400">重新上传会替换当前头像，最大 5MB。</p>
                        @error('avatar')
                            <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="ui-label">名字 <span class="text-danger-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $pet->name) }}" required maxlength="50" class="ui-input @error('name') border-danger-300 @enderror" placeholder="宠物名字">
                @error('name')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">物种 <span class="text-danger-500">*</span></label>
                    <select name="pet_species_id" required class="ui-select">
                        <option value="">请选择</option>
                        @foreach(\App\Models\PetSpecies::where('is_enabled', true)->orderBy('sort_order')->get() as $species)
                            <option value="{{ $species->id }}" {{ old('pet_species_id', $pet->pet_species_id) == $species->id ? 'selected' : '' }}>{{ $species->name }}</option>
                        @endforeach
                    </select>
                    @error('pet_species_id')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">品种</label>
                    <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}" maxlength="100" class="ui-input" placeholder="如：金毛、布偶猫">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">性别</label>
                    <select name="gender" class="ui-select">
                        <option value="unknown" {{ old('gender', $pet->gender) === 'unknown' ? 'selected' : '' }}>未知</option>
                        <option value="male" {{ old('gender', $pet->gender) === 'male' ? 'selected' : '' }}>公 ♂</option>
                        <option value="female" {{ old('gender', $pet->gender) === 'female' ? 'selected' : '' }}>母 ♀</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">生日</label>
                    <input type="date" name="birthday" value="{{ old('birthday', $pet->birthday ? $pet->birthday->format('Y-m-d') : '') }}" class="ui-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">到家日期</label>
                    <input type="date" name="adoption_date" value="{{ old('adoption_date', $pet->adoption_date ? $pet->adoption_date->format('Y-m-d') : '') }}" class="ui-input">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-warm-200 hover:border-primary-300 hover:bg-primary-50 transition-all duration-200 w-full">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $pet->is_default) ? 'checked' : '' }} class="rounded-lg border-warm-300 text-primary-500 focus:ring-primary-400 focus:ring-2">
                        <span class="text-sm font-medium text-warm-700">设为默认宠物</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-warm-100">
                <button type="submit" class="ui-btn-primary flex-1 py-3.5 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    保存修改
                </button>
                <a href="{{ route('pets.show', $pet) }}" class="ui-btn-secondary px-6 py-3.5">
                    取消
                </a>
            </div>
        </form>
    </div>

    <!-- 删除区域 -->
    <div class="mt-8 ui-card ui-card-shadow border-danger-100 p-6 animate-fade-in-up delay-200">
        <h2 class="text-lg font-display font-bold text-danger-600 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            危险操作
        </h2>
        <p class="text-sm text-warm-500 mb-4">删除宠物后，相关的医疗记录和动态也将被删除，此操作不可撤销。</p>
        <form action="{{ route('pets.destroy', $pet) }}" method="POST" onsubmit="return confirm('确定要删除 {{ $pet->name }} 吗？此操作不可撤销！')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2.5 bg-danger-50 hover:bg-danger-100 text-danger-600 border border-danger-200 rounded-xl font-semibold transition-colors">
                删除此宠物
            </button>
        </form>
    </div>
</div>
@endsection
