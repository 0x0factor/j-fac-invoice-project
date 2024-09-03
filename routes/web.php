<?php

use App\Http\Controllers\AdministerController;
use App\Http\Controllers\AjaxController;
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
use App\Http\Controllers\PasswordController;

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
Route::get('/users/reset', [AuthController::class, 'reset'])->name('users.reset');
Route::post('/users/reset', [AuthController::class, 'resetPost'])->name('users.reset');
Route::post('/users/pass_edit', [AuthController::class, 'resetPost'])->name('users.pass_edit');
Route::get('/users/reset_end', [AuthController::class, 'resetEnd'])->name('users.reset_end');
Route::get('/users/pass_edit_end', [AuthController::class, 'passEditEnd'])->name('users.pass_edit_end');


// route dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/', [HomeController::class, 'index'])->middleware('auth');

Route::get('/homes', [HomeController::class, 'index'])->name('home');

Route::get('administers', [AdministerController::class, 'index'])->name('administer.index');
Route::get('administers/add', [AdministerController::class, 'add'])->name('administer.add');
Route::post('administers/store', [AdministerController::class, 'store'])->name('administer.store');
Route::get('administers/edit', [AdministerController::class, 'edit'])->name('administer.edit');
Route::get('administers/check', [AdministerController::class, 'check'])->name('administer.check');

Route::get('ajax/popup', [AjaxController::class, 'popup'])->name('ajax.popup');
Route::post('ajax/popupInsert', [AjaxController::class, 'popupInsert'])->name('ajax.popupInsert');


Route::get('bills', [BillController::class, 'index'])->name('bill.index');
Route::get('bills/add', [BillController::class, 'add'])->name('bill.add');
Route::post('bills/add', [BillController::class, 'add'])->name('bill.add');
Route::get('bills/check', [BillController::class, 'check'])->name('bill.check');
Route::get('bills/receipt', [BillController::class, 'receipt'])->name('bill.receipt');
Route::get('bills/export', [BillController::class, 'export'])->name('bill.export');
Route::post('bills/export', [BillController::class, 'export'])->name('bill.export');
Route::get('bills/action', [BillController::class, 'action'])->name('bill.action');
Route::delete('bills/action', [BillController::class, 'action'])->name('bill.action');
Route::post('bills/download-pdf', [BillController::class, 'downloadPDF'])->name('bill.download-pdf');

Route::get('charges', [ChargeController::class, 'index'])->name('charge.index');
Route::get('charges/add', [ChargeController::class, 'add'])->name('charge.add');
Route::post('charges/add', [ChargeController::class, 'add'])->name('charge.add');
Route::get('charges/delete', [ChargeController::class, 'delete'])->name('charge.delete');
Route::get('charges/check/{charge_ID}', [ChargeController::class, 'check'])->name('charge.check');
Route::get('charges/edit', [ChargeController::class, 'edit'])->name('charge.edit');

Route::get('companies', [CompanyController::class, 'index'])->name('company.index');
Route::get('companies/edit', [CompanyController::class, 'edit'])->name('companies.edit');

Route::get('configurations', [ConfigurationController::class, 'index'])->name('configuration.index');
Route::get('configurations/edit', [ConfigurationController::class, 'edit'])->name('configuration.edit');

Route::get('coverpages', [CoverpageController::class, 'index'])->name('coverpage.index');
Route::get('coverpages/store', [CoverpageController::class, 'store'])->name('coverpage.store');

// Route::get('customers/add', [CustomerController::class, 'addCustomers'])->name('customers.add');

Route::get('customers', [CustomerController::class, "index"])->name('customer.index');
Route::get('customers/select', [CustomerController::class,"select"])->name('customer.select');
Route::get('customers/check/{customer_ID}', [CustomerController::class, "check"])->name('customer.check');
Route::get('customers/add', [CustomerController::class,"add"])->name('customer.add');
Route::post('customers/add', [CustomerController::class ,'add'])->name('customer.save');
Route::post('customers/delete', [CustomerController::class ,'delete'])->name('customer.delete');
Route::get('customers/edit/{customer_ID}', [CustomerController::class, 'edit'])->name('customer.edit');
Route::post('customers/edit/{customer_ID}', [CustomerController::class,'edit'])->name('customer.update');

Route::get('customer_charges', [CustomerchargeController::class, 'index'])->name('customer_charge.index');
Route::get('customer_charges/add', [CustomerchargeController::class, 'add'])->name('customer_charge.add');
Route::post('customer_charges/add', [CustomerchargeController::class, 'add'])->name('customer_charge.add');
Route::get('customer_charges/check', [CustomerchargeController::class, 'check'])->name('customer_charge.check');
Route::get('customer_charges/edit', [CustomerchargeController::class, 'edit'])->name('customer_charge.edit');
Route::get('customer_charges/delete', [CustomerchargeController::class, 'delete'])->name('customer_charge.delete');

Route::get('deliveries', [DeliveryController::class, 'index'])->name('delivery.index');
Route::get('deliveries/add', [DeliveryController::class, 'add'])->name('delivery.add');
Route::get('deliveries/export', [DeliveryController::class, 'export'])->name('delivery.export');
Route::post('deliveries/export', [DeliveryController::class, 'export'])->name('delivery.export');
Route::get('deliveries/check', [DeliveryController::class, 'check'])->name('delivery.check');
Route::get('deliveries/edit', [DeliveryController::class, 'edit'])->name('delivery.edit');
Route::post('deliveries/action', [DeliveryController::class, 'action'])->name('delivery.action');
Route::delete('deliveries/action', [DeliveryController::class, 'action'])->name('delivery.action');

Route::get('histories', [HistoryController::class, 'index'])->name('history.index');

Route::get('items', [ItemController::class, 'index'])->name('item.index');
Route::get('items/add', [ItemController::class, 'add'])->name('item.add');
Route::post('items/store', [ItemController::class, 'store'])->name('item.store');
Route::get('items/check/{item_ID}', [ItemController::class, 'check'])->name('item.check');
Route::get('items/edit', [ItemController::class, 'edit'])->name('item.edit');
Route::get('items/delete', [ItemController::class, 'delete'])->name('item.delete');

Route::get('mails', [MailController::class, 'index'])->name('mail.index');
Route::get('mails/check', [MailController::class, 'check'])->name('mail.check');

Route::get('personals/passEdit', [PersonalController::class, 'passEdit'])->name('personal.passEdit');

Route::get('postcode', [PostcodeController::class, 'index'])->name('postcode.index');
Route::get('postcode/update', [PostcodeController::class, 'update'])->name('postcode.update');
Route::get('postcode/reset', [PostcodeController::class, 'reset'])->name('postcode.reset');

Route::get('quotes', [QuoteController::class, 'index'])->name('quote.index');
Route::get('quotes/add', [QuoteController::class, 'add'])->name('quote.add');
Route::post('quotes/add', [QuoteController::class, 'add'])->name('quote.add');
Route::get('quotes/edit/{quoteId}', [QuoteController::class, 'edit'])->name('quote.edit');
Route::get('quotes/export', [QuoteController::class, 'export'])->name('quote.export');
Route::post('quotes/export', [QuoteController::class, 'export'])->name('quote.export');
Route::get('quotes/check', [QuoteController::class, 'check'])->name('quote.check');
Route::get('quotes/action', [QuoteController::class, 'action'])->name('quote.action');
Route::delete('/quotes/action', [QuoteController::class, 'action'])->name('quotes.action');
Route::post('quotes/action', [QuoteController::class, 'action'])->name('quote.action');

Route::get('totalbills', [TotalbillController::class, 'index'])->name('totalbill.index');
Route::get('totalbills/add', [TotalbillController::class, 'add'])->name('totalbill.add');
Route::post('totalbills/add', [TotalbillController::class, 'add'])->name('totalbill.add');
Route::get('totalbills/store', [TotalbillController::class, 'store'])->name('totalbill.store');
Route::get('totalbills/check', [TotalbillController::class, 'check'])->name('totalbill.check');
Route::get('totalbills/delete', [TotalbillController::class, 'delete'])->name('totalbill.delete');
Route::get('totalbills/edit', [TotalbillController::class, 'edit'])->name('totalbill.edit');
Route::get('totalbills/edit_search', [TotalbillController::class, 'edit_search'])->name('totalbill.edit_search');
Route::get('totalbills/search', [TotalbillController::class, 'search'])->name('totalbill.search');

Route::get('view_options', [View_optionController::class, 'index'])->name('view_option.index');
Route::get('view_options/edit', [View_optionController::class, 'edit'])->name('view_option.edit');

Route::get('zipcode', [ZipcodeController::class, 'index'])->name('zipcode.index');
Route::post('zipcode/update', [ZipcodeController::class, 'update'])->name('zipcode.update');
Route::get('zipcode/reset', [ZipcodeController::class, 'reset'])->name('zipcode.reset');

//route barang
Route::resource('barang', BarangController::class)->middleware('auth');
