@extends('layouts.dashboard')

@section('title', 'Homepage Settings')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @extends('layouts.dashboard')

        @section('title', 'Homepage Settings')

        @section('sidebar-nav')
            @include('admin.partials.sidebar-nav')
        @endsection

        @section('content')
        <div class="min-h-screen bg-slate-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-800">Homepage Settings</h1>
                        <p class="text-slate-600 mt-1">Edit content, preview changes, then publish when ready.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($hasDraft)
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                                Draft Pending
                            </span>
                        @endif
                        <a href="{{ route('admin.homepage.preview') }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                            Preview Draft
                        </a>
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                            Back to Dashboard
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-emerald-700 font-semibold">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 rounded-2xl p-4">
                        <ul class="text-rose-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.homepage.settings.draft') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Hero Section -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl flex items-center justify-center border border-teal-200">
                                <span class="text-xl">✨</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Hero Section</h2>
                                <p class="text-sm text-slate-600">Control the headline, CTA, and hero image</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Hero Title</label>
                                <input type="text" name="hero_title" value="{{ old('hero_title', $formData['hero_title']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Hero Button Text</label>
                                <input type="text" name="hero_button_text" value="{{ old('hero_button_text', $formData['hero_button_text']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Hero Subtitle</label>
                                <textarea name="hero_subtitle" rows="3"
                                          class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">{{ old('hero_subtitle', $formData['hero_subtitle']) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Hero Button Link</label>
                                <input type="text" name="hero_button_link" value="{{ old('hero_button_link', $formData['hero_button_link']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Hero Image</label>
                                <input type="file" name="hero_image" accept="image/*"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                                @if(!empty($formData['hero_image_path']))
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $formData['hero_image_path']) }}" alt="Hero" class="h-16 w-24 object-cover rounded-xl border border-slate-200">
                                        <span class="text-xs text-slate-500">Current image</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Growth & Stats Section -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl flex items-center justify-center border border-indigo-200">
                                <span class="text-xl">📈</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Growth & Stats Section</h2>
                                <p class="text-sm text-slate-600">Combine growth chart and platform stats into one section</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl">
                                <div>
                                    <p class="font-semibold text-slate-800">Enable Growth & Stats Section</p>
                                    <p class="text-xs text-slate-500">Master toggle for the combined section</p>
                                </div>
                                <div>
                                    <input type="hidden" name="show_growth_stats_section" value="0">
                                    <input type="checkbox" name="show_growth_stats_section" value="1" class="w-6 h-6 text-teal-600 border-slate-300 rounded"
                                           {{ old('show_growth_stats_section', $formData['show_growth_stats_section']) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl">
                                <div>
                                    <p class="font-semibold text-slate-800">Show Growth Chart</p>
                                    <p class="text-xs text-slate-500">Display daily registrations chart</p>
                                </div>
                                <div>
                                    <input type="hidden" name="show_growth_chart" value="0">
                                    <input type="checkbox" name="show_growth_chart" value="1" class="w-6 h-6 text-teal-600 border-slate-300 rounded"
                                           {{ old('show_growth_chart', $formData['show_growth_chart']) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl">
                                <div>
                                    <p class="font-semibold text-slate-800">Show Stats Cards</p>
                                    <p class="text-xs text-slate-500">Display platform statistics cards</p>
                                </div>
                                <div>
                                    <input type="hidden" name="show_platform_stats" value="0">
                                    <input type="checkbox" name="show_platform_stats" value="1" class="w-6 h-6 text-teal-600 border-slate-300 rounded"
                                           {{ old('show_platform_stats', $formData['show_platform_stats']) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Section Title</label>
                                <input type="text" name="growth_section_title" value="{{ old('growth_section_title', $formData['growth_section_title']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Section Subtitle</label>
                                <input type="text" name="growth_section_subtitle" value="{{ old('growth_section_subtitle', $formData['growth_section_subtitle']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Stats Title</label>
                                <input type="text" name="stats_section_title" value="{{ old('stats_section_title', $formData['stats_section_title']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Stats Subtitle</label>
                                <input type="text" name="stats_section_subtitle" value="{{ old('stats_section_subtitle', $formData['stats_section_subtitle']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Stats Card Builder -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl flex items-center justify-center border border-amber-200">
                                <span class="text-xl">🧱</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Stats Card Builder</h2>
                                <p class="text-sm text-slate-600">Enable, rename, style and reorder the stats cards</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($formData['stats_cards'] as $index => $card)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $card['key'])) }}</div>
                                        <div>
                                            <input type="hidden" name="stats_cards[{{ $index }}][enabled]" value="0">
                                            <input type="checkbox" name="stats_cards[{{ $index }}][enabled]" value="1"
                                                   class="w-5 h-5 text-teal-600 border-slate-300 rounded"
                                                   {{ old("stats_cards.$index.enabled", $card['enabled']) ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    <input type="hidden" name="stats_cards[{{ $index }}][key]" value="{{ $card['key'] }}">

                                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mt-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Label</label>
                                            <input type="text" name="stats_cards[{{ $index }}][label]"
                                                   value="{{ old("stats_cards.$index.label", $card['label']) }}"
                                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm">
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                                            <input type="text" name="stats_cards[{{ $index }}][description]"
                                                   value="{{ old("stats_cards.$index.description", $card['description']) }}"
                                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Order</label>
                                            <input type="number" min="1" max="20" name="stats_cards[{{ $index }}][order]"
                                                   value="{{ old("stats_cards.$index.order", $card['order']) }}"
                                                   class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm">
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Icon</label>
                                        <select name="stats_cards[{{ $index }}][icon]" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm">
                                            @foreach(['graduation-cap','user','file-text','book-open','infinity','chart-bar','star'] as $icon)
                                                <option value="{{ $icon }}" {{ old("stats_cards.$index.icon", $card['icon']) === $icon ? 'selected' : '' }}>
                                                    {{ $icon }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Growth Date Range -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-50 to-sky-50 rounded-2xl flex items-center justify-center border border-teal-200">
                                <span class="text-xl">📅</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Growth Date Range</h2>
                                <p class="text-sm text-slate-600">Set dates for daily growth metrics</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Start Date</label>
                                <input type="date" name="growth_start_date" value="{{ old('growth_start_date', $formData['growth_start_date']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">End Date</label>
                                <input type="date" name="growth_end_date" value="{{ old('growth_end_date', $formData['growth_end_date']) }}"
                                       class="w-full px-4 py-2.5 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Other Sections -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl flex items-center justify-center border border-purple-200">
                                <span class="text-xl">🧩</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Other Sections</h2>
                                <p class="text-sm text-slate-600">Control additional homepage sections</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl">
                                <div>
                                    <p class="font-semibold text-slate-800">Leaderboard</p>
                                    <p class="text-xs text-slate-500">Top 10 coin earners</p>
                                </div>
                                <div>
                                    <input type="hidden" name="show_leaderboard" value="0">
                                    <input type="checkbox" name="show_leaderboard" value="1" class="w-6 h-6 text-teal-600 border-slate-300 rounded"
                                           {{ old('show_leaderboard', $formData['show_leaderboard']) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl">
                                <div>
                                    <p class="font-semibold text-slate-800">Features</p>
                                    <p class="text-xs text-slate-500">Why students love ExamPortal</p>
                                </div>
                                <div>
                                    <input type="hidden" name="show_features" value="0">
                                    <input type="checkbox" name="show_features" value="1" class="w-6 h-6 text-teal-600 border-slate-300 rounded"
                                           {{ old('show_features', $formData['show_features']) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl">
                                <div>
                                    <p class="font-semibold text-slate-800">Testimonials</p>
                                    <p class="text-xs text-slate-500">Student feedback section</p>
                                </div>
                                <div>
                                    <input type="hidden" name="show_testimonials" value="0">
                                    <input type="checkbox" name="show_testimonials" value="1" class="w-6 h-6 text-teal-600 border-slate-300 rounded"
                                           {{ old('show_testimonials', $formData['show_testimonials']) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 text-white font-extrabold shadow hover:bg-slate-800 transition">
                            Save Draft
                        </button>
                        <button type="submit" formaction="{{ route('admin.homepage.settings.publish') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-teal-600 text-white font-extrabold shadow hover:bg-teal-500 transition">
                            Publish
                        </button>
                        <a href="{{ route('admin.homepage.preview') }}" target="_blank"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-slate-300 text-slate-700 font-extrabold hover:bg-slate-50 transition">
                            Preview Draft
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @endsection
                    <!-- Total Coins Summary -->
                    <label class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl hover:bg-slate-50 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-slate-800">Total Coins Summary</p>
                                <p class="text-xs text-slate-500">Show overall coin statistics</p>
                            </div>
                        </div>
                        <input 
                            type="checkbox" 
                            name="show_total_coins" 
                            {{ $homepageSettings['show_total_coins'] ?? true ? 'checked' : '' }}
                            class="w-5 h-5 text-teal-600 border-slate-300 rounded focus:ring-teal-500"
                        >
                    </label>

                    <!-- Growth Widget -->
                    <label class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl hover:bg-slate-50 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-slate-800">Growth Widget</p>
                                <p class="text-xs text-slate-500">Display user registration trends</p>
                            </div>
                        </div>
                        <input 
                            type="checkbox" 
                            name="show_growth_widget" 
                            {{ $homepageSettings['show_growth_widget'] ?? false ? 'checked' : '' }}
                            class="w-5 h-5 text-teal-600 border-slate-300 rounded focus:ring-teal-500"
                        >
                    </label>

                    <!-- Past Paper Stats -->
                    <label class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl hover:bg-slate-50 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-slate-800">Past Paper Statistics</p>
                                <p class="text-xs text-slate-500">Show past paper metrics and performance</p>
                            </div>
                        </div>
                        <input 
                            type="checkbox" 
                            name="show_past_paper_stats" 
                            {{ $homepageSettings['show_past_paper_stats'] ?? true ? 'checked' : '' }}
                            class="w-5 h-5 text-teal-600 border-slate-300 rounded focus:ring-teal-500"
                        >
                    </label>

                    <!-- Exam Stats -->
                    <label class="flex items-center justify-between p-4 border border-slate-200 rounded-2xl hover:bg-slate-50 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-slate-800">Exam Statistics</p>
                                <p class="text-xs text-slate-500">Display exam metrics and attempts</p>
                            </div>
                        </div>
                        <input 
                            type="checkbox" 
                            name="show_exam_stats" 
                            {{ $homepageSettings['show_exam_stats'] ?? true ? 'checked' : '' }}
                            class="w-5 h-5 text-teal-600 border-slate-300 rounded focus:ring-teal-500"
                        >
                    </label>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-6 py-3 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-2xl font-bold shadow-sm hover:shadow-md transition">
                    Save Settings
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
