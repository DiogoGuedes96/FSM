<?php

namespace Modules\Dashboard\Services;
use Modules\Orders\Entities\Order;
use Modules\Clients\Entities\Clients;
use Modules\Calls\Entities\AsteriskCall;
use Carbon\Carbon;
use Modules\Primavera\Entities\PrimaveraInvoices;

class DashboardService
{
    private $wherePeriods;
    private $wherePeriodsInvoices;

    public function __construct()
    {
        $this->wherePeriods = [
            'now' => "writen_date >= '". date('Y-m-d') ." 00:00:00'",
            'week' => "writen_date >= '". date('Y-m-d', strtotime("-7 days")) ." 00:00:00'",
            'month' => "writen_date >= '". date('Y-m-d', strtotime("-30 days")) ." 00:00:00'",
            'last_week' => "writen_date BETWEEN '". date('Y-m-d', strtotime("-14 days")) ." 00:00:00' AND '". date('Y-m-d', strtotime("-7 days")) ." 00:00:00'",
            'last_month' => "writen_date BETWEEN '". date('Y-m-d', strtotime("-60 days")) ." 00:00:00' AND '". date('Y-m-d', strtotime("-30 days")) ." 00:00:00'"
        ];

        $this->wherePeriodsInvoices = [
            'now' => "invoice_date >= '". date('Y-m-d') ." 00:00:00'",
            'week' => "invoice_date >= '". date('Y-m-d', strtotime("-7 days")) ." 00:00:00'",
            'month' => "invoice_date >= '". date('Y-m-d', strtotime("-30 days")) ." 00:00:00'",
            'last_week' => "invoice_date BETWEEN '". date('Y-m-d', strtotime("-14 days")) ." 00:00:00' AND '". date('Y-m-d', strtotime("-7 days")) ." 00:00:00'",
            'last_month' => "invoice_date BETWEEN '". date('Y-m-d', strtotime("-60 days")) ." 00:00:00' AND '". date('Y-m-d', strtotime("-30 days")) ." 00:00:00'"
        ];
    }

    public function getInvoicesKpis($period = 'week')
    {
        $wherePeriod = str_replace(' 00:00:00', '', $this->wherePeriodsInvoices[$period]);

        $kpis = [
            'received_total_value' => PrimaveraInvoices::whereRaw($wherePeriod)
                ->sum('total_value'),
        ];

        if ($period != 'now') {
            $wherePeriod = str_replace(' 00:00:00', '', $this->wherePeriodsInvoices['last_' . $period]);

            $kpis['last_received_total_value'] = PrimaveraInvoices::whereRaw($wherePeriod)
               ->sum('total_value');
        }

        return $kpis;
    }

    public function getClientsKpis($period = 'now')
    {
        return [
            'total' => Clients::where('status', '!=', 'INACTIVO')->count()
        ];
    }

    public function getCallsKpis($period = 'now')
    {
        $wherePeriod = str_replace('writen_date', 'created_at', $this->wherePeriods[$period]);

        return [
            'lost' => AsteriskCall::whereRaw($wherePeriod)
                ->where('status', '=', 'missed')
                ->orWhereIn('hangup_status', ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603'])
                ->count(),
            'hangup' => AsteriskCall::whereRaw($wherePeriod)
                ->where('status', '=', 'hangup')->count(),
            'total' => AsteriskCall::whereRaw($wherePeriod)->count()
        ];
    }

    public function getOrdersKpis($period = 'now')
    {
        $wherePeriod = str_replace(' 00:00:00', '', $this->wherePeriods[$period]);

        return [
            'by_calls' => Order::whereRaw($wherePeriod)
                ->whereNull('erp_invoice_id')->count(),
            'pending' => Order::whereRaw($wherePeriod)
                ->where('status', '=', 'pending')->count(),
            'completed' => Order::whereRaw($wherePeriod)
                ->where('status', '=', 'completed')->count()
        ];
    }
}
