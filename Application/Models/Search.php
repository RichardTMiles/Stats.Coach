<?php
namespace App\Models;

use App\Modules\Models\UserRelay;
use App\Views\View;


class Search
{

    public $UserRelay;

    public function __construct($data)
    {
        extract( $data );
        $this->UserRelay = new UserRelay();
        extract( $this->UserRelay->profileData( $_SESSION['id'] ) ); // Array Will all data TODO - A user basic info function
        $pageTitle = "Search";


        return new View( compact( array_keys( get_defined_vars() ) ) );

    }
}
