@extends('layouts.app')

@section('title', 'iPet - 记录每个毛孩子的成长瞬间')

@section('content')
<div class="relative overflow-hidden">
    <!-- 背景装饰 -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-warm-50 to-accent-50"></div>
    <div class="absolute top-20 left-10 w-72 h-72 bg-primary-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent-200/20 rounded-full blur-3xl"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="text-center mb-20 animate-fade-in-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-6">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/></svg>
                宠物管理新体验
            </div>
            <h1 class="text-5xl sm:text-6xl font-display font-bold text-warm-900 mb-6 leading-tight">
                记录每个<br>
                <span class="bg-gradient-to-r from-primary-500 via-primary-600 to-accent-500 bg-clip-text text-transparent">毛孩子的成长瞬间</span>
            </h1>
            <p class="text-xl text-warm-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                iPet 是您的宠物管理助手，帮助您管理多只宠物、记录医疗档案、分享美好动态。
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ route('pets.index') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/></svg>
                        管理宠物
                    </a>
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-warm-50 text-warm-700 px-8 py-4 rounded-xl font-bold text-lg border-2 border-warm-200 hover:border-primary-300 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        浏览动态
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                        开始使用
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-warm-50 text-warm-700 px-8 py-4 rounded-xl font-bold text-lg border-2 border-warm-200 hover:border-primary-300 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        注册账号
                    </a>
                @endauth
            </div>
        </div>

        <!-- 功能卡片 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl border border-warm-100 hover:border-primary-200 transition-all duration-500 hover:-translate-y-2 animate-fade-in-up delay-100">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-display font-bold text-warm-900 mb-3 text-center">多宠物管理</h3>
                <p class="text-warm-600 text-center leading-relaxed">支持管理多只宠物，每只都有独立的档案和医疗记录。</p>
            </div>
            
            <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl border border-warm-100 hover:border-accent-200 transition-all duration-500 hover:-translate-y-2 animate-fade-in-up delay-200">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-accent-100 to-accent-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-display font-bold text-warm-900 mb-3 text-center">医疗记录</h3>
                <p class="text-warm-600 text-center leading-relaxed">记录疫苗、体检、病历等医疗信息，到期自动提醒。</p>
            </div>
            
            <div class="group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl border border-warm-100 hover:border-purple-200 transition-all duration-500 hover:-translate-y-2 animate-fade-in-up delay-300">
                <div class="w-16 h-16 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-display font-bold text-warm-900 mb-3 text-center">动态分享</h3>
                <p class="text-warm-600 text-center leading-relaxed">分享照片和视频，记录与宠物的美好时光。</p>
            </div>
        </div>
    </div>
</div>
@endsection
