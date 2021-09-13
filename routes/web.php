<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoiceArchiveController;
use App\Http\Controllers\Customers_ReportsController;

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
    return view('auth.login');
});

Route::get('dashboard', 'HomeController@index')->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';



// customer reeports
Route::get('customers_report', 'Customers_ReportsController@index')->name("customers_report");

Route::post('Search_customers', 'Customers_ReportsController@Search_customers');

// routes for invoices table Reports
Route::get('invoices_report', 'Invoices_Report@index');

Route::post('Search_invoices', 'Invoices_Report@Search_invoices');


Route::resource('Archive', 'InvoiceArchiveController');

//  الفواتير المدفوعة
Route::get('Invoice_Paid','InvoicesController@Invoice_Paid');

// الفواتير الغير مدفوعة
Route::get('Invoice_UnPaid','InvoicesController@Invoice_UnPaid');

// الفواتير المدفوعة جزئيا
Route::get('Invoice_Partial','InvoicesController@Invoice_Partial');

// طباعة الفاتورة
Route::get('Print_invoice/{id}','InvoicesController@Print_invoice');

// تصدير الفاتورة لملف اكسل
Route::get('export_invoices', 'InvoicesController@export');

// جعل كل الرسائل مقروءة
Route::get('MarkAsRead_all','InvoicesController@MarkAsRead_all')->name('MarkAsRead_all');

Route::get('unreadNotifications_count', 'InvoicesController@unreadNotifications_count')->name('unreadNotifications_count');

Route::get('unreadNotifications', 'InvoicesController@unreadNotifications')->name('unreadNotifications');

Route::resource('invoices', 'InvoicesController');

Route::resource('sections', 'SectionController');

Route::resource('products', 'ProductController');

Route::resource('InvoiceAttachments', 'InvoiceAttachmentsController');

Route::get('/section/{id}', 'InvoicesController@getproducts');

Route::get('/InvoicesDetails/{id}', 'InvoicesDetailsController@edit');


Route::get('download/{invoice_number}/{file_name}', 'InvoicesDetailsController@get_file');

Route::get('View_file/{invoice_number}/{file_name}', 'InvoicesDetailsController@open_file');

Route::post('delete_file', 'InvoicesDetailsController@destroy')->name('delete_file');

Route::get('/{page}', 'AdminController@index');

Route::get('/edit_invoice/{id}', 'InvoicesController@edit');

Route::get('/Status_show/{id}', 'InvoicesController@show')->name('Status_show');

Route::post('/Status_Update/{id}', 'InvoicesController@Status_Update')->name('Status_Update');

// routes for permissions and roles
Route::group(['middleware' => ['auth']], function() {

    Route::resource('roles','RoleController');
    
    Route::resource('users','UserController');
    
    });








