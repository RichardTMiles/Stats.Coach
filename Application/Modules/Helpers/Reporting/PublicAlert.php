<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/16/17
 * Time: 5:19 PM
 */

namespace Modules\Helpers\Reporting;


/**
 * Class PublicAlert
 * @package Modules\Helpers\Reporting
 * 
 * danger
 * warning
 * info
 * success
 *
 */


class PublicAlert extends CustomException {

    private static function alert($message, $code) {
        if ($code != 'success' && $code != 'info') $message .= ' Contact us if problem persists.';
        $GLOBALS['alert'][$code] = $message;
    }

    public function __construct($message = null, $code = 'warning')
    {
        if (!empty($message)) static::alert( $message, $code );
        parent::__construct($message, 0);
    }

    public function __call($code = null, $message)
    {
        static::alert( $message[0], $code );
    }

    public static function __callStatic($code = null, $message)
    {
        static::alert( $message[0], $code );
    }


}
