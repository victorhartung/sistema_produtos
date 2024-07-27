<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Files\NewExcelFile;

class StockExitsReport extends NewExcelFile
{

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate) {
        
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    
    }

    public function getFilename() {
        
        return 'stock_exits.xlsx';
    
    }

    public function getData() {
                 
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
                    r.is_exit = TRUE
                    AND r.requisition_date BETWEEN ? AND ?
                    AND p.composite = FALSE
                GROUP BY
                        p.name

                UNION ALL

                -- Relatório de saída dos componentes do produto composto
                SELECT
                    
                    ps.name AS product_name,
                    SUM(r.amount * cp.amount) AS total_amount,
                    SUM(r.amount * cp.amount * ps.cost_price) AS total_cost_price,
                    SUM(r.amount * cp.amount * ps.retail_price) AS total_retail_price
                FROM
                    requisitions r
                JOIN
                    composite_products cp ON r.product_id = cp.composite_id
                JOIN
                    products ps ON cp.simple_id = ps.id
                WHERE
                    r.is_exit = TRUE
                    AND r.requisition_date BETWEEN ? AND ?
                GROUP BY
                        ps.name

                ORDER BY
                    product_name";
                       
        $data = DB::select(DB::raw($sql), [
            $this->startDate, $this->endDate, $this->startDate, $this->endDate
        ]);
    
        return collect($data)->map(function($rows) {
            return (array) $rows;
        });       
    }
}