<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\Feedback;
use App\Models\Gallery;
use App\Models\RoomType;
use App\Models\Visitor;
use App\Models\WhatsappLead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HotelStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRooms = RoomType::count();
        $totalGallery = Gallery::count();
        $unreadMessages = Contact::where('is_read', false)->count();
        $whatsappLeads = WhatsappLead::count();
        $approvedFeedback = Feedback::where('is_approved', true)->count();
        $todayVisitors = Visitor::whereDate('created_at', today())->distinct('ip_address')->count();

        return [
            Stat::make('Total Rooms', $totalRooms)
                ->description('Hotel rooms')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('primary')
                ->url(route('filament.admin.resources.room-types.index')),

            Stat::make('Gallery Images', $totalGallery)
                ->description('Photos uploaded')
                ->descriptionIcon('heroicon-o-photo')
                ->color('info')
                ->url(route('filament.admin.resources.galleries.index')),

            Stat::make('Unread Messages', $unreadMessages)
                ->description('Awaiting response')
                ->descriptionIcon('heroicon-o-envelope')
                ->color($unreadMessages > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.contacts.index')),

            Stat::make('WhatsApp Leads', $whatsappLeads)
                ->description('Total leads captured')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->url(route('filament.admin.resources.whatsapp-leads.index')),

            Stat::make('Approved Feedback', $approvedFeedback)
                ->description('Published reviews')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning')
                ->url(route('filament.admin.resources.feedback.index')),

            Stat::make('Today\'s Visitors', $todayVisitors)
                ->description('Unique IPs today')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}
