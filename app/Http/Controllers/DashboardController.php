<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'suppliers'  => Supplier::count(),
                'layups'     => CltLayup::count(),
                'layers'     => CltLayer::count(),
                'activities' => ActivityLog::count(),
            ],
            'recentActivity' => ActivityLog::latest('created_at')->limit(8)->get(),
        ]);
    }
}
