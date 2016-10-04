<?php

namespace App\Views;

/* The function View is auto loaded on the initial view class call.
    the view class will only point to the 'current-master' template
            Which in this case is the class AdminLTE
*/

class View
{
    public function __construct($data)
    {
        // Do we actually need to call the view? If its not a .tpl.php file then no.
        if (isset($data['view']) && file_exists( SITE_STRIPPED . "users/" . $data["view"] . ".php" ) == true) {
            extract( $data );
            require SITE_STRIPPED . "users/" . $data["view"] . ".php";
            exit();
        }
        return new AdminLTE( $data );  // This is how we set our master template
    }
}
