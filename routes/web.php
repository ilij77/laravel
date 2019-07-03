<?php

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

use Illuminate\Support\Facades\Auth;

Route::get('/', 'HomeController@index')->name('home');


Auth::routes();
Route::get('/verify/{token}','Auth\RegisterController@verify')->name('register.verify');
Route::get('/cabinet','Cabinet\HomeController@index')->name('cabinet');

Route::group([
    'prefix'=>'adverts',
    'as'=>'adverts.',
    'namespace'=>'Adverts',
],function (){
    Route::get('/show/{advert}','AdvertController@show')->name('show');
    Route::post('/show/{advert}/phone','AdvertController@phone')->name('phone');
    Route::get('/all/{category?}','AdvertController@index')->name('index.all');
    Route::get('/{region?}/{category?}','AdvertController@index')->name('index');

    Route::post('/show/{advert}/favorites', 'FavoriteController@add')->name('favorites');
    Route::delete('/show/{advert}/favorites', 'FavoriteController@remove');

    Route::get('/{adverts_path?}', 'AdvertController@index')->name('index')->where('adverts_path', '.+');
});




Route::group(
    [ 'prefix'=>'cabinet',
        'as'=>'cabinet.',
        'namespace'=>'Cabinet',
        'middleware'=>['auth'],
    ],function ()
{
    Route::get('/','HomeController@index')->name('home');
    Route::group(['prefix'=>'profile', 'as'=>'profile.'],function ()
    {
        Route::get('/','ProfileController@index')->name('home');
        Route::get('/edit','ProfileController@edit')->name('edit');
        Route::put('/update','ProfileController@update')->name('update');
        Route::post('/phone','PhoneController@request');
        Route::get('/phone','PhoneController@form')->name('phone');
        Route::put('/phone','PhoneController@verify')->name('phone.verify');
        Route::post('/phone/auth','PhoneController@auth')->name('phone.auth');
    });

    Route::get('favorites', 'FavoriteController@index')->name('favorites.index');
    Route::delete('favorites/{advert}', 'FavoriteController@remove')->name('favorites.remove');

    Route::group([
        'prefix'=>'adverts',
        'as'=>'adverts.',
        'namespace'=>'Adverts',
        'middleware'=>[\App\Http\Middleware\FilledProfile::class],
    ],function(){
        Route::get('/','AdvertController@index')->name('index');
        Route::get('/create','CreateController@category')->name('create');
        Route::get('/create/region/{category}/{region?}','CreateController@region')->name('create.region');
        Route::get('/create/advert{category}/{region?}','CreateController@advert')->name('create.advert');
        Route::post('/create/advert/{category}/{region?}','CreateController@store')->name('create.advert.store');

        Route::get('/{advert}/edit','ManageController@edit')->name('edit');
        Route::put('/{advert}/edit','ManageController@update')->name('update');
        Route::get('/{advert}/photos','ManageController@photos')->name('photos');
        Route::post('/{advert}/photos','ManageController@photos');
        Route::post('/{advert}/send','ManageController@send')->name('send');
        Route::post('/{advert}/close', 'ManageController@close')->name('close');
        Route::delete('/{advert}/send','ManageController@destroy')->name('destroy');
    });
    Route::group([
        'prefix' => 'banners',
        'as' => 'banners.',
        'namespace' => 'Banners',
        'middleware' => [App\Http\Middleware\FilledProfile::class],
    ], function () {
        Route::get('/', 'BannerController@index')->name('index');
        Route::get('/create', 'CreateController@category')->name('create');
        Route::get('/create/region/{category}/{region?}', 'CreateController@region')->name('create.region');
        Route::get('/create/banner/{category}/{region?}', 'CreateController@banner')->name('create.banner');
        Route::post('/create/banner/{category}/{region?}', 'CreateController@store')->name('create.banner.store');

        Route::get('/show/{banner}', 'BannerController@show')->name('show');
        Route::get('/{banner}/edit', 'BannerController@editForm')->name('edit');
        Route::put('/{banner}/edit', 'BannerController@edit');
        Route::get('/{banner}/file', 'BannerController@fileForm')->name('file');
        Route::put('/{banner}/file', 'BannerController@file');
        Route::post('/{banner}/send', 'BannerController@send')->name('send');
        Route::post('/{banner}/cancel', 'BannerController@cancel')->name('cancel');
        Route::post('/{banner}/order', 'BannerController@order')->name('order');
        Route::delete('/{banner}/destroy', 'BannerController@destroy')->name('destroy');
    });

});



Route::group(
    [ 'prefix'=>'admin',
        'as'=>'admin.',
        'namespace'=>'Admin',
        'middleware'=>['auth','can:admin-panel'],
    ],
    function (){
        Route::get('/', 'HomeController@index')->name('home');
        Route::resource('users','UsersController');
        Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');
        Route::resource('regions','RegionController');

        Route::group(['prefix'=>'adverts','as'=>'adverts.','namespace'=>'Adverts'],function ()
        {
            Route::resource('categories','CategoryController');

            Route::group(['prefix'=>'categories/{category}', 'as'=>'categories.'],function (){
                Route::post('/first','CategoryController@first')->name('first');
                Route::post('/up','CategoryController@up')->name('up');
                Route::post('/down','CategoryController@down')->name('down');
                Route::post('/last','CategoryController@last')->name('last');

                Route::resource('attributes','AttributeController')->except('index');
            });

            Route::group(['prefix' => 'adverts', 'as' => 'adverts.'], function () {
                Route::get('/', 'AdvertController@index')->name('index');
                Route::get('/{advert}/edit', 'AdvertController@editForm')->name('edit');
                Route::put('/{advert}/edit', 'AdvertController@edit');
                Route::get('/{advert}/photos', 'AdvertController@photosForm')->name('photos');
                Route::post('/{advert}/photos', 'AdvertController@photos');
                Route::get('/{advert}/attributes', 'AdvertController@attributesForm')->name('attributes');
                Route::post('/{advert}/attributes', 'AdvertController@attributes');
                Route::post('/{advert}/moderate', 'AdvertController@moderate')->name('moderate');
                Route::get('/{advert}/reject', 'AdvertController@rejectForm')->name('reject');
                Route::post('/{advert}/reject', 'AdvertController@reject');
                Route::delete('/{advert}/destroy', 'AdvertController@destroy')->name('destroy');
            });
        });

        Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {
            Route::get('/', 'BannerController@index')->name('index');
            Route::get('/{banner}/show', 'BannerController@show')->name('show');
            Route::get('/{banner}/edit', 'BannerController@editForm')->name('edit');
            Route::put('/{banner}/edit', 'BannerController@edit');
            Route::post('/{banner}/moderate', 'BannerController@moderate')->name('moderate');
            Route::get('/{banner}/reject', 'BannerController@rejectForm')->name('reject');
            Route::post('/{banner}/reject', 'BannerController@reject');
            Route::post('/{banner}/pay', 'BannerController@pay')->name('pay');
            Route::delete('/{banner}/destroy', 'BannerController@destroy')->name('destroy');
        });


    });

