@extends('layouts.app')

@section('title', '添加医疗记录 - ' . $pet->name . ' - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('pets.show', $pet) }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回宠物详情
        </a>
        <h1 class="ui-page-title">添加医疗记录</h1>
        <p class="ui-page-subtitle">为 {{ $pet->name }} 添加就诊、疫苗或健康记录</p>
    </div>

    <div class="ui-card ui-card-shadow-strong p-8 animate-fade-in-up delay-100">
        <form action="{{ route('pets.records.store', $pet) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">记录类型 <span class="text-danger-500">*</span></label>
                    <select name="pet_record_type_id" required class="ui-select @error('pet_record_type_id') border-danger-300 @enderror">
                        <option value="">请选择</option>
                        @foreach(\App\Models\PetRecordType::where('is_enabled', true)->orderBy('sort_order')->get() as $type)
                            <option value="{{ $type->id }}" {{ old('pet_record_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('pet_record_type_id')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">记录标题 <span class="text-danger-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="200" class="ui-input @error('title') border-danger-300 @enderror" placeholder="如： annual vaccination">
                    @error('title')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">就诊日期 <span class="text-danger-500">*</span></label>
                    <input type="date" name="visit_date" value="{{ old('visit_date') }}" required class="ui-input @error('visit_date') border-danger-300 @enderror">
                    @error('visit_date')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">下次复诊/疫苗日期</label>
                    <input type="date" name="next_visit_date" value="{{ old('next_visit_date') }}" class="ui-input @error('next_visit_date') border-danger-300 @enderror">
                    @error('next_visit_date')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">医院名称</label>
                    <input type="text" name="hospital_name" value="{{ old('hospital_name') }}" maxlength="200" class="ui-input @error('hospital_name') border-danger-300 @enderror" placeholder="如：XX宠物医院">
                    @error('hospital_name')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">医生姓名</label>
                    <input type="text" name="vet_name" value="{{ old('vet_name') }}" maxlength="100" class="ui-input @error('vet_name') border-danger-300 @enderror" placeholder="医生姓名">
                    @error('vet_name')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="ui-label">医院电话</label>
                    <input type="text" name="hospital_phone" value="{{ old('hospital_phone') }}" maxlength="20" class="ui-input @error('hospital_phone') border-danger-300 @enderror" placeholder="电话号码">
                    @error('hospital_phone')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">体重 (kg)</label>
                    <input type="number" name="weight" value="{{ old('weight') }}" step="0.01" class="ui-input @error('weight') border-danger-300 @enderror" placeholder="如：5.5">
                    @error('weight')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">体温 (℃)</label>
                    <input type="number" name="temperature" value="{{ old('temperature') }}" step="0.1" class="ui-input @error('temperature') border-danger-300 @enderror" placeholder="如：38.5">
                    @error('temperature')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="ui-label">症状描述</label>
                <textarea name="symptoms" rows="3" class="ui-input @error('symptoms') border-danger-300 @enderror" placeholder="描述宠物的症状">{{ old('symptoms') }}</textarea>
                @error('symptoms')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">诊断结果</label>
                <textarea name="diagnosis" rows="3" class="ui-input @error('diagnosis') border-danger-300 @enderror" placeholder="医生的诊断结果">{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">治疗方案</label>
                <textarea name="treatment" rows="3" class="ui-input @error('treatment') border-danger-300 @enderror" placeholder="治疗方案">{{ old('treatment') }}</textarea>
                @error('treatment')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">处方/用药说明</label>
                <textarea name="prescription" rows="3" class="ui-input @error('prescription') border-danger-300 @enderror" placeholder="处方或用药说明">{{ old('prescription') }}</textarea>
                @error('prescription')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">备注</label>
                <textarea name="notes" rows="3" class="ui-input @error('notes') border-danger-300 @enderror" placeholder="其他备注信息">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">费用 (¥)</label>
                    <input type="number" name="cost" value="{{ old('cost') }}" step="0.01" class="ui-input @error('cost') border-danger-300 @enderror" placeholder="如：200">
                    @error('cost')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-warm-200 hover:border-primary-300 hover:bg-primary-50 transition-all duration-200 w-full">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }} class="rounded-lg border-warm-300 text-primary-500 focus:ring-primary-400 focus:ring-2">
                        <div>
                            <span class="text-sm font-medium text-warm-700">公开此记录</span>
                            <p class="text-xs text-warm-400">勾选后其他用户也可查看此记录</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-warm-100">
                <button type="submit" class="ui-btn-primary flex-1 py-3.5 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    添加记录
                </button>
                <a href="{{ route('pets.show', $pet) }}" class="ui-btn-secondary px-6 py-3.5">
                    取消
                </a>
            </div>
        </form>
    </div>
</div>
@endsection