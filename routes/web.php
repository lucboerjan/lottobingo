<?php
use Illuminate\Support\Facades\Route;

// TaalController
use App\Http\Controllers\TaalController;

Route::controller(TaalController::class)->group(function () {
    Route::get('/taal/{taal?}', 'zetTaal');
});

//AppController
use App\Http\Controllers\AppController;

Route::controller(AppController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/home', 'index');
    Route::get('/spelers', 'spelers');
    Route::get('/trekkingen', 'trekkingen');    
    Route::post('/jxSetVisit', 'jxSetVisit');    
});

//SpelerController
use App\Http\Controllers\SpelerController;

Route::controller(SpelerController::class)->group(function () {
    Route::post('/jxSpelersOverzicht', 'jxSpelersOverzicht');
    Route::post('/jxSpelerGet', 'jxSpelerGet');
    Route::post('/jxBetalingToevoegen', 'jxBetalingToevoegen');
    Route::post('/jxBetalingVerwijderen', 'jxBetalingVerwijderen');
});

//TrekkingController
use App\Http\Controllers\TrekkingController;

Route::controller(TrekkingController::class)->group(function () {
    Route::post('/jxTrekkingenOverzicht', 'jxTrekkingenOverzicht');
    Route::post('/jxTrekkingZoekBoodschappen', 'jxTrekkingZoekBoodschappen');
    Route::post('/jxTrekkingGet', 'jxTrekkingGet');
    Route::post('/jxTrekkingSet', 'jxTrekkingSet');
    Route::post('/jxSendEmail', 'jxSendEmail');
    Route::post('/jxUitbetalingWinnaars', 'jxUitbetalingWinnaars');
});

//LottobingoController
use App\Http\Controllers\LottobingoController;

Route::controller(LottobingoController::class)->group(function () {
    Route::post('/jxLottobingoOverzicht', 'jxLottobingoOverzicht');
    Route::post('/jxLottobingoBoodschappen', 'jxLottobingoBoodschappen');
    Route::post('/jxGetActieveReeksen', 'jxGetActieveReeksen');
    Route::post('/jxUitbetalingen', 'jxUitbetalingen');
});

Auth::routes();