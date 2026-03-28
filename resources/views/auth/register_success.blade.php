@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-10">
  <div class="w-full max-w-2xl rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="p-8 sm:p-10">

      <div class="flex items-center gap-3">
        <div class="h-12 w-12 rounded-2xl bg-teal-600 flex items-center justify-center shadow-sm">
          <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-7 w-7 object-contain">
        </div>
        <div>
          <div class="text-xs font-semibold tracking-widest text-teal-700">EXAMPORTAL</div>
          <div class="text-2xl font-extrabold text-slate-900">Registration successful ✅</div>
        </div>
      </div>

      <p class="mt-4 text-slate-600 text-sm">
        Copy your Student ID and Temporary Password now. You will need them to log in.
      </p>

      <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
        <div class="text-sm text-emerald-900 font-semibold">Your Login Credentials</div>

        <div class="mt-4 grid gap-3">
          <div class="rounded-xl bg-white border border-emerald-100 p-4 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="text-xs text-slate-500 font-semibold">STUDENT ID</div>
              <div class="font-extrabold text-slate-900 break-all" id="sid">{{ $student_id }}</div>
            </div>
            <button type="button"
              onclick="copyText('{{ $student_id }}'); copiedSid=true; showCopiedToast();"
              class="shrink-0 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-500 transition">
              Copy
            </button>
          </div>

          <div class="rounded-xl bg-white border border-emerald-100 p-4 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="text-xs text-slate-500 font-semibold">TEMP PASSWORD</div>
              <div class="font-extrabold text-slate-900 break-all" id="tp">{{ $temp_password }}</div>
            </div>
            <button type="button"
              onclick="copyText('{{ $temp_password }}'); copiedPw=true; showCopiedToast();"
              class="shrink-0 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-500 transition">
              Copy
            </button>
          </div>
        </div>

        <div class="mt-4 text-xs text-emerald-800/80">
          ⚠️ After login, go to <b>Change Password</b> and set your own password.
        </div>
      </div>

      <!-- Button must verify copied -->
      <button
        type="button"
        onclick="goLogin()"
        class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-teal-600 px-4 py-3 font-extrabold text-white shadow-sm transition hover:bg-teal-500">
        Go to Login (Auto-fill)
      </button>

    </div>
  </div>
</div>

<!-- Copy warning modal -->
<div id="warnModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="relative w-full max-w-md rounded-3xl bg-white shadow-xl border border-slate-200 overflow-hidden">
    <div class="p-6">
      <div class="flex items-start gap-3">
        <div class="h-10 w-10 rounded-2xl bg-amber-100 flex items-center justify-center text-lg">⚠️</div>
        <div>
          <div class="text-lg font-extrabold text-slate-900">Please copy first</div>
          <p class="mt-1 text-sm text-slate-600">
            You must copy your <b>Student ID</b> and <b>Temporary Password</b> before going to login.
          </p>
        </div>
      </div>

      <div class="mt-5 flex gap-3">
        <button
          type="button"
          onclick="closeWarn()"
          class="flex-1 rounded-2xl bg-teal-600 px-4 py-3 font-extrabold text-white hover:bg-teal-500 transition">
          OK
        </button>
      </div>
    </div>
  </div>
</div>

<!-- small toast -->
<div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 hidden z-50">
  <div class="rounded-2xl bg-slate-900 text-white text-sm font-semibold px-4 py-2 shadow-lg">
    Copied ✅
  </div>
</div>

<script>
  let copiedSid = false;
  let copiedPw  = false;

  function copyText(text) {
    navigator.clipboard.writeText(text);
  }

  function showCopiedToast() {
    const t = document.getElementById('toast');
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 1200);
  }

  function openWarn() {
    const m = document.getElementById('warnModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
  }

  function closeWarn() {
    const m = document.getElementById('warnModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
  }

  function goLogin() {
    if (!(copiedSid && copiedPw)) {
      openWarn();
      return;
    }

    const sid = @json($student_id);
    const tp  = @json($temp_password);
    window.location.href = "{{ route('login') }}" + "?sid=" + encodeURIComponent(sid) + "&tp=" + encodeURIComponent(tp);
  }
</script>
@endsection
