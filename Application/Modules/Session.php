<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/27/17
 * Time: 10:26 PM
 *
 *
 * http://php.net/manual/en/function.session-set-save-handler.php
 *
 */

namespace Modules;


use Model\Helpers\GlobalMap;
use Tables\Users;
use Modules\Helpers\Serialized;

class Session extends GlobalMap implements \SessionHandlerInterface
{
    #private $sessionVerified;                                  // we should check between each request for browsers and ip if both change logout
    private static $user_id;


    public function __construct()
    {
        parent::__construct();  // Start the Database

        session_save_path( SERVER_ROOT . 'Data/Sessions' );   // Manually Set where the Users Session Data is stored

        ini_set( 'session.gc_probability', 1 );  // Clear any lingering session data in default locations

        session_set_save_handler( $this, true );                // Comment this out to stop storing session on the server

        if (SOCKET) $this->verifySocket();

        session_start();

        // More cache control is given in the .htaccess File
        Request::setHeader( 'Cache-Control: must-revalidate' );

        // Pull theses from session, and store on shutdown
        Serialized::start( 'user', 'team', 'course', 'tournaments' );

    }


    static function update()
    {
        if (static::$user_id = $_SESSION['id'] = ($_SESSION['id'] ?? false)) {

            $_SESSION['X_PJAX_Version'] = 'v' . SITE_VERSION . 'u' . $_SESSION['id']; // force reload occurs when X_PJAX_Version changes between requests

            Request::setHeader( 'X-PJAX-Version: ' . $_SESSION['X_PJAX_Version'] );

            #if ($user[$_SESSION['id']] ?? false)
            global $user;

            $user[$_SESSION['id']] = Users::sport(Users::all( \stdClass::class, $_SESSION['id'] ), $_SESSION['id']);

        } else $_SESSION['X_PJAX_Version'] = SITE_VERSION;

        /* If the session variable changes from the constant we will
         * send the full html page and notify the pjax js to reload
         * everything
         * */

        if (!isset( $_SESSION['X_PJAX_Version'] )) $_SESSION['X_PJAX_Version'] = SITE_VERSION;

        if (!defined( 'X_PJAX_VERSION' ))
            define( 'X_PJAX_VERSION', $_SESSION['X_PJAX_Version'] );

        Request::setHeader( "X-PJAX-Version: " . $_SESSION['X_PJAX_Version'] );

        Request::sendHeaders();  // Send any stored headers

    }

    private function verifySocket()
    {
        $sql = "SELECT session_id FROM StatsCoach.user_session WHERE user_ip = ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$_SERVER['REMOTE_ADDR']] );
        $session = $stmt->fetchColumn();
        if (empty( $session )) {
            if (SOCKET) echo "BAD ADDRESS :: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
            exit( 0 );
        }
        session_id( $session );
    }

    public function open($savePath, $sessionName)
    {
        if (!isset( $this->db )) $this->db = Database::getConnection();
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $stmt = $this->db->prepare( 'SELECT session_data FROM StatsCoach.user_session WHERE user_session.session_id = ?' );
        $stmt->execute( [$id] );
        return $stmt->fetchColumn() ?: '';
    }

    public function write($id, $data)
    {
        if (!$this->db instanceof Database)
            $this->db = Database::getConnection();
        // for security we don't store information processed from the sockets
        if (SOCKET || empty( static::$user_id = $_SESSION['id'] )) return true;     // must be true for php 7.0
        $NewDateTime = date( 'Y-m-d H:i:s', strtotime( date( 'Y-m-d H:i:s' ) . ' + 1 day' ) );  // so from time of last write and whenever the gc_collector hits
        return ($this->db->prepare( 'REPLACE INTO StatsCoach.user_session SET session_id = ?, user_id = ?, StatsCoach.user_session.user_ip = ?,  Session_Expires = ?, Session_Data = ?' )->execute( [$id, static::$user_id, $_SERVER['REMOTE_ADDR'], $NewDateTime, $data] )) ?
            true : false;
    }

    public function destroy($id)
    {
        return ($this->db->prepare( 'DELETE FROM StatsCoach.user_session WHERE user_id = ?' )->execute( [self::$user_id] )) ?
            true : false;
    }

    public function gc($maxLife)
    {
        return ($this->db->prepare( 'DELETE FROM StatsCoach.user_session WHERE (UNIX_TIMESTAMP(Session_Expires) + ? ) < ?' )->execute( [$maxLife, $maxLife] )) ?
            true : false;
    }
}
