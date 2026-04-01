<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ExamPortal – Secure Online Exams</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:300,400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeInUp 0.9s ease-out forwards; }
        .delay-150 { animation-delay: .15s; }
        .delay-300 { animation-delay: .3s; }
        .delay-450 { animation-delay: .45s; }

        /* soft blob background */
        .hero-blob {
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,.18), rgba(255,255,255,0) 55%),
                        radial-gradient(circle at 70% 60%, rgba(255,255,255,.10), rgba(255,255,255,0) 55%);
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased">

@if($isPreview ?? false)
    <div class="bg-amber-500 text-white text-sm font-semibold text-center py-2">
        Preview Mode — changes are visible only to admins until published.
    </div>
@endif

<!-- Header -->
<header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-slate-200/70">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand -->
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-sm">
                <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
            </div>
            <div class="font-semibold text-lg text-teal-700">ExamPortal</div>
        </div>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
            <a href="#features" class="hover:text-slate-900 transition">Features</a>
            <a href="#how" class="hover:text-slate-900 transition">How It Works</a>
        </nav>

        <!-- Auth buttons -->
        <div class="flex items-center gap-2 sm:gap-3">
    @auth
        <a href="{{ route('dashboard') }}"
           class="px-4 sm:px-5 py-2 rounded-full bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
            Dashboard
        </a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" 
                    class="px-4 sm:px-5 py-2 rounded-full border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                Logout
            </button>
        </form>
    @else
        @if (Route::has('login'))
            <a href="{{ route('login') }}"
               class="px-4 sm:px-5 py-2.5 rounded-xl border-2 border-teal-600 text-teal-600 text-sm font-bold hover:bg-teal-50 transition">
                Sign In
            </a>
        @endif

        @if (Route::has('register'))
  <a href="{{ route('register') }}"
     class="px-4 sm:px-5 py-2.5 rounded-xl bg-teal-600 text-white text-sm font-bold hover:bg-teal-500 transition shadow-sm">
    Register
  </a>
@endif
    @endauth
</div>

    </div>
</header>

<!-- Hero -->
<section class="relative overflow-hidden">
    <!-- Gradient background like screenshot -->
    <div class="absolute inset-0 bg-gradient-to-br from-teal-600 via-teal-600 to-cyan-500"></div>
    <div class="absolute inset-0 hero-blob opacity-100"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
        <!-- Left -->
        <div class="text-white">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 border border-white/25 text-sm font-semibold">
                <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                Secure • Timed • Fair
            </div>

            <h1 class="mt-7 text-5xl lg:text-6xl font-extrabold leading-[1.05] tracking-tight animate-fade-up">
                {{ $homepageSettings['hero_title'] ?? 'Practice smarter with ExamPortal' }}
            </h1>

            @if(!empty($homepageSettings['hero_subtitle']))
                <p class="mt-6 text-lg text-white/90 max-w-xl animate-fade-up delay-150">
                    {{ $homepageSettings['hero_subtitle'] }}
                </p>
            @endif

            <div class="mt-10 flex flex-col sm:flex-row gap-4 animate-fade-up delay-300">
                <a href="{{ $homepageSettings['hero_button_link'] ?? route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-white text-teal-700 font-bold shadow-lg hover:bg-white/95 transition">
                    {{ $homepageSettings['hero_button_text'] ?? 'Start Practicing' }}
                    <span aria-hidden="true">→</span>
                </a>
                <a href="#how"
                   class="inline-flex items-center justify-center px-8 py-4 rounded-2xl border border-white/40 text-white font-semibold hover:bg-white/10 transition">
                    How It Works
                </a>
            </div>
        </div>

        <!-- Right mock UI -->
        <div class="relative animate-fade-up delay-450">
            @if(!empty($homepageSettings['hero_image_path']))
                <div class="relative rounded-[28px] border border-white/35 bg-white/10 p-4 shadow-2xl">
                    <img src="{{ asset('storage/' . $homepageSettings['hero_image_path']) }}" alt="Hero" class="rounded-2xl w-full h-auto object-cover">
                </div>
            @else
            <!-- Outer frame -->
            <div class="relative rounded-[28px] border border-white/35 bg-white/10 p-6 shadow-2xl">
                <!-- Main app card -->
                <div class="rounded-2xl bg-white shadow-xl overflow-hidden border border-slate-100">
                    <!-- top bar -->
                    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                            <span class="h-3 w-3 rounded-full bg-amber-300"></span>
                            <span class="h-3 w-3 rounded-full bg-emerald-300"></span>
                        </div>
                        <div class="text-xs font-semibold text-teal-700 bg-teal-50 px-3 py-1 rounded-full">
                            ⏱ 45:00 remaining
                        </div>
                    </div>

                    <!-- content -->
                    <div class="p-5 space-y-4">
                        <div class="h-3 w-3/4 rounded bg-slate-100"></div>
                        <div class="h-3 w-full rounded bg-slate-100"></div>
                        <div class="h-3 w-5/6 rounded bg-slate-100"></div>

                        <!-- options -->
                        <div class="mt-2 rounded-xl border border-slate-200 p-4 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-4 w-4 rounded-full border border-slate-300"></div>
                                <div class="h-3 w-4/5 rounded bg-slate-100"></div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="h-4 w-4 rounded-full border-4 border-teal-600"></div>
                                <div class="h-3 w-4/5 rounded bg-slate-100"></div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="h-4 w-4 rounded-full border border-slate-300"></div>
                                <div class="h-3 w-4/5 rounded bg-slate-100"></div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="h-4 w-4 rounded-full border border-slate-300"></div>
                                <div class="h-3 w-4/5 rounded bg-slate-100"></div>
                            </div>
                        </div>

                        <!-- bottom buttons -->
                        <div class="flex items-center justify-between pt-2">
                            <button class="px-4 py-2 text-sm font-semibold rounded-xl border border-slate-200 text-slate-600">
                                Previous
                            </button>
                            <button class="px-4 py-2 text-sm font-semibold rounded-xl bg-teal-600 text-white">
                                Next Question
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Floating pill: Secure -->
                <div class="absolute -left-6 top-24 bg-white rounded-xl shadow-lg px-4 py-2 border border-slate-100 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-teal-50 text-teal-700">🛡️</span>
                    <span class="text-sm font-semibold text-slate-700">Secure</span>
                </div>

                <!-- Floating pill: Auto-save -->
                <div class="absolute -right-6 bottom-16 bg-white rounded-xl shadow-lg px-4 py-2 border border-slate-100 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-teal-50 text-teal-700">🕒</span>
                    <span class="text-sm font-semibold text-slate-700">Auto-save</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- SECTION: MCQ IMPORTANCE (Split Hero Style) -->
<section class="relative py-16 lg:py-24 overflow-hidden">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 1px 1px, rgb(148 163 184 / 0.15) 1px, transparent 0); background-size: 40px 40px;"></div>
    
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Split Layout -->
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center mb-16">
            <!-- Left: Content -->
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 backdrop-blur border border-teal-200 text-sm font-semibold text-teal-700 mb-6 shadow-sm">
                    <span class="text-lg">📊</span>
                    Sri Lankan Exam Success
                </div>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">
                    Why MCQs Matter for Your Results
                </h2>
                <p class="mt-6 text-lg text-slate-600 leading-relaxed">
                    Multiple Choice Questions form the backbone of Sri Lankan examinations from Grade 5 to A/L. Master MCQs to boost your scores, improve speed, and gain the competitive edge you need.
                </p>
                <div class="mt-8">
                    <a href="{{ Route::has('login') ? route('login') : '#' }}"
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-teal-600 text-white text-lg font-bold shadow-xl hover:bg-teal-500 hover:shadow-2xl transition-all">
                        Start MCQ Practice
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>

            <!-- Right: Stats Panel -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Stat 1 -->
                <div class="bg-white/80 backdrop-blur rounded-2xl border border-slate-200 shadow-lg p-6 hover:scale-105 transition-transform">
                    <div class="text-3xl mb-2">🎓</div>
                    <div class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-1">Grade 5</div>
                    <div class="text-xl font-bold text-slate-900">Mostly MCQ</div>
                </div>
                
                <!-- Stat 2 -->
                <div class="bg-white/80 backdrop-blur rounded-2xl border border-slate-200 shadow-lg p-6 hover:scale-105 transition-transform">
                    <div class="text-3xl mb-2">📝</div>
                    <div class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-1">O/L Paper I</div>
                    <div class="text-xl font-bold text-slate-900">20-30%</div>
                </div>
                
                <!-- Stat 3 -->
                <div class="bg-white/80 backdrop-blur rounded-2xl border border-slate-200 shadow-lg p-6 hover:scale-105 transition-transform">
                    <div class="text-3xl mb-2">⚡</div>
                    <div class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-1">A/L CGT</div>
                    <div class="text-xl font-bold text-slate-900">50 MCQs</div>
                </div>
                
                <!-- Stat 4 -->
                <div class="bg-white/80 backdrop-blur rounded-2xl border border-slate-200 shadow-lg p-6 hover:scale-105 transition-transform">
                    <div class="text-3xl mb-2">🎯</div>
                    <div class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-1">Training</div>
                    <div class="text-xl font-bold text-slate-900">Speed++</div>
                </div>
            </div>
        </div>

        <!-- Compact Mini Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/60 backdrop-blur rounded-xl border border-slate-200 p-4 hover:bg-white transition">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">🎓</div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Grade 5 Scholarship</h4>
                        <p class="text-xs text-slate-600 mt-1">MCQ-based selection exam</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur rounded-xl border border-slate-200 p-4 hover:bg-white transition">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">📝</div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">O/L Contribution</h4>
                        <p class="text-xs text-slate-600 mt-1">Paper I adds to final grade</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur rounded-xl border border-slate-200 p-4 hover:bg-white transition">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">⚡</div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Z-Score Boost</h4>
                        <p class="text-xs text-slate-600 mt-1">CGT improves university eligibility</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur rounded-xl border border-slate-200 p-4 hover:bg-white transition">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">🎯</div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Exam Confidence</h4>
                        <p class="text-xs text-slate-600 mt-1">Practice reduces anxiety</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($homepageSettings['show_features'] ?? true)
<!-- SECTION: STUDENTS vs ADMINS (2-Column Comparison) -->
<section id="features" class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900">Why Everyone Loves ExamPortal</h2>
            <p class="mt-4 text-xl text-slate-600 max-w-2xl mx-auto">
                Built for students, loved by teachers and admins. One platform, multiple benefits.
            </p>
        </div>

        <!-- Two-Column Comparison -->
        <div class="grid lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <!-- Students Column -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-3xl border-2 border-teal-200 p-8 shadow-lg">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-12 w-12 rounded-xl bg-teal-600 text-white flex items-center justify-center text-2xl">👨‍🎓</div>
                    <h3 class="text-2xl font-bold text-slate-900">For Students</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Practice Anytime</h4>
                            <p class="text-sm text-slate-600 mt-1">Access past papers and MCQs 24/7 from any device</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Instant Feedback</h4>
                            <p class="text-sm text-slate-600 mt-1">Get detailed results immediately after submission</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Track Progress</h4>
                            <p class="text-sm text-slate-600 mt-1">Monitor improvement with history and analytics</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Earn Rewards</h4>
                            <p class="text-sm text-slate-600 mt-1">Collect coins and climb the leaderboard</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admins Column -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl border-2 border-purple-200 p-8 shadow-lg">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-12 w-12 rounded-xl bg-purple-600 text-white flex items-center justify-center text-2xl">👨‍💼</div>
                    <h3 class="text-2xl font-bold text-slate-900">For Admins & Teachers</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Easy Management</h4>
                            <p class="text-sm text-slate-600 mt-1">Create exams and questions in minutes, not hours</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Secure & Fair</h4>
                            <p class="text-sm text-slate-600 mt-1">Timed exams with auto-submit prevent cheating</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Control Results</h4>
                            <p class="text-sm text-slate-600 mt-1">Publish scores only when you're ready</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="mt-1 h-6 w-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-slate-900">Bulk Operations</h4>
                            <p class="text-sm text-slate-600 mt-1">Upload hundreds of students at once with CSV</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </section>
@endif

<!-- SECTION: HOW IT WORKS TIMELINE -->
<section id="how" class="py-16 lg:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900">How It Works</h2>
            <p class="mt-4 text-xl text-slate-600">Simple. Clear. Stress-free.</p>
        </div>

        <!-- Desktop: Horizontal Timeline -->
        <div class="hidden lg:block">
            <div class="relative">
                <!-- Connecting Line -->
                <div class="absolute top-16 left-1/4 right-1/4 h-1 bg-gradient-to-r from-teal-200 via-teal-400 to-teal-600"></div>
                
                <div class="grid grid-cols-3 gap-8 relative">
                    <!-- Step 1 -->
                    <div class="text-center group">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 text-8xl font-black text-teal-100 opacity-50 -top-4 text-center">01</div>
                            <div class="relative h-32 w-32 mx-auto rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition-transform">
                                ➡️
                            </div>
                        </div>
                        <h3 class="mt-6 text-2xl font-bold text-slate-900">Sign In</h3>
                        <p class="mt-3 text-slate-600 px-4">Use your email or student ID for quick, secure authentication</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center group">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 text-8xl font-black text-teal-100 opacity-50 -top-4 text-center">02</div>
                            <div class="relative h-32 w-32 mx-auto rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition-transform">
                                📄
                            </div>
                        </div>
                        <h3 class="mt-6 text-2xl font-bold text-slate-900">Take Exam</h3>
                        <p class="mt-3 text-slate-600 px-4">Timer starts, answers save live, navigate freely between questions</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center group">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 text-8xl font-black text-teal-100 opacity-50 -top-4 text-center">03</div>
                            <div class="relative h-32 w-32 mx-auto rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center text-4xl shadow-xl group-hover:scale-110 transition-transform">
                                🏆
                            </div>
                        </div>
                        <h3 class="mt-6 text-2xl font-bold text-slate-900">Get Results</h3>
                        <p class="mt-3 text-slate-600 px-4">Submit or auto-submit, then view scores when admin publishes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile: Vertical Timeline -->
        <div class="lg:hidden space-y-8">
            <!-- Step 1 -->
            <div class="relative pl-20">
                <div class="absolute left-0 top-0">
                    <div class="relative">
                        <div class="absolute inset-0 text-6xl font-black text-teal-100 -top-2 -left-2">01</div>
                        <div class="relative h-16 w-16 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center text-2xl shadow-lg">
                            ➡️
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Sign In</h3>
                <p class="mt-2 text-sm text-slate-600">Use your email or student ID for quick, secure authentication</p>
            </div>

            <!-- Connecting Line -->
            <div class="ml-8 w-0.5 h-8 bg-gradient-to-b from-teal-400 to-teal-600"></div>

            <!-- Step 2 -->
            <div class="relative pl-20">
                <div class="absolute left-0 top-0">
                    <div class="relative">
                        <div class="absolute inset-0 text-6xl font-black text-teal-100 -top-2 -left-2">02</div>
                        <div class="relative h-16 w-16 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center text-2xl shadow-lg">
                            📄
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Take Exam</h3>
                <p class="mt-2 text-sm text-slate-600">Timer starts, answers save live, navigate freely between questions</p>
            </div>

            <!-- Connecting Line -->
            <div class="ml-8 w-0.5 h-8 bg-gradient-to-b from-teal-400 to-teal-600"></div>

            <!-- Step 3 -->
            <div class="relative pl-20">
                <div class="absolute left-0 top-0">
                    <div class="relative">
                        <div class="absolute inset-0 text-6xl font-black text-teal-100 -top-2 -left-2">03</div>
                        <div class="relative h-16 w-16 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 text-white flex items-center justify-center text-2xl shadow-lg">
                            🏆
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Get Results</h3>
                <p class="mt-2 text-sm text-slate-600">Submit or auto-submit, then view scores when admin publishes</p>
            </div>
        </div>

        <!-- CTA -->
        <div class="mt-16 flex justify-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center gap-2 px-10 py-4 rounded-2xl bg-teal-600 text-white font-extrabold shadow-md hover:bg-teal-500 transition">
                Start Your First Exam
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>

<!-- Public Leaderboard -->
@if($homepageSettings['show_leaderboard'] ?? true)
<section class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24">
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-sm font-semibold text-amber-700 mb-4">
            <span class="text-lg">🏆</span>
            Top Performers
        </div>
        <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 mt-4">Leaderboard</h2>
        <p class="mt-4 text-xl text-slate-600 max-w-2xl mx-auto">
            Celebrate your achievements. See how you rank among all students.
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-4 sm:px-6 py-4 text-left text-xs font-extrabold text-slate-700 uppercase tracking-wider">Rank</th>
                        <th class="px-4 sm:px-6 py-4 text-left text-xs font-extrabold text-slate-700 uppercase tracking-wider">Student</th>
                        <th class="px-4 sm:px-6 py-4 text-right text-xs font-extrabold text-slate-700 uppercase tracking-wider">Total Coins</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topLeaderboard as $index => $entry)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @if($index === 0)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 font-extrabold text-sm">🥇</span>
                                @elseif($index === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-700 font-extrabold text-sm">🥈</span>
                                @elseif($index === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-extrabold text-sm">🥉</span>
                                @else
                                    <span class="text-sm font-extrabold text-slate-600">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $entry['full_name'] ?? $entry['name'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-extrabold text-yellow-600">{{ number_format($entry['total_coins']) }} 🪙</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">
                                No students yet. Be the first to join and earn coins!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-sm text-slate-600">
            Rankings update daily based on coins earned. <a href="{{ route('login') }}" class="text-teal-600 font-semibold hover:text-teal-700">Sign in</a> to start earning coins!
        </p>
    </div>
</section>
@endif

@if($homepageSettings['show_testimonials'] ?? false)
<!-- Testimonials -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-50 border border-rose-200 text-sm font-semibold text-rose-700 mb-4">
                <span class="text-lg">💬</span>
                Testimonials
            </div>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">What Students Say</h2>
            <p class="mt-3 text-slate-600">Real feedback from learners who improved their results.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <p class="text-slate-600">“ExamPortal made revision easy. I practiced daily and scored higher than ever.”</p>
                <div class="mt-4 text-sm font-bold text-slate-900">— Amina K.</div>
            </div>
            <div class="rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <p class="text-slate-600">“Love the past papers and instant feedback. It feels like a real exam.”</p>
                <div class="mt-4 text-sm font-bold text-slate-900">— David R.</div>
            </div>
            <div class="rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <p class="text-slate-600">“The leaderboard keeps me motivated. I practice more consistently now.”</p>
                <div class="mt-4 text-sm font-bold text-slate-900">— Grace T.</div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Footer -->
<footer class="border-t border-slate-200 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center">
                <img src="{{ asset('logo.png') }}" alt="ExamPortal" class="h-6 w-6 object-contain">
            </div>
            <span class="font-semibold text-slate-700">ExamPortal</span>
        </div>

        <div class="text-sm text-slate-500">
            © {{ date('Y') }} ExamPortal. Built with care for fair assessments.
        </div>

        <div class="flex items-center gap-6 text-sm font-medium text-slate-500">
            <a href="#" class="hover:text-slate-900 transition">Privacy</a>
            <a href="#" class="hover:text-slate-900 transition">Terms</a>
            <a href="#" class="hover:text-slate-900 transition">Support</a>
        </div>
    </div>
</footer>

</body>
</html>
