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


class Session implements \SessionHandlerInterface
{
    private $db;
    private $sessionVerified;           // we should check between each request for browsers and ip if both change logout
    private static $user_id;

    public function __construct()
    {
        #session_save_path( SERVER_ROOT . 'Data/Sessions' );   // Manually Set where the Users Session Data is stored
        #ini_set( 'session.gc_probability', 1 );               // Clear any lingering session data in default locations

        session_set_save_handler( $this, true);                // Comment this out to stop storing session on the server

        session_start();

        if (empty($_SESSION['id'])) $_SESSION['id'] = false;  // This will be the users account id found in [database].user.user_id

        static::$user_id = $_SESSION['id'];
    }


    public function open($savePath, $sessionName)
    {
        $this->db = Database::getConnection();
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
        if (empty(static::$user_id = $_SESSION['id'])) return false;
        $NewDateTime = date( 'Y-m-d H:i:s', strtotime( date( 'Y-m-d H:i:s' ) . ' + 1 day' ) );  // so from time of last write and whenever the gc_collector hits
        return ($this->db->prepare( 'REPLACE INTO StatsCoach.user_session SET session_id = ?, user_id = ?, Session_Expires = ?, Session_Data = ?' )->execute( [$id, static::$user_id, $NewDateTime, $data] )) ?
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
