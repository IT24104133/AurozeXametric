<!-- Student Onboarding Modal - First Login Flow -->
@php
    $user = auth()->user();
    $needsPasswordChange = is_null($user->password_changed_at);
    $needsProfileCompletion = is_null($user->profile_completed_at);
    $needsOnboarding = $needsPasswordChange || $needsProfileCompletion;

    // Determine best display name: prefer fullName / first+last, then name, then studentName, fallback to student_id
    $displayName = null;
    if (!empty($user->full_name)) {
        $displayName = $user->full_name;
    } elseif (!empty($user->first_name) || !empty($user->last_name)) {
        $displayName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    } elseif (!empty($user->name)) {
        $displayName = $user->name;
    } elseif (!empty($user->student_id)) {
        $displayName = $user->student_id;
    } else {
        $displayName = 'Student';
    }

    // Compute default first/last name to satisfy backend required fields
    $nameParts = preg_split('/\s+/', trim($user->name ?? '')) ?: [];
    $defaultFirstName = $user->first_name ?? ($nameParts[0] ?? '');
    if (!empty($user->last_name)) {
        $defaultLastName = $user->last_name;
    } else {
        $defaultLastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $defaultFirstName;
    }
@endphp

<div x-data="studentOnboarding()" x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>
    
    <!-- Modal Container -->
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Student Portal</p>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Welcome back, <span x-text="student.full_name || student.student_id"></span>!</h2>
            </div>
            <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            
            <!-- Profile Section -->
            <div x-show="!profileSubmitted">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Complete Your Profile</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-6 bg-slate-50 p-4 rounded-lg">
                    <!-- Student ID (readonly) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Student ID</label>
                        <p class="text-sm font-medium text-slate-900">{{ $user->student_id }}</p>
                    </div>
                    
                    <!-- Full Name (editable) -->
                    <div>
                        <label for="full_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Full Name</label>
                        <input
                            type="text"
                            id="full_name"
                            x-model="profile.full_name"
                            placeholder="Enter your full name"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        <p class="text-xs text-red-600 mt-1" x-show="errors.full_name" x-text="errors.full_name"></p>
                    </div>
                </div>

                <!-- Editable Profile Fields -->
                <form @submit.prevent="updateProfile()" class="space-y-4">
                    <!-- School Name -->
                    <div>
                        <label for="school_name" class="block text-sm font-medium text-slate-700 mb-1">School Name <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            id="school_name" 
                            x-model="profile.school_name"
                            placeholder="Enter your school name"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            required>
                        <p class="text-xs text-red-600 mt-1" x-show="errors.school_name" x-text="errors.school_name"></p>
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-slate-700 mb-1">Contact Number <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            id="contact_number" 
                            x-model="profile.contact_number"
                            placeholder="Enter your contact number"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            required>
                        <p class="text-xs text-red-600 mt-1" x-show="errors.contact_number" x-text="errors.contact_number"></p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        :disabled="!profile.full_name || !profile.school_name || !profile.contact_number || isSubmitting"
                        class="w-full px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed transition">
                        <span x-show="!isSubmitting">Next: Change Password</span>
                        <span x-show="isSubmitting">Saving...</span>
                    </button>
                </form>
            </div>

            <!-- Password Section -->
            <div x-show="profileSubmitted">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Create a Secure Password</h3>

                <form @submit.prevent="updatePassword()" class="space-y-4">
                    
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">Current Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                :type="passwordData.showCurrent ? 'text' : 'password'" 
                                id="current_password" 
                                x-model="passwordData.current_password"
                                placeholder="Enter your current password"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required>
                            <button 
                                type="button" 
                                @click="passwordData.showCurrent = !passwordData.showCurrent"
                                class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-700 transition">
                                <svg x-show="!passwordData.showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="passwordData.showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-red-600 mt-1" x-show="errors.current_password" x-text="errors.current_password"></p>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-slate-700 mb-1">New Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                :type="passwordData.showNew ? 'text' : 'password'" 
                                id="new_password" 
                                x-model="passwordData.new_password"
                                minlength="8"
                                placeholder="At least 8 characters"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required>
                            <button 
                                type="button" 
                                @click="passwordData.showNew = !passwordData.showNew"
                                class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-700 transition">
                                <svg x-show="!passwordData.showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="passwordData.showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">Minimum 8 characters</p>
                        <p class="text-xs text-red-600 mt-1" x-show="errors.new_password" x-text="errors.new_password"></p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                :type="passwordData.showConfirm ? 'text' : 'password'" 
                                id="confirm_password" 
                                x-model="passwordData.confirm_password"
                                minlength="8"
                                placeholder="Repeat your password"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{'border-red-500': passwordMismatch}"
                                required>
                            <button 
                                type="button" 
                                @click="passwordData.showConfirm = !passwordData.showConfirm"
                                class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-700 transition">
                                <svg x-show="!passwordData.showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="passwordData.showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p x-show="passwordMismatch" class="text-xs text-red-600 mt-1">Passwords do not match</p>
                        <p class="text-xs text-red-600 mt-1" x-show="errors.password" x-text="errors.password"></p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        :disabled="!canSubmitPassword || isSubmitting"
                        class="w-full px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed transition">
                        <span x-show="!isSubmitting">Save & Continue to Dashboard</span>
                        <span x-show="isSubmitting">Saving...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function studentOnboarding() {
    return {
        showModal: @json($needsOnboarding),
        // If profile completion already exists on the backend, consider profile submitted
        profileSubmitted: @json(!$needsProfileCompletion),
        passwordDone: @json(!$needsPasswordChange),
        isSubmitting: false,
        errors: {},
        
        // Current persisted student snapshot
        student: {
            student_id: '{{ $user->student_id }}',
            full_name: '{{ $user->full_name ?? $displayName }}',
            school_name: '{{ $user->school_name ?? "" }}',
            contact_number: '{{ $user->contact_number ?? "" }}',
        },

        // Editable profile fields (local form state)
        profile: {
            full_name: '{{ $user->full_name ?? $displayName }}',
            school_name: '{{ $user->school_name ?? "" }}',
            contact_number: '{{ $user->contact_number ?? "" }}',
        },
        
        passwordData: {
            current_password: '',
            new_password: '',
            confirm_password: '',
            showCurrent: false,
            showNew: false,
            showConfirm: false,
        },

        get passwordMismatch() {
            return this.passwordData.confirm_password.length > 0 && 
                   this.passwordData.new_password !== this.passwordData.confirm_password;
        },

        get canSubmitPassword() {
            return this.passwordData.current_password.length > 0 &&
                   this.passwordData.new_password.length >= 8 &&
                   this.passwordData.confirm_password.length >= 8 &&
                   !this.passwordMismatch;
        },

        async updateProfile() {
            this.isSubmitting = true;
            this.errors = {};

            try {
                const response = await fetch('{{ route("profile.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    credentials: 'same-origin',
                        body: JSON.stringify({
                            full_name: this.profile.full_name,
                            school_name: this.profile.school_name,
                            contact_number: this.profile.contact_number,
                        }),
                });

                const contentType = response.headers.get('content-type') || '';

                if (!response.ok) {
                    if (contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.errors) {
                            this.errors = data.errors;
                        }
                    } else {
                        const text = await response.text();
                        this.errors.general = text || 'An error occurred while updating profile.';
                    }
                    this.isSubmitting = false;
                    return;
                }

                // On success, parse JSON if available and update local student state
                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    console.log('Profile update response:', data);

                    // backend may return 'user' or 'student'
                    const respStudent = (data.student || data.user || data);
                    if (respStudent) {
                        this.student.full_name = respStudent.full_name || this.student.full_name;
                        this.student.school_name = respStudent.school_name || this.student.school_name;
                        this.student.contact_number = respStudent.contact_number || this.student.contact_number;
                        console.log('Updated Alpine student state:', this.student);

                        // Notify other UI components (profile modal, header) about the updated student
                        try {
                            console.log('Dispatching student.updated', respStudent);
                            window.dispatchEvent(new CustomEvent('student.updated', { detail: respStudent }));
                        } catch (e) { console.warn('Could not dispatch student.updated event', e); }
                    }

                    this.profileSubmitted = true;
                    try {
                        const userId = respStudent?.student_id || '{{ $user->student_id }}';
                        localStorage.setItem('student_onboarding_done_' + userId, '1');
                    } catch (e) {}
                } else {
                    this.profileSubmitted = true;
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                this.errors.general = 'An error occurred. Please try again.';
            } finally {
                this.isSubmitting = false;
            }
        },

        async updatePassword() {
            this.isSubmitting = true;
            this.errors = {};

            try {
                const response = await fetch('{{ route("password.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        current_password: this.passwordData.current_password,
                        password: this.passwordData.new_password,
                        password_confirmation: this.passwordData.confirm_password,
                    }),
                });

                const contentType = response.headers.get('content-type') || '';

                if (!response.ok) {
                    if (contentType.includes('application/json')) {
                        const data = await response.json();
                        if (data.errors) {
                            this.errors = data.errors;
                        }
                    } else {
                        const text = await response.text();
                        this.errors.general = text || 'An error occurred while updating password.';
                    }
                    this.isSubmitting = false;
                    return;
                }

                // Parse JSON if present
                let data = {};
                if (contentType.includes('application/json')) {
                    data = await response.json();
                }

                // Mark password done and close modal
                this.passwordDone = true;

                // Persist onboarding completion (prefer backend flag, otherwise localStorage fallback)
                try {
                    const user = data.user || {};
                    if (user.password_changed_at || user.profile_completed_at) {
                        localStorage.setItem('student_onboarding_done_' + (user.student_id || '{{ $user->student_id }}'), '1');
                    } else {
                        localStorage.setItem('student_onboarding_done_{{ $user->student_id }}', '1');
                    }
                } catch (e) {}

                // Close modal without redirecting or reloading
                this.showModal = false;
            } catch (error) {
                console.error('Error updating password:', error);
                this.errors.general = 'An error occurred. Please try again.';
            } finally {
                this.isSubmitting = false;
            }
        },

        closeModal() {
            // If onboarding is mandatory and not yet completed, prevent closing
            const mandatory = @json($needsOnboarding);
            if (mandatory && (!this.profileSubmitted || !this.passwordDone)) {
                // Inform the user; do not logout or navigate away
                alert('Please complete the onboarding steps to continue.');
                return;
            }

            // Otherwise, just hide the modal
            this.showModal = false;
        },

        logout() {
            // Create hidden form and submit logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    };
}
</script>
