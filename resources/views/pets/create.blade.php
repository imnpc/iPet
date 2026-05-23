@extends('layouts.app')

@section('title', '添加宠物 - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('pets.index') }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回宠物列表
        </a>
        <h1 class="ui-page-title">添加新宠物</h1>
        <p class="ui-page-subtitle">填写以下信息，为您的毛孩子建立档案</p>
    </div>

    <div class="ui-card ui-card-shadow-strong p-8 animate-fade-in-up delay-100">
        <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="ui-label">宠物头像</label>
                <input type="file" name="avatar" accept="image/*" class="ui-input @error('avatar') border-danger-300 @enderror">
                <p class="mt-2 text-xs text-warm-400">上传一张正脸照，让动态流里的主角更醒目，最大 5MB。</p>
                @error('avatar')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">名字 <span class="text-danger-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="50" class="ui-input @error('name') border-danger-300 @enderror" placeholder="如：旺财、咪咪">
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
                            <option value="{{ $species->id }}" {{ old('pet_species_id') == $species->id ? 'selected' : '' }}>{{ $species->name }}</option>
                        @endforeach
                    </select>
                    @error('pet_species_id')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">品种</label>
                    <input type="text" name="breed" value="{{ old('breed') }}" maxlength="100" class="ui-input" placeholder="如：金毛、布偶猫">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">性别</label>
                    <select name="gender" class="ui-select">
                        <option value="unknown" {{ old('gender', 'unknown') === 'unknown' ? 'selected' : '' }}>未知</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>公 ♂</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>母 ♀</option>
                    </select>
                </div>
                <div>
                    <label class="ui-label">生日</label>
                    <input type="date" name="birthday" value="{{ old('birthday') }}" class="ui-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">到家日期</label>
                    <input type="date" name="adoption_date" value="{{ old('adoption_date') }}" class="ui-input">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-warm-200 hover:border-primary-300 hover:bg-primary-50 transition-all duration-200 w-full">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded-lg border-warm-300 text-primary-500 focus:ring-primary-400 focus:ring-2">
                        <span class="text-sm font-medium text-warm-700">设为默认宠物</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-warm-100">
                <button type="submit" class="ui-btn-primary flex-1 py-3.5 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    保存
                </button>
                <a href="{{ route('pets.index') }}" class="ui-btn-secondary px-6 py-3.5">
                    取消
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
