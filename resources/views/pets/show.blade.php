@extends('layouts.app')

@section('title', $pet->name . ' - iPet')

@push('styles')
<style>
    .stat-card {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- 宠物头部信息 -->
    <div class="ui-card ui-card-shadow-strong overflow-hidden mb-8 animate-fade-in-up" style="border-radius: 1.5rem;">
        <div class="h-72 bg-gradient-to-br from-primary-100 via-primary-50 to-accent-50 relative">
            @if($pet->avatar)
                <img src="{{ $pet->avatar }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-32 h-32 text-primary-300/60" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                    </svg>
                </div>
            @endif
            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="flex items-end justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-4xl font-display font-bold text-white drop-shadow-lg">{{ $pet->name }}</h1>
                            @if($pet->is_default)
                                <span class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">默认宠物</span>
                            @endif
                        </div>
                        <p class="text-white/90 text-lg font-medium">{{ $pet->species }} · {{ $pet->breed ?: '未知品种' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-accent-50 border border-accent-200 rounded-xl text-accent-700 text-sm animate-fade-in-up">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 统计卡片 -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-5 text-center border border-blue-200/50">
                    <div class="flex justify-center">
                        @if($pet->gender === 'male')
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-2xl text-blue-600">♂</span>
                        @elseif($pet->gender === 'female')
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-pink-100 text-2xl text-pink-600">♀</span>
                        @else
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-warm-100 text-2xl text-warm-500">?</span>
                        @endif
                    </div>
                    <p class="text-sm text-warm-600 font-medium mt-1">性别</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-5 text-center border border-primary-200/50">
                    <p class="text-3xl font-display font-bold text-primary-600">{{ $pet->birthday ? $pet->birthday->age : '-' }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">年龄(岁)</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-accent-50 to-accent-100 rounded-2xl p-5 text-center border border-accent-200/50">
                    <p class="text-3xl font-display font-bold text-accent-600">{{ $pet->records->count() }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">医疗记录</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-5 text-center border border-purple-200/50">
                    <p class="text-3xl font-display font-bold text-purple-600">{{ $pet->posts_count }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">动态</p>
                </div>
            </div>

            <!-- 详细信息 -->
            <div class="border-t border-warm-100 pt-8">
                <h3 class="text-xl font-display font-bold text-warm-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    详细信息
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">物种</span>
                        <span class="font-semibold text-warm-900">{{ $pet->species }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">品种</span>
                        <span class="font-semibold text-warm-900">{{ $pet->breed ?: '未知' }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">生日</span>
                        <span class="font-semibold text-warm-900">{{ $pet->birthday ? $pet->birthday->format('Y-m-d') : '未知' }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">到家日期</span>
                        <span class="font-semibold text-warm-900">{{ $pet->adoption_date ? $pet->adoption_date->format('Y-m-d') : '未知' }}</span>
                    </div>
                </div>
            </div>

            @if($pet->metadata)
                <div class="border-t border-warm-100 pt-8 mt-8">
                    <h3 class="text-xl font-display font-bold text-warm-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        扩展信息
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        @foreach($pet->metadata as $key => $value)
                            <div class="flex justify-between py-3 border-b border-warm-100">
                                <span class="text-warm-500 font-medium">{{ $key }}</span>
                                <span class="font-semibold text-warm-900">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- 内容区域：Tab 切换 -->
    <div class="mb-8">
        <!-- Tab 导航 -->
        <div class="flex items-center justify-center gap-3 mb-8">
            <a href="{{ route('pets.show', [$pet, 'tab' => 'posts']) }}"
               class="tab-btn group inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300
               @if($activeTab !== 'records') bg-primary-500 text-white shadow-md shadow-primary-200 hover:bg-primary-600 @else bg-white text-warm-500 border border-warm-200 hover:border-primary-300 hover:text-primary-600 @endif"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                动态
                @if($petPosts->total() > 0)
                    <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-xs font-bold rounded-md
                        @if($activeTab !== 'records') bg-white/25 text-white @else bg-warm-100 text-warm-600 @endif">
                        {{ $petPosts->total() }}
                    </span>
                @endif
            </a>

            <a href="{{ route('pets.show', [$pet, 'tab' => 'records']) }}"
               class="tab-btn group inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300
               @if($activeTab === 'records') bg-accent-500 text-white shadow-md shadow-accent-200 hover:bg-accent-600 @else bg-white text-warm-500 border border-warm-200 hover:border-accent-300 hover:text-accent-600 @endif"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                医疗记录
                @if($petRecords->total() > 0)
                    <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-xs font-bold rounded-md
                        @if($activeTab === 'records') bg-white/25 text-white @else bg-warm-100 text-warm-600 @endif">
                        {{ $petRecords->total() }}
                    </span>
                @endif
            </a>
        </div>

        @if($activeTab === 'records')
            <!-- 医疗记录 Tab -->
            <div class="animate-fade-in-up">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-warm-900">医疗记录</h2>
                    @if(auth()->check() && auth()->id() === $pet->user_id)
                        <a href="{{ route('pets.records.create', $pet) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-accent-500 hover:bg-accent-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            添加记录
                        </a>
                    @endif
                </div>

                @if($petRecords->count() > 0)
                    <div class="space-y-4">
                        @foreach($petRecords as $record)
                            @php
                                $typeColors = [
                                    'vaccine' => ['bg-green-50', 'text-green-600', 'border-green-200', '疫苗'],
                                    'checkup' => ['bg-blue-50', 'text-blue-600', 'border-blue-200', '体检'],
                                    'illness' => ['bg-red-50', 'text-red-600', 'border-red-200', '病历'],
                                    'medication' => ['bg-yellow-50', 'text-yellow-600', 'border-yellow-200', '用药'],
                                    'surgery' => ['bg-purple-50', 'text-purple-600', 'border-purple-200', '手术'],
                                    'grooming' => ['bg-pink-50', 'text-pink-600', 'border-pink-200', '美容'],
                                    'other' => ['bg-warm-50', 'text-warm-600', 'border-warm-200', '其他'],
                                ];
                                $typeStyle = $typeColors[$record->type] ?? $typeColors['other'];
                            @endphp
                            <div class="record-card {{ $typeStyle[0] }} border {{ $typeStyle[2] }} rounded-2xl p-5 sm:p-6 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">

                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-white border {{ $typeStyle[2] }} {{ $typeStyle[1] }}">
                                            {{ $typeStyle[3] }}
                                        </span>
                                        @if(! $record->is_public)
                                            <span class="inline-flex items-center gap-1 text-xs text-warm-500 bg-warm-100/80 px-2.5 py-1 rounded-full border border-warm-200 font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                私有
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2.5 py-1 rounded-full border border-green-200 font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                公开
                                            </span>
                                        @endif
                                        <span class="text-sm text-warm-400 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $record->visit_date->format('Y-m-d') }}
                                        </span>
                                    </div>
                                    @if(auth()->check() && auth()->id() === $pet->user_id)
                                        <div class="flex items-center gap-2 relative z-10">
                                            <a href="{{ route('pets.records.edit', [$pet, $record]) }}" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-primary-600 transition-colors bg-white/60 hover:bg-white px-3 py-1.5 rounded-full font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                编辑
                                            </a>
                                            <form action="{{ route('pets.records.destroy', [$pet, $record]) }}" method="POST" class="inline" onsubmit="return confirm('确定要删除这条记录吗？')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 text-sm text-warm-500 hover:text-danger-600 transition-colors bg-white/60 hover:bg-white px-3 py-1.5 rounded-full font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    删除
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <h4 class="font-bold text-warm-900 mb-3 text-lg">{{ $record->title }}</h4>

                                <div class="flex flex-wrap gap-x-5 gap-y-2.5 text-sm mb-4">
                                    @if($record->hospital_name)
                                        <div class="flex items-center gap-2 text-warm-600 bg-white/50 px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 text-warm-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $record->hospital_name }}
                                        </div>
                                    @endif
                                    @if($record->vet_name)
                                        <div class="flex items-center gap-2 text-warm-600 bg-white/50 px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 text-warm-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $record->vet_name }}
                                        </div>
                                    @endif
                                    @if($record->weight)
                                        <div class="flex items-center gap-2 text-warm-600 bg-white/50 px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 text-warm-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 1m3-1v2.5M21 6l-3 1m0 0l3 1m-3-1v2.5M9 6l3 1m0 0l-3 1m3-1v2.5M15 6l-3 1m0 0l3 1m-3-1v2.5M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $record->weight }} kg
                                        </div>
                                    @endif
                                    @if($record->temperature)
                                        <div class="flex items-center gap-2 text-warm-600 bg-white/50 px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 text-warm-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                                            {{ $record->temperature }} ℃
                                        </div>
                                    @endif
                                    @if($record->cost)
                                        <div class="flex items-center gap-2 text-warm-600 bg-white/50 px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 text-warm-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            ¥{{ number_format($record->cost, 2) }}
                                        </div>
                                    @endif
                                    @if($record->next_visit_date)
                                        <div class="flex items-center gap-2 text-accent-600 bg-accent-50 px-3 py-1 rounded-full border border-accent-100">
                                            <svg class="w-4 h-4 text-accent-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            下次复诊：{{ $record->next_visit_date->format('Y-m-d') }}
                                        </div>
                                    @endif
                                </div>

                                @if($record->symptoms || $record->diagnosis || $record->treatment || $record->prescription || $record->notes)
                                    <div class="mt-4 pt-4 border-t border-warm-200 space-y-2.5 text-sm">
                                        @if($record->symptoms)
                                            <div class="text-warm-700 leading-relaxed"><span class="font-medium text-warm-800">症状：</span>{{ $record->symptoms }}</div>
                                        @endif
                                        @if($record->diagnosis)
                                            <div class="text-warm-700 leading-relaxed"><span class="font-medium text-warm-800">诊断：</span>{{ $record->diagnosis }}</div>
                                        @endif
                                        @if($record->treatment)
                                            <div class="text-warm-700 leading-relaxed"><span class="font-medium text-warm-800">治疗：</span>{{ $record->treatment }}</div>
                                        @endif
                                        @if($record->prescription)
                                            <div class="text-warm-700 leading-relaxed"><span class="font-medium text-warm-800">处方：</span>{{ $record->prescription }}</div>
                                        @endif
                                        @if($record->notes)
                                            <div class="text-warm-700 leading-relaxed"><span class="font-medium text-warm-800">备注：</span>{{ $record->notes }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 医疗记录分页 -->
                    @if($petRecords->hasPages())
                        <div class="mt-8">
                            {{ $petRecords->appends(array_merge(request()->except('page'), ['tab' => 'records']))->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 animate-fade-in-up">
                        <div class="w-20 h-20 mx-auto mb-5 bg-warm-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-warm-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-display font-bold text-warm-900 mb-2">暂无医疗记录</h3>
                        <p class="text-warm-500 mb-6 text-sm">
                            @if(auth()->check() && auth()->id() === $pet->user_id)
                                为 {{ $pet->name }} 添加第一条医疗记录
                            @else
                                {{ $pet->name }} 的主人还没有公开医疗记录
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        @else
            <!-- 动态 Tab（默认） -->
            <div class="animate-fade-in-up">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-display font-bold text-warm-900">动态</h2>
                    @if(auth()->check() && auth()->id() === $pet->user_id)
                        <a href="{{ route('posts.create', ['pet_id' => $pet->id]) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-500 hover:bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            发动态
                        </a>
                    @endif
                </div>

                @if($petPosts->count() > 0)
                    <div class="space-y-5">
                        @foreach($petPosts as $post)
                            @php
                                $postShowUrl = route('posts.show', $post);
                                $speciesLabel = filled($post->pet?->species) ? trim($post->pet->species) : '未分类';
                            @endphp

                            <div class="post-card js-post-card ui-card ui-card-shadow overflow-hidden cursor-pointer animate-fade-in-up transition-all duration-300 hover:-translate-y-1 hover:shadow-lg" data-href="{{ $postShowUrl }}">
                                <div class="px-5 py-4 sm:px-6 sm:py-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <x-post-header :post="$post" :species-label="$speciesLabel" :show-pet-link="true" :show-dropdown="false" :stop-propagation="true" />
                                        </div>
                                        @if(auth()->check() && auth()->id() === $pet->user_id)
                                            <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                                <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center gap-1 text-xs text-warm-500 hover:text-primary-600 transition-colors bg-warm-50 hover:bg-primary-50 px-2.5 py-1 rounded-lg font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    编辑
                                                </a>
                                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('确定要删除这条动态吗？')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 text-xs text-warm-500 hover:text-danger-600 transition-colors bg-warm-50 hover:bg-danger-50 px-2.5 py-1 rounded-lg font-medium">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        删除
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>

                                    <x-post-content :content="$post->content" :expandable="true" />

                                    @if($post->location)
                                        <p class="text-sm text-warm-500 mb-4 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $post->location }}
                                        </p>
                                    @endif

                                    @if($post->tags && $post->tags->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($post->tags as $tag)
                                                <a href="{{ route('posts.index', ['tag' => $tag->name]) }}" onclick="event.stopPropagation()" class="text-xs bg-warm-100 text-warm-600 px-3 py-1.5 rounded-lg font-medium hover:bg-primary-100 hover:text-primary-600 transition-colors">
                                                    #{{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div onclick="event.stopPropagation()">
                                        <x-post-media :post="$post" mode="feed" />
                                    </div>

                                    <x-post-stats :post="$post" layout="weibo" :show-view="true" :show-like="true" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $petPosts->appends(array_merge(request()->except('page'), ['tab' => 'posts']))->links() }}
                    </div>
                @else
                    <div class="text-center py-16 animate-fade-in-up">
                        <div class="w-20 h-20 mx-auto mb-5 bg-warm-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-warm-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-display font-bold text-warm-900 mb-2">还没有动态</h3>
                        <p class="text-warm-500 text-sm">{{ $pet->name }} 还没有发布过动态</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('.js-post-card').forEach((card) => {
        card.addEventListener('click', () => {
            window.location.href = card.dataset.href;
        });
    });

    document.querySelectorAll('.js-post-expand').forEach((expandButton) => {
        const content = expandButton.closest('div')?.querySelector('.js-post-content');
        if (!content) {
            return;
        }

        const computedStyle = window.getComputedStyle(content);
        const lineHeight = Number.parseFloat(computedStyle.lineHeight || '0');
        const estimatedLineCount = lineHeight > 0 ? Math.round(content.scrollHeight / lineHeight) : 0;
        const textLength = (content.textContent || '').trim().length;
        const shouldShowExpand = content.scrollHeight > content.clientHeight + 4 || estimatedLineCount > 4 || textLength > 140;

        if (!shouldShowExpand) {
            expandButton.classList.add('hidden');
            return;
        }

        expandButton.addEventListener('click', () => {
            const expanded = content.dataset.expanded === 'true';
            if (expanded) {
                content.dataset.expanded = 'false';
                content.classList.add('max-h-[6.5rem]', 'overflow-hidden');
                expandButton.textContent = '展开全文';
            } else {
                content.dataset.expanded = 'true';
                content.classList.remove('max-h-[6.5rem]', 'overflow-hidden');
                expandButton.textContent = '收起';
            }
        });
    });
</script>
@endsection
