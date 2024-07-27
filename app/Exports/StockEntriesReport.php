<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Files\NewExcelFile;
use Carbon\Carbon;

class StockEntriesReport extends NewExcelFile
{

    protected $startDate;
    protected $endDate;


    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getFilename()
    {
        return 'stock_entries.xlsx';
    }

    public function getData() {

        //DB::enableQueryLog();
        $this->startDate = Carbon::parse($this->startDate)->format('Y-m-d');
        $this->endDate = Carbon::parse($this->endDate)->format('Y-m-d');
    
        $sql = "SELECT
                p.name AS product_name,
                SUM(r.amount) AS total_amount,
                SUM(r.amount * p.cost_price) AS total_cost_price,
                SUM(r.amount * p.retail_price) AS total_retail_price
            FROM
                requisitions r
            JOIN
                products p ON r.product_id = p.id
            WHERE
                r.is_exit = FALSE
                AND r.requisition_date BETWEEN ? AND ?
                AND p.composite = FALSE
            GROUP BY
                p.id, p.name

            UNION ALL

            SELECT
                ps.name AS product_name,
                SUM(r.amount) AS total_amount,
                SUM(r.amount * ps.cost_price) AS total_cost_price,
                SUM(r.amount * ps.retail_price) AS total_retail_price
            FROM
                requisitions r
            JOIN
                composite_products cp ON r.product_id = cp.composite_id
            JOIN
                products ps ON cp.composite_id = ps.id
            WHERE
                r.is_exit = FALSE
                AND r.requisition_date BETWEEN ? AND ?
            GROUP BY
                cp.simple_id, ps.name

            ORDER BY
                product_name;"; 

                //dd($sql);
        //dd(DB::getQueryLog());
        $data = DB::select(DB::raw($sql), [
            $this->startDate, $this->endDate, $this->startDate, $this->endDate

        ]);

        return collect($data)->map(function($rows) {
            return (array) $rows;
        });

   

    }

}