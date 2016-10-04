<?php

namespace App\Controllers;

use App\ApplicationController as Controller;

class Search extends Controller
{
    // This should construct the values that are needed initially and push them back to the controller
    // 'index' will always be called.. lets change this to __construct and use the action var to specify more specific operations
    // this if the function runner.

    public function search($data)
    {
        return new \App\Models\Search( $data );
    }
}