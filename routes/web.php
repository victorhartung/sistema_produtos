<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function() {
    Route::get('/home', 'HomeController@index')->name('home');

    //Rotas para usuário
    Route::resource('users', 'UserController');

    //Rotas para produtos
    Route::get('/products/table', 'ProductController@dataTable')->name('products.table');
    Route::get('/products/search', 'ProductController@search')->name('products.search');
    Route::resource('products', 'ProductController');
   
    //Rotas para requisição
    Route::resource('requisitions', 'RequisitionController');
    Route::get('/get-requisition-data', 'RequisitionController@getData')->name('requisitions.getData');

    //Rotas para estoque
    Route::resource('stocks', 'StockController');
    Route::get('/get-stocks-data', 'StockController@getData')->name('stocks.getData');

    //Rotas para relatório
    Route::get('/relatorios', 'ReportController@index')->name('reports.index');
    // Route::get('/relatorio/entrada-estoque', 'ReportController@getEntryStockReport')->name('report.entry_stock');
    // Route::get('/relatorio/saida-estoque', 'ReportController@getExitStockReport')->name('report.exit_stock');

    Route::get('/export-stock-entries', 'ReportController@exportStockEntries')->name('get_excel_entries');
    Route::get('/export-stock-exits', 'ReportController@exportStockExits')->name('get_excel_exits');

});