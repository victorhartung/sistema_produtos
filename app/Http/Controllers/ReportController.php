<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\StockEntriesReport;
use App\Exports\StockExitsReport;
use App\Models\Requisition;

class ReportController extends Controller
{
    public function index() {
        
        return view('reports.index');
    
    }

    // public function getEntryStockReport(Request $request) {

    //     $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
    //     $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

    //     //query para relatório de entrada
    //     $sql = "SELECT
    //                 p.name AS product_name,
    //                 SUM(r.amount) AS total_amount,
    //                 SUM(r.amount * p.cost_price) AS total_cost_price,
    //                 SUM(r.amount * p.retail_price) AS total_retail_price
    //             FROM
    //                 requisitions r
    //             JOIN
    //                 products p ON r.product_id = p.id
    //             WHERE
    //                 r.is_exit = FALSE
    //                 AND r.requisition_date BETWEEN ? AND ?
    //                 AND p.composite = FALSE
    //             GROUP BY
    //                 p.id, p.name

    //             UNION ALL

    //             SELECT
    //                 ps.name AS product_name,
    //                 SUM(r.amount) AS total_amount,
    //                 SUM(r.amount * ps.cost_price) AS total_cost_price,
    //                 SUM(r.amount * ps.retail_price) AS total_retail_price
    //             FROM
    //                 requisitions r
    //             JOIN
    //                 composite_products cp ON r.product_id = cp.composite_id
    //             JOIN
    //                 products ps ON cp.composite_id = ps.id
    //             WHERE
    //                 r.is_exit = FALSE
    //                 AND r.requisition_date BETWEEN ? AND ?
    //             GROUP BY
    //                 cp.simple_id, ps.name

    //             ORDER BY
    //                 product_name;";


    //         $reportData = DB::select(DB::raw($sql), [
    //             $startDate, $endDate, $startDate, $endDate

    //         ]);


    //     return view('reports.entry-report', compact('reportData'));

    // }

    //Exporta excel com relatório de entrada
    public function exportStockEntries(Request $request) {

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $export = new StockEntriesReport($startDate, $endDate);

        //dd($export->getData());

        return Excel::create($export->getFilename(), function($excel) use ($export) {
            $excel->sheet('Sheet1', function($sheet) use ($export) {
                $sheet->fromArray($export->getData(), null, 'A1', false, false);
                $sheet->prependRow(['Produto', 'Quantidade Total', 'Preço Total de Custo', 'Preço Total de Venda']);
            });
        })->download('xlsx');
    }
    
    //Exporta excel com relatório de saída
    public function exportStockExits(Request $request) {

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $export = new StockExitsReport($startDate, $endDate);

        //dd($export->getData());

        return Excel::create($export->getFilename(), function($excel) use ($export) {
            $excel->sheet('Sheet1', function($sheet) use ($export) {
                $sheet->fromArray($export->getData(), null, 'A1', false, false);
                $sheet->prependRow(['Produto', 'Quantidade Total', 'Preço Total de Custo', 'Preço Total de Venda']);
            });
        })->download('xlsx');
    }

    // public function getExitStockReport(Request $request) {
        
    //     $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
    //     $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
        

    //     //query para relatório de saída
    //     $sql = "SELECT
    //                 p.id AS product_id,
    //                 p.name AS product_name,
    //                 SUM(r.amount) AS total_amount,
    //                 SUM(r.amount * p.cost_price) AS total_cost_price,
    //                 SUM(r.amount * p.retail_price) AS total_retail_price
    //             FROM
    //                 requisitions r
    //             JOIN
    //                 products p ON r.product_id = p.id
    //             WHERE
    //                 r.is_exit = TRUE
    //                 AND r.requisition_date BETWEEN ? AND ?
    //                 AND p.composite = FALSE
    //             GROUP BY
    //                 p.id, p.name

    //             UNION ALL

    //             SELECT
    //                 ps.id AS product_id,
    //                 ps.name AS product_name,
    //                 SUM(r.amount * cp.amount) AS total_amount,
    //                 SUM(r.amount * cp.amount * ps.cost_price) AS total_cost_price,
    //                 SUM(r.amount * cp.amount * ps.retail_price) AS total_retail_price
    //             FROM
    //                 requisitions r
    //             JOIN
    //                 composite_products cp ON r.product_id = cp.composite_id
    //             JOIN
    //                 products ps ON cp.simple_id = ps.id
    //             WHERE
    //                 r.is_exit = TRUE
    //                 AND r.requisition_date BETWEEN ? AND ?
    //             GROUP BY
    //                 ps.id, ps.name

    //             ORDER BY
    //                 product_name;";

    //     //DB::enableQueryLog();
    //     $reportData = DB::select(DB::raw($sql), [
    //         $startDate, $endDate, $startDate, $endDate

    //     ]);
             
    //     //dd(DB::getQueryLog());

    //     return view('reports.exit-report', compact('reportData'));
    // }
}
