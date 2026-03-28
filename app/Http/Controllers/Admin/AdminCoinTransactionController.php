<?php

namespace App\Http\Controllers\Admin;

use App\Models\CoinTransaction;
use App\Models\PastPaperSubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminCoinTransactionController extends Controller
{
    /**
     * Show coin transactions audit log
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $fromDate = $request->query('from_date') ? \Carbon\Carbon::parse($request->query('from_date')) : null;
        $toDate = $request->query('to_date') ? \Carbon\Carbon::parse($request->query('to_date'))->endOfDay() : null;
        $studentSearch = $request->query('student');
        $subjectId = $request->query('subject_id');
        $mode = $request->query('mode');
        $coinsOnly = $request->query('coins_only') === '1';

        // Build query
        $query = CoinTransaction::query()
            ->with([
                'user:id,name,full_name,email',
                'subject:id,name,stream',
                'paper:id,title,category,year',
                'attempt:id,percentage,score,total_questions',
            ])
            ->orderByDesc('created_at');

        // Date range filter
        if ($fromDate) {
            $query->whereDate('earned_on', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('earned_on', '<=', $toDate);
        }

        // Student search filter
        if ($studentSearch) {
            $query->whereHas('user', function ($q) use ($studentSearch) {
                $q->where('name', 'like', "%{$studentSearch}%")
                  ->orWhere('full_name', 'like', "%{$studentSearch}%")
                  ->orWhere('email', 'like', "%{$studentSearch}%");
            });
        }

        // Subject filter
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Mode filter
        if ($mode) {
            $query->where('mode', $mode);
        }

        // Coins > 0 toggle
        if ($coinsOnly) {
            $query->where('coins', '>', 0);
        }

        // Get paginated results
        $transactions = $query->paginate(25)->appends($request->all());

        // Get all subjects for filter dropdown
        $subjects = PastPaperSubject::query()
            ->select('id', 'name', 'stream')
            ->orderBy('stream')
            ->orderBy('name')
            ->get();

        // Get today's summary
        $today = now()->toDateString();
        $todayTransactions = CoinTransaction::whereDate('earned_on', $today)
            ->with('user:id,name,full_name');

        $todayTotalCoins = (clone $todayTransactions)->sum('coins');
        $todayTransactionCount = (clone $todayTransactions)->count();

        // Top 5 students today
        $topStudentsToday = (clone $todayTransactions)
            ->selectRaw('user_id, SUM(coins) as total_coins')
            ->groupBy('user_id')
            ->orderByDesc('total_coins')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->user = $item->user()->first(['id', 'name', 'full_name']);
                return $item;
            });

        return view('admin.coins.transactions.index', compact(
            'transactions',
            'subjects',
            'todayTotalCoins',
            'todayTransactionCount',
            'topStudentsToday',
            'fromDate',
            'toDate',
            'studentSearch',
            'subjectId',
            'mode',
            'coinsOnly'
        ));
    }
}
