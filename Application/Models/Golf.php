<?php

namespace App\Models;

use App\ApplicationModel as Model;
use App\Modules\Models\GolfRelay;
use App\Views\View;


class Golf extends Model
{
    // This should construct the values that are needed initially and push them back to the controller
    // 'index' will always be called.. lets change this to __construct and use the action var to specify more specific operations
    // this if the function runner.

    protected $GolfRelay;

    // Stats.Coach / $controller / $action / $parameter / $unique / $id /

    public function __construct($data)
    {
        $this->GolfRelay = new GolfRelay();
        parent::__construct( $data );
    }

    public function Golf()
    {
        extract( $this->data );

        $pageTitle = "Home";

        return new View( compact( array_keys( get_defined_vars() ) ) );
    }

    public function PostScore()
    {
        extract( $this->data );

        if (isset($parameter)) {
            $courses = $this->GolfRelay->courseByState( $parameter );
            if (empty($courses)) $courses[] = "Add Course";
        }

        return new View( compact( array_keys( get_defined_vars() ) ) );
    }
} 


