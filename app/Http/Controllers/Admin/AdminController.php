<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\RoomType;
use App\Models\RoomUnit;
use App\Models\Visitor;
use App\Models\WhatsappLead;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $roomTypeCount = RoomType::count();
        $totalUnits = RoomUnit::where('is_active', true)->count();
        $availableUnits = RoomUnit::where('is_active', true)->where('status', 'available')->count();
        $galleryCount = Gallery::count();
        $unreadMessages = Contact::where('is_read', false)->count();
        $whatsappLeadsCount = WhatsappLead::count();
        $approvedFeedbackCount = Feedback::where('is_approved', true)->count();
        $unreadFeedbackCount = Feedback::where('is_read', false)->count();


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
            'roomTypeCount',
            'totalUnits',
            'availableUnits',
            'galleryCount',
            'unreadMessages',
            'whatsappLeadsCount',
            'approvedFeedbackCount',
            'unreadFeedbackCount',
            'visitorLabels',
            'visitorData',
            'todayVisitors'
        ));
    }
}