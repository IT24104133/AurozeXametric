@php
    $isAdmin = ($user?->role ?? null) === 'admin';

    $school = $user?->schoolName
        ?? $user?->school
        ?? $user?->school_name
        ?? $user?->department
        ?? $user?->faculty
        ?? $user?->school?->name
        ?? $user?->profile->school
        ?? $user?->profile->school_name
        ?? null;

    $phone = $user?->phone
        ?? $user?->contactNumber
        ?? $user?->contact
        ?? $user?->mobile
        ?? $user?->tel
        ?? $user?->studentPhone
        ?? $user?->contact_number
        ?? $user?->profile->phone
        ?? $user?->profile->contact_number
        ?? null;
@endphp

<div x-cloak x-show="profileOpen" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/50" @click.self="profileOpen = false" @keydown.escape.window="profileOpen = false">
    <div class="relative w-full max-w-5xl px-4 py-6 sm:px-8" x-trap.noscroll="profileOpen">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v1h16v-1c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">{{ $isAdmin ? 'Admin Portal' : 'Student Portal' }}</p>
                            <h2 class="text-2xl font-semibold text-slate-900">Welcome back, <span id="profile-welcome-name">{{ $user?->full_name ?? $user?->first_name ?? $user?->name ?? $user?->student_id ?? 'User' }}</span>!</h2>
                        </div>
                    </div>
                    <button type="button" @click="profileOpen = false" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="ml-2">Close</span>
                    </button>
                </div>

                @if($isAdmin)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Role</p>
                                <p class="text-lg font-semibold text-slate-900">Admin</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Name</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $user?->full_name ?? $user?->name ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-sky-50 border border-sky-100">
                            <div class="w-10 h-10 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12h.01M12 12h.01M8 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.8L3 20l1.8-4a8.853 8.853 0 01-.8-3.2c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Email</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $user?->email ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-100">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h2l1 5a2 2 0 002 2h6a2 2 0 002-2l1-5h2M16 11V7M8 11V7M9 21h6M10 17h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Contact Number</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $phone ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-sky-50 border border-sky-100">
                            <div class="w-10 h-10 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Student IT Number</p>
                                <p id="profile-student-id" class="text-lg font-semibold text-slate-900">{{ $user?->student_id ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Full Name</p>
                                <p id="profile-fullname" class="text-lg font-semibold text-slate-900">{{ $user?->full_name ?? $user?->name ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">School</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $school ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-100">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h2l1 5a2 2 0 002 2h6a2 2 0 002-2l1-5h2M16 11V7M8 11V7M9 21h6M10 17h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Contact Number</p>
                                <p id="profile-contact-number" class="text-lg font-semibold text-slate-900">{{ $phone ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$isAdmin)
                    <div class="flex flex-col sm:flex-row gap-3 mb-10">
                        <button type="button" @click="$refs.resetSection.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 text-base font-semibold text-white bg-gradient-to-r from-sky-500 to-indigo-600 rounded-xl shadow-md hover:from-sky-600 hover:to-indigo-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11v2m0 4h.01M5.93 20h12.14A2.93 2.93 0 0021 17.07V6.93A2.93 2.93 0 0018.07 4H5.93A2.93 2.93 0 003 6.93v10.14A2.93 2.93 0 005.93 20z" />
                            </svg>
                            Reset Password
                        </button>
                        <a href="{{ route('student.results.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 text-base font-semibold text-sky-600 border border-sky-200 rounded-xl hover:border-sky-300 hover:text-sky-700 bg-sky-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                            </svg>
                            View Marks
                        </a>
                    </div>
                @endif

                <div class="border border-slate-100 rounded-xl p-6" id="reset-password-section" x-ref="resetSection">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-.8-1.6l-5.2-3.9a2 2 0 00-2.4 0L6.8 11.4A2 2 0 006 13v6a2 2 0 002 2zM12 11a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs uppercase font-semibold tracking-wide text-slate-500">Security</p>
                            <h3 class="text-lg font-semibold text-slate-900">Reset Password</h3>
                        </div>
                    </div>

                    <form x-data="{
                            oldPassword: '',
                            newPassword: '',
                            confirmPassword: '',
                            showOldPw: false,
                            showNewPw: false,
                            showConfirmPw: false,
                            get mismatch() { return this.confirmPassword.length > 0 && this.newPassword !== this.confirmPassword; },
                            get disabled() {
                                return !this.oldPassword || !this.newPassword || !this.confirmPassword || this.newPassword.length < 8 || this.mismatch;
                            }
                        }"
                        x-ref="passwordForm"
                        @submit.prevent="if (!disabled) { $refs.passwordForm.submit(); }"
                        method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Old Password</span>
                                <div class="mt-1 relative">
                                    <input x-model="oldPassword" :type="showOldPw ? 'text' : 'password'" name="current_password" class="w-full rounded-lg border-slate-200 focus:border-sky-500 focus:ring-sky-500" required autocomplete="current-password">
                                    <button type="button" @click="showOldPw = !showOldPw" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">New Password</span>
                                <div class="mt-1 relative">
                                    <input x-model="newPassword" :type="showNewPw ? 'text' : 'password'" name="password" class="w-full rounded-lg border-slate-200 focus:border-sky-500 focus:ring-sky-500" required autocomplete="new-password" minlength="8">
                                    <button type="button" @click="showNewPw = !showNewPw" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Confirm New Password</span>
                                <div class="mt-1 relative">
                                    <input x-model="confirmPassword" :type="showConfirmPw ? 'text' : 'password'" name="password_confirmation" class="w-full rounded-lg border-slate-200 focus:border-sky-500 focus:ring-sky-500" required autocomplete="new-password" minlength="8">
                                    <button type="button" @click="showConfirmPw = !showConfirmPw" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <p x-show="mismatch" class="mt-1 text-sm text-red-600">Passwords do not match</p>
                            </label>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" :disabled="disabled" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-sky-600 rounded-lg hover:bg-sky-700 disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 2c-2.67 0-8 1.34-8 4v1h16v-1c0-2.66-5.33-4-8-4z" />
                                </svg>
                                Reset Password
                            </button>
                            <button type="button" @click="profileOpen = false" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                                </svg>
                                Cancel
                            </button>
                        </div>
                        @if (session('status'))
                            <p class="text-sm text-emerald-600">{{ session('status') }}</p>
                        @endif
                        @if ($errors->any())
                            <p class="text-sm text-red-600">{{ $errors->first() }}</p>
                        @endif
                    </form>
                </div>
                <script>
                    // Listen for client-side updates to student profile and patch server-rendered modal values
                    console.log('Attaching student.updated listener');
                    window.addEventListener('student.updated', function (ev) {
                        console.log('student.updated event received', ev && ev.detail);
                        try {
                            // support multiple shapes: detail may be the model object itself, or {user:...} or {student:...}
                            let s = ev.detail || {};
                            if (s.user) s = s.user;
                            if (s.student) s = s.student;

                            // prefer full_name, fall back to name/first_name
                            const displayName = s.full_name || s.name || s.first_name || s.fullName || '';
                            if (displayName) {
                                const welcome = document.getElementById('profile-welcome-name');
                                if (welcome) welcome.textContent = displayName;
                                const fullname = document.getElementById('profile-fullname');
                                if (fullname) fullname.textContent = displayName;
                                // also update topbar username if present
                                const topbar = document.getElementById('topbar-username');
                                if (topbar) topbar.textContent = displayName;
                            }

                            if (s.student_id) {
                                const sid = document.getElementById('profile-student-id');
                                if (sid) sid.textContent = s.student_id;
                            }
                            if (s.contact_number) {
                                const contact = document.getElementById('profile-contact-number');
                                if (contact) contact.textContent = s.contact_number;
                            }
                        } catch (e) {
                            console.warn('Failed to apply student.updated event:', e);
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>
