<?php

// Modified from
// http://php.net/manual/en/features.file-upload.php

namespace Modules\Helpers;

use Modules\Helpers\Reporting\PublicAlert;

class StoreFiles
{
    public static function singleFile($fileArray, $location)
    {
        try {

            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (!isset($fileArray['error']) || is_array( $fileArray['error'] ))
                throw new \RuntimeException( 'Invalid parameters.' );

            // Check $_FILES['upfile']['error'] value.
            switch ($fileArray['error']) {
                case UPLOAD_ERR_OK:             // We hope
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new \RuntimeException( 'No file sent.' );
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new \RuntimeException( 'Exceeded filesize limit.' );
                default:
                    throw new \RuntimeException( 'Unknown errors.' );
            }

            // You should also check file size here.
            if ($fileArray['size'] > 1000000)
                throw new \RuntimeException( 'Exceeded filesize limit.' );


            // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new \finfo( FILEINFO_MIME_TYPE );

            if (false === $ext = array_search( $finfo->file( $fileArray['tmp_name'] ),
                    array(
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',),
                    true )) throw new \RuntimeException( 'Invalid file format.' );


            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from server/user state data.
            $count = 0;

            do {
                $relative = $location . $_SESSION['id'] . '_' . time() . '_' . $count++ . '.' . $ext;
                $targetPath = SERVER_ROOT . $relative;
            } while (file_exists( $targetPath ));


            if (!move_uploaded_file( $fileArray['tmp_name'], $targetPath ))
                throw new \RuntimeException( 'Failed to move uploaded file.' );

            return $relative;

        } catch (\RuntimeException $e) {
            PublicAlert::warning( $e->getMessage() );
            return false;
        }
    }
}
