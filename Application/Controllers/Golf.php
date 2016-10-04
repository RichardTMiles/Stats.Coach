<?php
namespace App\Controllers;

use App\Models\Golf as GolfModel;
use App\ApplicationController as Controller;

class Golf extends Controller
{

    protected function Golf()
    {
        return new GolfModel( $this->data );
    }

    protected function PostScore()
    {
        extract( $this->data );
        return new GolfModel( compact( array_keys( get_defined_vars() ) ) );
    }

}
