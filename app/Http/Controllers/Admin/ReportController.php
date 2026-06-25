<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to, $period] = $this->period($request);
        $sales = Sale::where('status', 'completed')->whereBetween('created_at', [$from, $to]);
        $items = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed')->whereBetween('created_at', [$from, $to]));

        return view('admin.reports.index', [
            'period' => $period, 'from' => $from, 'to' => $to,
            'salesCount' => (clone $sales)->count(),
            'revenue' => (clone $sales)->sum('grand_total'),
            'profit' => (clone $items)->selectRaw('COALESCE(SUM((unit_price - purchase_price) * (quantity - returned_quantity)),0) total')->value('total'),
            'topProducts' => (clone $items)->selectRaw('product_name, SUM(quantity-returned_quantity) quantity, SUM(line_total) revenue')->groupBy('product_name')->orderByDesc('quantity')->limit(10)->get(),
            'lowStock' => Product::whereColumn('stock_quantity', '<=', 'low_stock_alert')->count(),
            'inventoryValue' => Product::sum(\DB::raw('purchase_price * stock_quantity')) + ProductVariant::sum(\DB::raw('purchase_price * stock_quantity')),
        ]);
    }

    private function period(Request $request): array
    {
        $period = $request->input('period', 'daily');
        $now = now();

        return match ($period) {
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), $period],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), $period],
            'custom' => [Carbon::parse($request->input('from', today()))->startOfDay(), Carbon::parse($request->input('to', today()))->endOfDay(), $period],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'daily'],
        };
    }
}
