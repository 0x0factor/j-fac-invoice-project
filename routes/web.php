<?php

use App\Http\Controllers\AdministerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\CustomerchargeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\CoverpageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PostcodeController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TotalbillController;
use App\Http\Controllers\View_optionController;
use App\Http\Controllers\ZipcodeController;

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\BarangController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//route login
Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'process']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// route dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/', [HomeController::class, 'index'])->middleware('auth');

Route::get('/homes', [HomeController::class, 'index'])->name('home');


// Route::get('customers/add', [CustomerController::class, 'addCustomers'])->name('customers.add');

Route::get('/customers', [CustomerController::class, "index"])->name('customer.index');
Route::get('/customers/select', [CustomerController::class,"select"])->name('customer.select');
Route::get('/customers/check/{customer_ID}', [CustomerController::class, "check"])->name('customer.check');
Route::get('/customers/add', [CustomerController::class,"add"])->name('customer.add');
Route::post('/customers/add', [CustomerController::class ,'add'])->name('customer.save');
Route::get('/customers/edit/{customer_ID}', [CustomerController::class, 'edit'])->name('customer.edit');
Route::post('/customers/edit/{customer_ID}', [CustomerController::class,'edit'])->name('customer.update');



Route::get('quotes', [QuoteController::class, 'index'])->name('quote.index');
Route::get('quotes/add', [QuoteController::class, 'add'])->name('quote.add');
Route::get('quotes/create', [QuoteController::class, 'create'])->name('quote.create');
Route::get('quotes/export', [QuoteController::class, 'export'])->name('quote.export');
Route::get('quotes/action', [QuoteController::class, 'action'])->name('quote.action');


Route::get('bills', [BillController::class, 'index'])->name('bill.index');
Route::get('bills/add', [BillController::class, 'add'])->name('bill.add');
Route::get('bills/index', [BillController::class, 'index'])->name('bill.index');
Route::get('bills/export', [BillController::class, 'index'])->name('bill.export');
Route::post('bills/download-pdf', [BillController::class, 'downloadPDF'])->name('bill.download-pdf');

Route::get('totalbills', [TotalbillController::class, 'index'])->name('totalbill.index');
Route::get('totalbills/add', [TotalbillController::class, 'add'])->name('totalbill.add');


Route::get('deliveries', [DeliveryController::class, 'index'])->name('delivery.index');
Route::get('deliveries/add', [DeliveryController::class, 'add'])->name('delivery.add');
Route::get('deliveries/export', [DeliveryController::class, 'export'])->name('delivery.export');

Route::get('charges', [ChargeController::class, 'index'])->name('charge.index');
Route::get('charges/add', [ChargeController::class, 'add'])->name('charge.add');

Route::get('items', [ItemController::class, 'index'])->name('item.index');
Route::get('items/add', [ItemController::class, 'add'])->name('item.add');
Route::get('items/delete', [ItemController::class, 'delete'])->name('item.delete');


Route::get('mails', [MailController::class, 'index'])->name('mail.index');
Route::get('customercharges', [CustomerchargeController::class, 'index'])->name('customer_charge.index');
Route::get('customercharges/add', [CustomerchargeController::class, 'add'])->name('customer_charge.add');

Route::get('companies', [CompanyController::class, 'index'])->name('company.index');
Route::get('companies/edit', [CompanyController::class, 'edit'])->name('companies.edit');

Route::get('coverpages', [CoverpageController::class, 'index'])->name('coverpage.index');
Route::get('coverpages/store', [CoverpageController::class, 'store'])->name('coverpage.store');

Route::get('administers', [AdministerController::class, 'index'])->name('administer.index');
Route::get('administers/add', [AdministerController::class, 'add'])->name('administer.add');
Route::get('histories', [HistoryController::class, 'index'])->name('history.index');
Route::get('configurations', [ConfigurationController::class, 'index'])->name('configuration.index');
Route::get('postcode', [PostcodeController::class, 'index'])->name('postcode.index');

Route::get('view_options', [View_optionController::class, 'index'])->name('view_option.index');
Route::get('view_options/edit', [View_optionController::class, 'edit'])->name('view_option.edit');

Route::get('personals/passEdit', [PersonalController::class, 'passEdit'])->name('personal.passEdit');

Route::get('/zipcode', [ZipcodeController::class, 'index'])->name('zipcode.index');
Route::post('/zipcode/update', [ZipcodeController::class, 'update'])->name('zipcode.update');
Route::get('/zipcode/reset', [ZipcodeController::class, 'reset'])->name('zipcode.reset');

//route barang
Route::resource('/barang', BarangController::class)->middleware('auth');
