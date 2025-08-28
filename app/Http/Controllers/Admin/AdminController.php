<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with dynamic stats.
     */
    public function dashboard()
    {
        $roomCount = Room::count();
        $galleryCount = Gallery::count();
        $unreadMessagesCount = Contact::where('is_read', false)->count();
        $todaysVisitors = Visitor::where('visited_date', today())->count();

        // Get visitor data for the last 7 days for the chart
        $visitorData = Visitor::select(
            DB::raw('DATE(visited_date) as date'),
            DB::raw('count(*) as visits')
        )
            ->where('visited_date', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Prepare data for the chart, ensuring all 7 days are present
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('D, M j');
            $visits = $visitorData->firstWhere('date', $date);
            $chartData[] = $visits ? $visits->visits : 0;
        }

        return view('admin.dashboard', compact(
            'roomCount',
            'galleryCount',
            'unreadMessagesCount',
            'todaysVisitors',
            'chartLabels',
            'chartData'
        ));
    }
}
