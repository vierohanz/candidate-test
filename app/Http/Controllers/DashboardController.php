<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Support\ApiPageCache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $payload = ApiPageCache::remember('dashboard', ['page' => 'dashboard'], 30, function () {
            return [
                'stats' => [
                    'suppliers' => Supplier::count(),
                    'layups' => CltLayup::count(),
                    'layers' => CltLayer::count(),
                    'activities' => ActivityLog::count(),
                ],
                'recentActivity' => ActivityLog::latest('created_at')->limit(8)->get(),
            ];
        });

        return view('dashboard', $payload);
    }
}
