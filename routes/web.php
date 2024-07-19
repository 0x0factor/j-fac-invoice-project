<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TotalbillController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\CustomerchargeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AdministerController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\PostcodeController;
use App\Http\Controllers\View_optionController;
use App\Http\Controllers\PersonalController;
use Illuminate\Support\Facades\Route;

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

Route::get('/home', [HomeController::class, 'index'])->name('home');


// Route::get('customers/select', [CustomerController::class, 'select'])->name('customers.select');
Route::get('customers/movetoindex', [CustomerController::class, 'movetoindexCustomers'])->name('customers.movetoindex');
// Route::get('customers/add', [CustomerController::class, 'addCustomers'])->name('customers.add');

Route::get('/customers', [CustomersController::class, "index"])->name('customers.index');
Route::get('/customers/select', [CustomersController::class,"select"])->name('customers.select');
Route::get('/customers/check/{customer_ID}', [CustomersController::class, "check"])->name('customers.check');
Route::get('/customers/add', [CustomersController::class,"add"])->name('customers.add');
Route::post('/customers/add', [CustomersController::class ,'add'])->name('customers.save');
Route::get('/customers/edit/{customer_ID}', [CustomersController::class, 'edit'])->name('customers.edit');
Route::post('/customers/edit/{customer_ID}', [CustomersController::class,'edit'])->name('customers.update');



Route::get('quotes/index', [QuoteController::class, 'index'])->name('quotes.index');
Route::get('quotes/add', [QuoteController::class, 'add'])->name('quote.add');
Route::get('quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
Route::get('quotes/export', [QuoteController::class, 'export'])->name('quotes.export');
Route::get('quotes/action', [QuoteController::class, 'action'])->name('quotes.action');


Route::get('bills/index', [BillController::class, 'index'])->name('bills.index');
Route::get('bills/add', [BillController::class, 'addBills'])->name('bills.add');
Route::get('bills/index', [BillController::class, 'index'])->name('bills.index');

Route::get('totalbills/movetoindex', [TotalbillController::class, 'movetoindexTotalbills'])->name('totalbills.movetoindex');
Route::get('totalbills/add', [TotalbillController::class, 'addTotaltills'])->name('totalbills.add');


Route::get('deliveries/movetoindex', [DeliveryController::class, 'movetoindexDeliveries'])->name('deliveries.movetoindex');
Route::get('deliveries/add', [DeliveryController::class, 'addDeliveries'])->name('deliveries.add');
Route::get('deliveries/index', [DeliveryController::class, 'index'])->name('deliveries.index');

Route::get('charges/movetoindex', [ChargeController::class, 'movetoindexCharges'])->name('charges.movetoindex');
Route::get('charges/add', [ChargeController::class, 'addCharges'])->name('charges.add');

Route::get('items/movetoindex', [ItemController::class, 'movetoindexItems'])->name('items.movetoindex');
Route::get('items/add', [ItemController::class, 'addItems'])->name('items.add');


Route::get('mails/index', [MailController::class, 'index'])->name('mails.index');
Route::get('customercharges/index', [CustomerchargeController::class, 'index'])->name('customer_charges.index');
Route::get('companies/index', [CompanyController::class, 'index'])->name('companies.index');
Route::get('coverpages/index', [CoverpageController::class, 'index'])->name('coverpages.index');
Route::get('administers/movetoindex', [AdministerController::class, 'movetoindexAdministers'])->name('administers.movetoindex');
Route::get('histories/movetoindex', [HistoryController::class, 'movetoindexHistories'])->name('histories.movetoindex');
Route::get('configurations/index', [ConfigurationController::class, 'index'])->name('configurations.index');
Route::get('postcode/index', [PostcodeController::class, 'index'])->name('postcode.index');
Route::get('view_options/index', [View_optionController::class, 'index'])->name('view_options.index');
Route::get('personals/passEdit', [PersonalController::class, 'passEditPersonals'])->name('personals.passEdit');



//route barang
Route::resource('/barang', BarangController::class)->middleware('auth');
