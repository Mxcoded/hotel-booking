<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\Visitor;
use App\Models\WhatsappLead;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with dynamic stats.
     */
    // public function dashboard()
    // {
    //     $roomCount = Room::count();
    //     $galleryCount = Gallery::count();
    //     $unreadMessagesCount = Contact::where('is_read', false)->count();
    //     $todaysVisitors = Visitor::where('visited_date', today())->count();

    //     // Get visitor data for the last 7 days for the chart
    //     $visitorData = Visitor::select(
    //         DB::raw('DATE(visited_date) as date'),
    //         DB::raw('count(*) as visits')
    //     )
    //         ->where('visited_date', '>=', Carbon::now()->subDays(6))
    //         ->groupBy('date')
    //         ->orderBy('date', 'ASC')
    //         ->get();

    //     // Prepare data for the chart, ensuring all 7 days are present
    //     $chartLabels = [];
    //     $chartData = [];
    //     for ($i = 6; $i >= 0; $i--) {
    //         $date = Carbon::now()->subDays($i)->format('Y-m-d');
    //         $chartLabels[] = Carbon::parse($date)->format('D, M j');
    //         $visits = $visitorData->firstWhere('date', $date);
    //         $chartData[] = $visits ? $visits->visits : 0;
    //     }

    //     return view('admin.dashboard', compact(
    //         'roomCount',
    //         'galleryCount',
    //         'unreadMessagesCount',
    //         'todaysVisitors',
    //         'chartLabels',
    //         'chartData'
    //     ));
    // }


    public function dashboard()
    {
        $roomCount = Room::count();
        $galleryCount = Gallery::count();
        $unreadMessages = Contact::where('is_read', false)->count();
        $whatsappLeadsCount = WhatsappLead::count(); // WhatsApp leads count
        $approvedFeedbackCount = Feedback::where('is_approved', true)->count(); //count the approved feedback
        $unreadFeedbackCount = Feedback::where('is_read', false)->count(); //count the unread feedback


        // Visitor Chart Data
        $visitors = Visitor::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(DISTINCT ip_address) as unique_visits')
        )
        ->where('created_at', '>=', now()->subDays(6))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        $visitorLabels = $visitors->map(fn ($v) => \Carbon\Carbon::parse($v->date)->format('M d'));
        $visitorData = $visitors->map(fn ($v) => $v->unique_visits);
        $todayVisitors = Visitor::whereDate('created_at', today())->distinct('ip_address')->count();


        return view('admin.dashboard', compact(
            'roomCount', 
            'galleryCount', 
            'unreadMessages', 
            'whatsappLeadsCount', // WhatsApp leads count
            'approvedFeedbackCount', // Approved feedback count
            'unreadFeedbackCount', // Unread feedback count
            'visitorLabels', 
            'visitorData',
            'todayVisitors'
        ));
    }
}

