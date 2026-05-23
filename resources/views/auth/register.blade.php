@extends('layouts.app')

@section('title', '注册 - iPet')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-md animate-fade-in-up">
        <!-- Logo 和标题 -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-accent-400 to-accent-600 rounded-2xl shadow-xl mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-warm-900">创建账号</h1>
            <p class="text-warm-500 mt-2">加入 iPet，开始您的宠物管理之旅</p>
        </div>

        <!-- 注册表单 -->
        <div class="bg-white rounded-3xl shadow-xl border border-warm-100 p-8">
            <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-2">用户名</label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 @error('name') border-danger-300 @enderror" placeholder="您的用户名">
                    @error('name')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-2">邮箱</label>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 @error('email') border-danger-300 @enderror" placeholder="your@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-2">密码</label>
                    <input type="password" name="password" required class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 @error('password') border-danger-300 @enderror" placeholder="至少 8 个字符">
                    @error('password')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-2">确认密码</label>
                    <input type="password" name="password_confirmation" required class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200" placeholder="再次输入密码">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                    注册
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-warm-500 text-sm">
                    已有账号？
                    <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">立即登录</a>
                </p>
            </div>
        </div>

        <!-- 返回首页 -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                返回首页
            </a>
        </div>
    </div>
</div>
@endsection
