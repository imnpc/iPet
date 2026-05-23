@extends('layouts.app')

@section('title', '登录 - iPet')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-md animate-fade-in-up">
        <!-- Logo 和标题 -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl shadow-xl mb-4">
                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-display font-bold text-warm-900">欢迎回来</h1>
            <p class="text-warm-500 mt-2">登录您的 iPet 账号</p>
        </div>

        <!-- 登录卡片 -->
        <div class="bg-white rounded-3xl shadow-xl border border-warm-100 p-8">
            @if(session('error'))
                <div class="mb-6 p-4 bg-danger-50 border border-danger-200 rounded-xl text-danger-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-accent-50 border border-accent-200 rounded-xl text-accent-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 账号密码登录表单 -->
            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-2">邮箱</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 @error('email') border-danger-300 @enderror" placeholder="your@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-warm-700 mb-2">密码</label>
                    <input type="password" name="password" required class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200" placeholder="输入密码">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded-lg border-warm-300 text-primary-500 focus:ring-primary-400 focus:ring-2">
                        <span class="text-sm text-warm-600">记住我</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                    登录
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-warm-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-warm-500 font-medium">或者</span>
                </div>
            </div>

            <!-- Passkey 登录按钮 -->
            <button id="passkey-login-btn" class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                使用 Passkey 登录
            </button>

            <!-- 注册链接 -->
            <div class="mt-6 text-center">
                <p class="text-warm-500 text-sm">
                    还没有账号？
                    <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-semibold">立即注册</a>
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

@push('scripts')
<script>
    function base64UrlEncode(buffer) {
        let binary = '';
        const bytes = new Uint8Array(buffer);
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary)
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=+$/, '');
    }

    function base64UrlToArrayBuffer(base64url) {
        let base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        while (base64.length % 4) {
            base64 += '=';
        }
        const binary = atob(base64);
        const buffer = new ArrayBuffer(binary.length);
        const bytes = new Uint8Array(buffer);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return buffer;
    }

    function normalizeRequestOptions(rawOptions) {
        const options = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

        options.challenge = base64UrlToArrayBuffer(options.challenge);
        if (options.allowCredentials) {
            options.allowCredentials.forEach((cred) => {
                cred.id = base64UrlToArrayBuffer(cred.id);
            });
        }

        return options;
    }

    document.getElementById('passkey-login-btn').addEventListener('click', async function () {
        try {
            const optionsResponse = await fetch('/passkeys/authentication-options', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!optionsResponse.ok) {
                throw new Error('获取认证选项失败');
            }

            const options = normalizeRequestOptions(await optionsResponse.json());

            const credential = await navigator.credentials.get({
                publicKey: options
            });

            if (!credential) {
                throw new Error('未获取到凭证');
            }

            const credentialData = {
                id: base64UrlEncode(credential.rawId),
                rawId: base64UrlEncode(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: base64UrlEncode(credential.response.clientDataJSON),
                    authenticatorData: base64UrlEncode(credential.response.authenticatorData),
                    signature: base64UrlEncode(credential.response.signature),
                    userHandle: credential.response.userHandle ? base64UrlEncode(credential.response.userHandle) : null
                }
            };

            const loginResponse = await fetch('/passkeys/authenticate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    start_authentication_response: JSON.stringify(credentialData)
                })
            });

            if (loginResponse.ok) {
                window.location.href = '{{ route("home") }}';
            } else {
                alert('登录失败，请重试');
            }
        } catch (error) {
            console.error('Passkey login error:', error);
            alert('登录失败：' + error.message);
        }
    });
</script>
@endpush
@endsection
