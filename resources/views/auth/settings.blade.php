@extends('layouts.app')

@section('title', '账号设置 - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <h1 class="ui-page-title">账号设置</h1>
        <p class="ui-page-subtitle">管理您的个人信息和安全设置</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-accent-50 border border-accent-200 rounded-xl text-accent-700 text-sm animate-fade-in-up">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-danger-50 border border-danger-200 rounded-xl text-danger-700 text-sm animate-fade-in-up">
            {{ session('error') }}
        </div>
    @endif

    <!-- 基本信息 -->
    <div class="ui-card ui-card-shadow p-6 mb-6 animate-fade-in-up">
        <h2 class="ui-section-title mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            基本信息
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-warm-500 mb-1">用户名</label>
                <p class="text-warm-900 font-semibold">{{ auth()->user()->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-500 mb-1">邮箱</label>
                <p class="text-warm-900 font-semibold">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

    <!-- Passkey 管理 -->
    <div class="ui-card ui-card-shadow p-6 animate-fade-in-up delay-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-display font-bold text-warm-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                Passkey 管理
            </h2>
            <button id="add-passkey-btn" class="inline-flex items-center gap-2 bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                添加 Passkey
            </button>
        </div>

        <p class="text-sm text-warm-500 mb-6">Passkey 允许您使用 Face ID、Touch ID 或 Windows Hello 快速登录，无需记忆密码。</p>

        <!-- 添加 Passkey 表单 -->
        <div id="add-passkey-form" class="hidden mb-6 p-4 bg-accent-50 border border-accent-200 rounded-xl">
            <div class="flex gap-3">
                <input type="text" id="passkey-name" placeholder="给这个设备起个名字（如：我的 iPhone）" class="ui-input">
                <button id="confirm-add-passkey" class="ui-btn-primary px-4 py-2">
                    确认
                </button>
                <button id="cancel-add-passkey" class="ui-btn-secondary px-4 py-2 border-warm-300">
                    取消
                </button>
            </div>
        </div>

        <!-- Passkey 列表 -->
        @if($passkeys && $passkeys->count() > 0)
            <div class="space-y-3">
                @foreach($passkeys as $passkey)
                    <div class="flex items-center justify-between p-4 bg-warm-50 rounded-xl border border-warm-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-warm-900">{{ $passkey->name }}</p>
                                <p class="text-xs text-warm-500">
                                    最后使用：{{ $passkey->last_used_at ? $passkey->last_used_at->diffForHumans() : '从未使用' }}
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('user.passkeys.delete', $passkey->id) }}" method="POST" onsubmit="return confirm('确定要删除这个 Passkey 吗？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-warm-400 hover:text-danger-600 transition-colors p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-warm-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                <p class="text-warm-500 font-medium">还没有添加 Passkey</p>
                <p class="text-sm text-warm-400 mt-1">点击上方按钮添加您的第一个 Passkey</p>
            </div>
        @endif
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

    const addBtn = document.getElementById('add-passkey-btn');
    const form = document.getElementById('add-passkey-form');
    const cancelBtn = document.getElementById('cancel-add-passkey');
    const confirmBtn = document.getElementById('confirm-add-passkey');
    const nameInput = document.getElementById('passkey-name');

    addBtn.addEventListener('click', () => {
        form.classList.remove('hidden');
        nameInput.focus();
    });

    cancelBtn.addEventListener('click', () => {
        form.classList.add('hidden');
        nameInput.value = '';
    });

    confirmBtn.addEventListener('click', async () => {
        const name = nameInput.value.trim();
        if (!name) {
            alert('请输入 Passkey 名称');
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = '处理中...';

        try {
            const optionsResponse = await fetch('{{ route('user.passkeys.options') }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!optionsResponse.ok) {
                const errorData = await optionsResponse.json().catch(() => null);
                throw new Error(errorData?.message || `服务器错误 (${optionsResponse.status})`);
            }

            const options = await optionsResponse.json();

            // 将 Base64Url 字符串解码为 ArrayBuffer
            options.challenge = base64UrlToArrayBuffer(options.challenge);
            if (options.user && options.user.id) {
                options.user.id = base64UrlToArrayBuffer(options.user.id);
            }
            if (options.excludeCredentials) {
                options.excludeCredentials.forEach(cred => {
                    cred.id = base64UrlToArrayBuffer(cred.id);
                });
            }

            const credential = await navigator.credentials.create({
                publicKey: options
            });

            if (!credential) {
                throw new Error('未获取到凭证');
            }

            const passkeyData = {
                id: base64UrlEncode(credential.rawId),
                rawId: base64UrlEncode(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: base64UrlEncode(credential.response.clientDataJSON),
                    attestationObject: base64UrlEncode(credential.response.attestationObject),
                    transports: credential.response.getTransports ? credential.response.getTransports() : []
                }
            };

            const storeResponse = await fetch('{{ route('user.passkeys.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: name,
                    passkey: JSON.stringify(passkeyData)
                })
            });

            if (storeResponse.ok) {
                window.location.reload();
            } else {
                const error = await storeResponse.json();
                alert('添加失败：' + (error.message || '请重试'));
            }
        } catch (error) {
            console.error('Passkey registration error:', error);
            alert('添加失败：' + error.message);
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.textContent = '确认';
        }
    });
</script>
@endpush
@endsection
