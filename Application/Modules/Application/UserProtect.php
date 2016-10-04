<?php

namespace App\Modules\Application;

class UserProtect
{
    public function logged_in()
    {
        return (isset($_SESSION['id'])) ? true : false;
    }

    public function logged_in_protect()
    {   // Only on the Login and register static
        if ($this->logged_in() === true) {
            header( 'Location: index.php' );            // Set to index to reload the session
            exit();
        }
    }

    public function logged_out_protect()
    {            // A general protect for all main static
        if ($this->logged_in() === false) {
            header( 'Location: index.php' );            // Set to index to reload the session
            exit();
        }
    }

    public function logout()
    {
        session_destroy();
        header( 'Location:index.php' );
    }

}

