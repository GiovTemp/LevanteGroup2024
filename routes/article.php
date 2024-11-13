<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;



Route::get('/', [ArticleController::class,'index'])->name('articles.index');

Route::get('/search',[HomeController::class, 'searchArticle'])->name('article.search');

Route::get('/show/{article}', [ArticleController::class,'show'])->name('articles.show');

Route::get('/byCategory/{category}', [ArticleController::class,'byCategory'])->name('articles.byCategory');
Route::get('/byTag/{tag}', [ArticleController::class,'byTag'])->name('articles.byTag');
Route::get('/byAuthor/{user}', [ArticleController::class,'byUser'])->name('articles.byUser');