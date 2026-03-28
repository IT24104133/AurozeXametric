@extends('layouts.dashboard')

@section('title', 'Coin Transactions Audit')

@section('sidebar-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('mobile-nav')
    @include('admin.partials.sidebar-nav')
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">💰 Coin Transactions Audit</h1>
                <p class="mt-2 text-slate-600">Monitor all coin awards and student rewards</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-2xl text-slate-700 font-bold hover:bg-slate-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Today's Total Coins --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Today's Total Coins</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $todayTotalCoins }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ now()->toDateString() }}</p>
        </div>

        {{-- Today's Transactions --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Today's Transactions</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $todayTransactionCount }}</p>
            <p class="mt-1 text-xs text-gray-500">Coin awards created</p>
        </div>

        {{-- Daily Cap Status --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Max Daily Cap</p>
            <p class="mt-2 text-3xl font-bold text-green-600">50</p>
            <p class="mt-1 text-xs text-gray-500">coins per student per day</p>
        </div>
    </div>

    {{-- Top 5 Students Today --}}
    @if($topStudentsToday->count() > 0)
        <div class="mb-8 rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">🏆 Top 5 Students Today</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($topStudentsToday as $idx => $entry)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 font-bold text-gray-700">
                                #{{ $idx + 1 }}
                            </span>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $entry->user->full_name ?? $entry->user->name }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $entry->user->email ?? '-' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold">
                            {{ $entry->total_coins }} 🪙
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Filters</h2>
        <form method="GET" action="{{ route('admin.coins.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            
            {{-- Date From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate?->toDateString() }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate?->toDateString() }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Student Search --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                <input type="text" name="student" placeholder="Name/Email" value="{{ $studentSearch }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <select name="subject_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->stream }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Mode --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                <select name="mode" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Modes</option>
                    <option value="normal" {{ $mode === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="ultra_easy" {{ $mode === 'ultra_easy' ? 'selected' : '' }}>Ultra Easy</option>
                    <option value="ultra_medium" {{ $mode === 'ultra_medium' ? 'selected' : '' }}>Ultra Medium</option>
                    <option value="ultra_hard" {{ $mode === 'ultra_hard' ? 'selected' : '' }}>Ultra Hard</option>
                </select>
            </div>

            {{-- Coins Only Toggle --}}
            <div class="flex items-end">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="coins_only" value="1" {{ $coinsOnly ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Coins > 0</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-end col-span-full md:col-span-1">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
            </div>

            {{-- Reset --}}
            <div class="flex items-end col-span-full md:col-span-1">
                <a href="{{ route('admin.coins.transactions.index') }}" class="w-full px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition text-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- Transactions Table --}}
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-900">Transactions</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Paper</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Mode</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Score %</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-yellow-700 uppercase">Coins</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->earned_on->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $transaction->user->full_name ?? $transaction->user->name }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $transaction->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $transaction->subject?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $transaction->paper?->title ?? '-' }}
                                @if($transaction->paper?->category === 'free_style')
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">FS</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if($transaction->mode === 'normal')
                                        bg-blue-100 text-blue-800
                                    @elseif($transaction->mode === 'ultra_easy')
                                        bg-green-100 text-green-800
                                    @elseif($transaction->mode === 'ultra_medium')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($transaction->mode === 'ultra_hard')
                                        bg-red-100 text-red-800
                                    @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->mode ?? 'N/A')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ $transaction->attempt?->percentage ?? '-' }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold text-sm">
                                    {{ $transaction->coins }} 🪙
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $transaction->reason ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
