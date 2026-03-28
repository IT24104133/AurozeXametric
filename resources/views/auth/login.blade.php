@extends('layouts.app')

@section('content')
@php
  $autoSid = request('sid');
  $autoTp  = request('tp');
@endphp

<div class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-10">
  <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

    <!-- Left Brand Panel -->
    <div class="hidden lg:flex flex-col justify-between p-10 text-white relative overflow-hidden
                bg-gradient-to-br from-teal-600 via-teal-600 to-cyan-500">
      <div class="absolute inset-0 opacity-100 pointer-events-none"
           style="background:
             radial-gradient(circle at 30% 30%, rgba(255,255,255,.18), rgba(255,255,255,0) 55%),
             radial-gradient(circle at 70% 60%, rgba(255,255,255,.10), rgba(255,255,255,0) 55%);">
      </div>

      <div class="relative z-10">
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/20 p-2 flex items-center justify-center">
            <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-7 w-7 object-contain">
          </div>
          <div>
            <div class="text-xs font-semibold tracking-widest text-white/85">EXAMPORTAL</div>
            <div class="text-2xl font-extrabold leading-tight">Online Exam System</div>
          </div>
        </div>

        <div class="mt-10">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 border border-white/25 text-sm font-semibold">
            <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
            Secure • Timed • Fair
          </div>

          <div class="mt-6 text-3xl font-extrabold leading-tight">
            Welcome back 👋
          </div>
          <p class="mt-3 text-white/85 text-sm leading-relaxed max-w-sm">
            Sign in with your Student ID to access exams and results when published by admin.
          </p>

          <div class="mt-8 grid gap-4 text-sm text-white/90">
            <div class="flex items-start gap-3">
              <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/15 border border-white/20">🔒</span>
              <div>
                <div class="font-semibold">Secure access</div>
                <div class="text-white/75">Role-based access and session protection.</div>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/15 border border-white/20">⏱️</span>
              <div>
                <div class="font-semibold">Smart timer</div>
                <div class="text-white/75">Auto-submit when time ends.</div>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/15 border border-white/20">📊</span>
              <div>
                <div class="font-semibold">Controlled results</div>
                <div class="text-white/75">Visible only when admin publishes.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="relative z-10 text-xs text-white/65">
        © {{ date('Y') }} ExamPortal. All rights reserved.
      </div>
    </div>

    <!-- Right Form Panel -->
    <div class="p-8 sm:p-10">
      <!-- Mobile header -->
      <div class="lg:hidden flex items-center gap-3 mb-8">
        <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-sm">
          <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
        </div>
        <div>
          <div class="text-[11px] font-semibold tracking-widest text-teal-700">EXAMPORTAL</div>
          <div class="text-lg font-extrabold text-slate-900 leading-tight">Online Exam System</div>
        </div>
      </div>

      <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Sign in</h2>
        <p class="mt-1 text-sm text-slate-600">Use your Student ID and password.</p>
      </div>

      @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
          <div class="font-semibold text-sm mb-1">Please fix the following:</div>
          <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
          <label for="identifier" class="block text-sm font-semibold text-slate-700">Student ID</label>
          <input
            id="identifier"
            name="identifier"
            type="text"
            autocomplete="username"
            required
            value="{{ old('identifier', $autoSid) }}"
            placeholder="e.g. NGI1012"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                   focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
          >
        </div>

        <div>
          <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            value="{{ $autoTp }}"
            placeholder="Enter your password"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                   focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
          >
          @if($autoTp)
            <div class="mt-2 text-xs text-slate-500">
              Temporary password auto-filled from registration.
            </div>
          @endif
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input
              id="remember"
              name="remember"
              type="checkbox"
              class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
            >
            Remember me
          </label>
        </div>

        <button
          type="submit"
          class="w-full rounded-2xl bg-teal-600 px-4 py-3 font-extrabold text-white shadow-sm transition
                 hover:bg-teal-500 focus:outline-none focus:ring-4 focus:ring-teal-200"
        >
          Sign in
        </button>

        <div class="text-center text-xs text-slate-500">
          By signing in, you agree to the exam rules and policies.
        </div>

        @if (Route::has('register'))
          <div class="pt-4 text-center text-sm text-slate-600">
            Don’t have an account?
            <a href="{{ route('register') }}" class="font-bold text-teal-700 hover:text-teal-800">
              Register
            </a>
          </div>
        @endif

      </form>
    </div>

  </div>
</div>
@endsection
