<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Publish Status
    |--------------------------------------------------------------------------
    |
    | The publish status of collection entries and taxonomy terms
    | created by the factory.
    |
    | Tipp: Randomize the status by setting this to '(bool) random_int(0, 1)'.
    |
    */

    'published' => true,

    /*
    |--------------------------------------------------------------------------
    | Title Fallback Settings
    |--------------------------------------------------------------------------
    |
    | These title settings will function as a fallback to create titles for
    | your collection entries and taxonomy terms, if you didn't explicitly set
    | a 'title' field in the respective blueprint.
    |
    | 'chars': The character count of the title will be in this range.
    | 'real_text': Use real english words by setting this to 'true'.
    |
    */

    'title' => [
        'chars' => [$min = 10, $max = 20],
        'real_text' => false,
    ],

];