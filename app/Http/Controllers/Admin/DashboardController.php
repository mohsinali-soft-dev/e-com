<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $todaySales = Sale::whereDate('created_at', today());

        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalBrands' => Brand::count(),
            'totalBarcodes' => ProductBarcode::count(),
            'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'low_stock_alert')->count(),
            'salesToday' => (clone $todaySales)->count(),
            'revenueToday' => (clone $todaySales)->sum('grand_total'),
            'totalRevenue' => Sale::where('status', 'completed')->sum('grand_total'),
            'topProducts' => SaleItem::query()
                ->selectRaw('product_id, product_name, SUM(quantity) as quantity_sold, SUM(line_total) as revenue')
                ->groupBy('product_id', 'product_name')
                ->orderByDesc('quantity_sold')
                ->limit(5)
                ->get(),
        ]);
    }
}
