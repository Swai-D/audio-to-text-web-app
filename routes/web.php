<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TranscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Simple, non-ambiguous routes. Avoid duplicate route names and avoid
| caching routes while you have closures.
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome.page');

Route::get('/home', [TranscriptionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/transcribe', [TranscriptionController::class, 'index'])->name('transcribe');
    Route::post('/transcribe', [TranscriptionController::class, 'store'])->name('transcribe.store');
    Route::post('/transcribe/{transcript}/summarize', [TranscriptionController::class, 'summarize'])->name('transcribe.summarize');
    Route::get('/transcribe/{transcript}/pdf', [TranscriptionController::class, 'downloadPdf'])->name('transcribe.pdf');
    Route::get('/transcribe/{transcript}/docx', [TranscriptionController::class, 'downloadDocx'])->name('transcribe.docx');
    Route::delete('/transcribe/{transcript}', [TranscriptionController::class, 'destroy'])->name('transcribe.destroy');
    Route::get('/transcribe/{transcript}/edit', [TranscriptionController::class, 'edit'])->name('transcribe.edit');
    Route::put('/transcribe/{transcript}', [TranscriptionController::class, 'update'])->name('transcribe.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok'], Response::HTTP_OK);
});

require __DIR__.'/auth.php';
