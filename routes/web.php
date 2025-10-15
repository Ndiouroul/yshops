<?php
namespace App\Http\Controllers;
//use App\Http\Requests\StoreInscriptionRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', [AccueilController::class, 'ShowAccueil']) -> name('accueil');
Route::get('/accueilShops', [AccueilController::class, 'getShops']) -> name('accueil-get-shops');
Route::get('/boutique/{matricule}', [AccueilController::class, 'getOpenShops']) -> name('accueil-open-shops');

Route::get('/shopOpendetails/{matricule}', [ShopDetailsController::class, 'getOpenShopsDetails']) -> name('get-open-details-shops');
Route::post('/shopgetProducts', [ShopDetailsController::class, 'getProductsShopsDetails']) -> name('shopSearchProductsDetails');
Route::get('/shopAbonner/{matricule}', [ShopDetailsController::class, 'sabonner'])->middleware('auth') -> name('abonner');
Route::get('/shopVoter/{matricule}', [ShopDetailsController::class, 'voter'])->middleware('auth') -> name('voter');
Route::get('/shopLiker/{matricule}', [ShopDetailsController::class, 'liker'])->middleware('auth') -> name('liker');

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/view', [DashboardController::class, 'showDashboard'])->name('dashboard');
    Route::get('/dashboard/gettingEverything{matricule}/{thing}', [DashboardController::class, 'gettingEverything'])->name('dashboard-get-everything');
    Route::get('/dashboard/gettingAccountDetails', [DashboardController::class, 'gettingAccountDetails'])->name('dashboard-get-account-details');
    Route::get('/dashboard/gettingElementForUpdating{element}/{matricule}', [DashboardController::class, 'gettingElementForUpdating'])->name('getting-Element-For-Updating');

    Route::post('/product', [DashboardController::class, 'addNewProduct'])->middleware('gestionStock')->name('dashboard_new_product');
    Route::post('/ingredient', [DashboardController::class, 'addNewIngredient'])->middleware('gestionStock')->name('dashboard_new_ingredient');
    Route::post('/updatingEverything', [DashboardController::class, 'makingUpdateForEverything'])->middleware('gestionStock')->name('dashboard-element-updating');
    Route::post('/structure', [DashboardController::class, 'addNewStructure'])->name('dashboard_new_structure');
    Route::post('/fournisseur', [DashboardController::class, 'addNewDealer'])->name('dashboard_new_fournisseur');
    Route::post('/accountUpdating', [DashboardController::class, 'updateDetailsAccount'])->name('dashboard_update_accout_details');

    Route::get('/showProfil', [DashboardController::class, 'showProfil'])->name('get-profil');
    Route::get('/getStockShops', [DashboardController::class, 'getStockShops'])->middleware('gestionStock')->name('get-all-shops-stock');

    Route::get('/showShop', [DashboardController::class, 'showShop'])->middleware('gestionStock')->name('get-shop');
    Route::get('/bill-product/{shopname}', [DashboardController::class, 'getbillproducts'])->middleware('gestionStock')->name('get-bill-products');
    Route::get('/shops-stock-products/{shopname}', [DashboardController::class, 'getStockProducts'])->middleware('gestionStock')->name('get-all-shops-products');
    Route::get('/dealer/{shopname}', [DashboardController::class, 'getDealerList'])->middleware('gestionStock')->name('get-dealers');
    Route::post('/new-bill-product/', [DashboardController::class, 'addNewBill'])->middleware('gestionStock')->name('add-new-bill');
    Route::get('/getting-sold-data/{shopmatricule}', [DashboardController::class, 'gettingSoldData'])->middleware('gestionStock')->name('get-shop-sold-data');
    Route::get('/getting-sold-fees-data/{shopmatricule}', [DashboardController::class, 'gettingSoldFeesData'])->middleware('gestionStock')->name('get-shop-sold-data-fees');
    Route::post('/new-bill-product-transformation-structure/', [DashboardController::class, 'addNewBillTransformationStrucutre'])->middleware('gestionStock')->name('add-new-bill-transformation');
    Route::get('/showStock', [DashboardController::class, 'showStock'])->name('get-stock');
    Route::put('/editProfil', [DashboardController::class, 'updateProfil'])->name('edit-profil');
    Route::post('/check_password', [DashboardController::class, 'check_password'])->name('check-password');

    Route::post('dashboard/newManager', [DashboardController::class, 'addNewManager']) ->middleware('owner')->name('dashboard_new_manager');
    Route::post('dashboard/newseller', [DashboardController::class, 'addNewSeller']) ->middleware('manager')->name('dashboard_new_seller');
});

Route::get('/connexion', [ConnexionController::class, 'showConnexion']) -> name('login');
Route::post('/connexion', [ConnexionController::class, 'login']) -> name('store_connexion');
Route::get('/vente', [VenteController::class, 'showVente'])->middleware('shoper') -> name('vente');
Route::get('/vente/getDealer', [VenteController::class, 'getStructures'])->middleware('shoper') -> name('venteGetStructure');
Route::post('/vente/validCommande', [VenteController::class, 'validCommande']) -> name('route-valid-Commande');
Route::post('/vente/getProductCommande', [VenteController::class, 'getNewProduct']) -> name('search-Product-Commande');

Route::get('/deconnexion', [ConnexionController::class, 'logout']) -> name('deconnexion');

Route::get('/inscription', [InscriptionController::class, 'showInscription']) -> name('inscription');
Route::post('/inscription', [InscriptionController::class, 'inscription']) -> name('store_inscription');

Route::get('/shop', [ShopController::class, 'showShop'])->middleware('gestionStock') -> name('shop');
Route::get('/shopGetDetails', [ShopController::class, 'showShopDetails']) -> name('shopGetDetails');
Route::post('/shopsendDetailsCommande', [ShopController::class, 'validNewCommande'])->middleware('auth') -> name('shopSendCommande');

Route::get('/Update-profil', [ToupdateprofilController::class, 'ShowToupdateprofil'])-> name('toupdateprofil');


Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth', 'role'])->name('admin');
