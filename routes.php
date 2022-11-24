<?php

Route::group(['prefix' => 'contenteditor'], function () {
    $pluginBase = "KosmosKosmos\BetterContentEditor\Controllers";
    Route::post('image/upload', $pluginBase.'\ImageController@uploadNew');
    Route::post('image/save', $pluginBase.'\ImageController@save');
    Route::post('uploader/image', $pluginBase.'\ImageController@imageUploaderUpload');

    // Additional styles route
    Route::get('styles', $pluginBase.'\AdditionalStylesController@render');
});
