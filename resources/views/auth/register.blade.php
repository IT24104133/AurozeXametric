@extends('layouts.app')

@section('content')
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
            <div class="text-2xl font-extrabold leading-tight">Student Registration</div>
          </div>
        </div>

        <div class="mt-10">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 border border-white/25 text-sm font-semibold">
            <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
            Auto Student ID + Temp Password
          </div>

          <p class="mt-6 text-white/85 text-sm leading-relaxed max-w-sm">
            After registration, we will generate your Student ID and a temporary password.
            Use them to log in and then change your password.
          </p>
        </div>
      </div>

      <div class="relative z-10 text-xs text-white/65">
        © {{ date('Y') }} ExamPortal. All rights reserved.
      </div>
    </div>

    <!-- Right Form Panel -->
    <div class="p-8 sm:p-10">
      <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Create account</h2>
        <p class="mt-1 text-sm text-slate-600">No email required. We will generate your Student ID and temp password.</p>
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

      <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
          <label class="block text-sm font-semibold text-slate-700">Full Name</label>
          <input
            name="full_name"
            type="text"
            required
            value="{{ old('full_name') }}"
            placeholder="Your full name"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                   focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
          >
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">School / Institute (optional)</label>
          <input
            name="school_name"
            type="text"
            value="{{ old('school_name') }}"
            placeholder="Your school or institute"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                   focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
          >
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Contact Number (optional)</label>
          <input
            name="contact_number"
            type="text"
            value="{{ old('contact_number') }}"
            placeholder="07XXXXXXXX"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                   focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
          >
        </div>

        <button
          type="submit"
          class="w-full rounded-2xl bg-teal-600 px-4 py-3 font-extrabold text-white shadow-sm transition
                 hover:bg-teal-500 focus:outline-none focus:ring-4 focus:ring-teal-200"
        >
          Register & Get Student ID
        </button>

        <div class="pt-2 text-center text-sm text-slate-600">
          Already have an account?
          <a href="{{ route('login') }}" class="font-bold text-teal-700 hover:text-teal-800">Sign in</a>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
