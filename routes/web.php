<?php

use App\Models\Content;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('/checkout', [\App\Http\Controllers\Actions\CheckoutAction::class, 'index'])
    ->middleware('auth')->name('checkout');

Route::post('/checkout/charge', [\App\Http\Controllers\Actions\CheckoutAction::class, 'charge'])
    ->middleware('auth')->name('checkout.charge');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';


Route::get('/my-content', function (Content $content) {

    $contents = $content->whereHas('videos', fn($query) => $query->whereNotNull('code')
        ->whereisProcessed(true))
        ->get()->groupBy('type');

    return inertia('MyContent', compact('contents'));
})->middleware(['auth', 'verified', 'check.user.is.subscribed'])->name('my_contents');

Route::prefix('media')->name('media.')->middleware('auth', 'can:access-admin')->group(function () {
    Route::resource(
        '/contents',
        \App\Http\Controllers\Media\ContentController::class
    );

    Route::get(
        '/contents/{content}/videos/upload',
        [\App\Http\Controllers\Media\VideoController::class, 'upload']
    )
        ->name('contents.videos.upload');

    Route::post(
        '/contents/{content}/videos',
        [\App\Http\Controllers\Media\VideoController::class, 'store']
    )
        ->name('contents.videos.store');

    Route::post('/contents/{content}/videos/{video}/process', [\App\Http\Controllers\Media\VideoController::class, 'process'])
        ->name('contents.videos.upload.process');
    Route::delete('/videos/{video}', [\App\Http\Controllers\Media\VideoController::class, 'destroy'])
        ->name('contents.videos.destroy');

    Route::match(['PUT', 'PATCH'], '/contents/{content}/videos/{video}', [\App\Http\Controllers\Media\VideoController::class, 'update'])
        ->name('contents.videos.update');
});


Route::get('/watch/{content:slug}', \App\Http\Controllers\Actions\PlayerAction::class)
    ->middleware(['auth'])
    ->name('video.player');


Route::get('resources/{code}/{video}', function ($code, $video = null) {
    $video = $code . '/' . $video;

    return \Illuminate\Support\Facades\Storage::disk('videos_processed')
        ->response(
            $video,
            null,
            [
                'Content-Type' => 'application/x-mpegURL',
                'isHls' => true
            ]
        );
})->name('stream_player')->middleware(['auth']);
