<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class AdminHealthController extends Controller
{
    public function index()
    {
        $checks = [
            'app' => $this->checkApp(),
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'session' => $this->checkSession(),
            'queue' => $this->checkQueue(),
            'storage' => $this->checkStorage(),
        ];

        $systemInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
        ];

        return view('admin.health.index', compact('checks', 'systemInfo'));
    }

    private function checkApp()
    {
        return [
            'status' => 'ok',
            'message' => 'Application is running',
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::select('SELECT 1');
            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
                'driver' => config('database.default'),
            ];
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkCache()
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test';
            
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);
            
            if ($retrieved === $testValue) {
                return [
                    'status' => 'ok',
                    'message' => 'Cache is working',
                    'driver' => config('cache.default'),
                ];
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'Cache read/write test failed',
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'fail',
                'message' => 'Cache error: ' . $e->getMessage(),
            ];
        }
    }

    private function checkSession()
    {
        $driver = config('session.driver');
        return [
            'status' => 'ok',
            'message' => "Session driver: {$driver}",
            'driver' => $driver,
        ];
    }

    private function checkQueue()
    {
        $driver = config('queue.default');
        $connection = config("queue.connections.{$driver}");
        
        return [
            'status' => 'ok',
            'message' => "Queue driver: {$driver}",
            'driver' => $driver,
        ];
    }

    private function checkStorage()
    {
        $storagePath = storage_path();
        $publicStorage = public_path('storage');
        
        $storageWritable = is_writable($storagePath);
        $publicWritable = file_exists($publicStorage) && is_writable($publicStorage);
        
        if ($storageWritable && $publicWritable) {
            return [
                'status' => 'ok',
                'message' => 'Storage directories are writable',
            ];
        } else {
            $issues = [];
            if (!$storageWritable) $issues[] = 'storage/ not writable';
            if (!$publicWritable) $issues[] = 'public/storage not writable or linked';
            
            return [
                'status' => 'fail',
                'message' => 'Storage issues: ' . implode(', ', $issues),
            ];
        }
    }
}
