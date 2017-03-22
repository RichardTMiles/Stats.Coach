<?php

namespace Modules;

class StoreFiles
{
    private $FilePath;


    public function __construct($input = 'FileToUpload', $loc = 'Data/Uploads/', $multiple = false)
    {
        $this->FilePath = 'false';
        (!$multiple) ?
            $this->singleFile( $input, $loc ) :
            $this->multipleFiles();
    }

    public function __toString()
    {
        return $this->FilePath;
    }

    private function singleFile($input, $loc)
    {
        try {
            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (
                !isset($_FILES[$input]['error']) ||
                is_array( $_FILES[$input]['error'] )
            ) {
                throw new \RuntimeException( 'Invalid parameters.' );
            }

            // Check $_FILES['upfile']['error'] value.
            switch ($_FILES[$input]['error']) {
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
            if ($_FILES[$input]['size'] > 1000000) {
                throw new \RuntimeException( 'Exceeded filesize limit.' );
            }

            // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new \finfo( FILEINFO_MIME_TYPE );

            if (false === $ext = array_search(
                    $finfo->file( $_FILES[$input]['tmp_name'] ),
                    array(
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ),
                    true
                )
            ) {
                throw new \RuntimeException( 'Invalid file format.' );
            }

            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from server/user state data.
            $count = 0;
            do {
                $targetPath = SERVER_ROOT . $loc . $_SESSION['id'] . '_' . time() . '_' . $count++ . '.' . $ext;
            } while (file_exists( $targetPath ));


            if (!move_uploaded_file( $_FILES[$input]['tmp_name'], $targetPath )) {
                throw new \RuntimeException( 'Failed to move uploaded file.' );
            }

            $this->FilePath = $targetPath;

        } catch (\RuntimeException $e) {
            // echo $e->getMessage();
        }
    }

    public function multipleFiles()
    {
        // ToDo - make the multiple file uploads work....
        // when eventually needed
        return;
    }


}
