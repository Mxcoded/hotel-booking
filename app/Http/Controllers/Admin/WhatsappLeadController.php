<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsappLeadController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $leads = WhatsappLead::leftJoin('visitors', 'whatsapp_leads.ip_address', '=', 'visitors.ip_address')
            ->select(
                'whatsapp_leads.id',
                'whatsapp_leads.name',
                'whatsapp_leads.phone',
                'whatsapp_leads.created_at',
                DB::raw('MAX(visitors.created_at) as last_seen_at')
            )
            ->groupBy('whatsapp_leads.id', 'whatsapp_leads.name', 'whatsapp_leads.phone', 'whatsapp_leads.created_at')
            ->orderBy($sort, $direction)
            ->paginate(20);

        return view('admin.whatsapp-leads.index', compact('leads'));
    }

    public function destroy(WhatsappLead $whatsappLead)
    {
        $whatsappLead->delete();
        return redirect()->route('admin.whatsapp-leads.index')->with('success', 'Lead deleted successfully.');
    }
}
