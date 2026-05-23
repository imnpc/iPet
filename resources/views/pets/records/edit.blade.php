@extends('layouts.app')

@section('title', '编辑医疗记录 - ' . $pet->name . ' - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('pets.show', $pet) }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回宠物详情
        </a>
        <h1 class="ui-page-title">编辑医疗记录</h1>
        <p class="ui-page-subtitle">修改 {{ $pet->name }} 的就诊信息</p>
    </div>

    <div class="ui-card ui-card-shadow-strong p-8 animate-fade-in-up delay-100">
        <form action="{{ route('pets.records.update', [$pet, $record]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">记录类型 <span class="text-danger-500">*</span></label>
                    <select name="type" required class="ui-select @error('type') border-danger-300 @enderror">
                        <option value="">请选择</option>
                        <option value="vaccine" {{ old('type', $record->type) === 'vaccine' ? 'selected' : '' }}>疫苗</option>
                        <option value="checkup" {{ old('type', $record->type) === 'checkup' ? 'selected' : '' }}>体检</option>
                        <option value="illness" {{ old('type', $record->type) === 'illness' ? 'selected' : '' }}>病历</option>
                        <option value="medication" {{ old('type', $record->type) === 'medication' ? 'selected' : '' }}>用药</option>
                        <option value="surgery" {{ old('type', $record->type) === 'surgery' ? 'selected' : '' }}>手术</option>
                        <option value="grooming" {{ old('type', $record->type) === 'grooming' ? 'selected' : '' }}>美容</option>
                        <option value="other" {{ old('type', $record->type) === 'other' ? 'selected' : '' }}>其他</option>
                    </select>
                    @error('type')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">标题 <span class="text-danger-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $record->title) }}" required maxlength="200" class="ui-input @error('title') border-danger-300 @enderror" placeholder="如：年度疫苗接种">
                    @error('title')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">就诊日期 <span class="text-danger-500">*</span></label>
                    <input type="date" name="visit_date" value="{{ old('visit_date', $record->visit_date?->format('Y-m-d')) }}" required class="ui-input @error('visit_date') border-danger-300 @enderror">
                    @error('visit_date')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="ui-label">下次复诊/疫苗日期</label>
                    <input type="date" name="next_visit_date" value="{{ old('next_visit_date', $record->next_visit_date?->format('Y-m-d')) }}" class="ui-input">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">医院名称</label>
                    <input type="text" name="hospital_name" value="{{ old('hospital_name', $record->hospital_name) }}" maxlength="200" class="ui-input" placeholder="如：爱心宠物医院">
                </div>
                <div>
                    <label class="ui-label">医生姓名</label>
                    <input type="text" name="vet_name" value="{{ old('vet_name', $record->vet_name) }}" maxlength="100" class="ui-input" placeholder="如：张医生">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="ui-label">医院电话</label>
                    <input type="text" name="hospital_phone" value="{{ old('hospital_phone', $record->hospital_phone) }}" maxlength="20" class="ui-input" placeholder="如：010-12345678">
                </div>
                <div>
                    <label class="ui-label">体重 (kg)</label>
                    <input type="number" name="weight" value="{{ old('weight', $record->weight) }}" step="0.01" class="ui-input" placeholder="如：5.5">
                </div>
                <div>
                    <label class="ui-label">体温 (℃)</label>
                    <input type="number" name="temperature" value="{{ old('temperature', $record->temperature) }}" step="0.1" class="ui-input" placeholder="如：38.5">
                </div>
            </div>

            <div>
                <label class="ui-label">症状描述</label>
                <textarea name="symptoms" rows="3" class="ui-input" placeholder="描述宠物的症状表现">{{ old('symptoms', $record->symptoms) }}</textarea>
            </div>

            <div>
                <label class="ui-label">诊断结果</label>
                <textarea name="diagnosis" rows="3" class="ui-input" placeholder="医生的诊断结论">{{ old('diagnosis', $record->diagnosis) }}</textarea>
            </div>

            <div>
                <label class="ui-label">治疗方案</label>
                <textarea name="treatment" rows="3" class="ui-input" placeholder="详细的治疗方案">{{ old('treatment', $record->treatment) }}</textarea>
            </div>

            <div>
                <label class="ui-label">处方/用药说明</label>
                <textarea name="prescription" rows="3" class="ui-input" placeholder="药品名称、用量、用法">{{ old('prescription', $record->prescription) }}</textarea>
            </div>

            <div>
                <label class="ui-label">备注</label>
                <textarea name="notes" rows="3" class="ui-input" placeholder="其他需要记录的信息">{{ old('notes', $record->notes) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="ui-label">费用 (¥)</label>
                    <input type="number" name="cost" value="{{ old('cost', $record->cost) }}" step="0.01" class="ui-input" placeholder="如：200.00">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-warm-200 hover:border-primary-300 hover:bg-primary-50 transition-all duration-200 w-full">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public', $record->is_public) ? 'checked' : '' }} class="rounded-lg border-warm-300 text-primary-500 focus:ring-primary-400 focus:ring-2">
                        <div>
                            <span class="text-sm font-medium text-warm-700">公开此记录</span>
                            <p class="text-xs text-warm-400">开启后其他用户也可以查看此记录</p>
                        </div>
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
        <p class="text-sm text-warm-500 mb-4">删除后此医疗记录将无法恢复，请谨慎操作。</p>
        <form action="{{ route('pets.records.destroy', [$pet, $record]) }}" method="POST" onsubmit="return confirm('确定要删除这条医疗记录吗？此操作不可撤销！')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2.5 bg-danger-50 hover:bg-danger-100 text-danger-600 border border-danger-200 rounded-xl font-semibold transition-colors">
                删除此记录
            </button>
        </form>
    </div>
</div>
@endsection