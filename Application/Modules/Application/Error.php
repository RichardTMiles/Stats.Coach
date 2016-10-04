<?php

namespace App\Modules\Application;

// TODO - Set Up Error Class

class Error
{

    public function __construct($data)
    {

        // Open the file to get existing content
        $current = file_get_contents( ERROR_LOG );
        // Append a new person to the file
        $current .= "<pre>" . var_dump( $data ) . "</pre>\n\n\n";

        // Write the contents back to the file
        file_put_contents( ERROR_LOG, $current );

        echo "Error Logging has been deployed.";
        die();

    }
}
