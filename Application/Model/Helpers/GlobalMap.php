<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/26/17
 * Time: 8:47 PM
 *
 * So I get really pissed when people yell at me for bad practice. I've
 * been working in PHP for many years now and as far as I can tell PHP
 * follows one pattern and one only. DO WHATEVER YOU WANT. Seriously, that
 * is, like, the whole goal of the language. If you attempt to follow other
 * practices based prior knowledge of other languages, then you probably
 * don't know enough about PHP. For example, did you know that PHP arrays
 * are actually HASH TABLES. This means that it is far faster to store user data
 * in an array than an object by calling new. Moreover private methods and variables
 * can be hijacked by any other class, or even the global scope, using
 * closure binding... So why are you still using private scopes?
 *
 * The above question has a right answer and if you do not know it off the top
 * of your noggin then you have no right to give me crap about the global scope.
 * .UGH. My rant isn't over. Every neat trick has a time and a place, but
 * knowing when to use them is the key to professional programming. You've probably
 * heard that the global scope is dreaded in practice because it makes development
 * difficult and hard to follow, and I would agree with this statement. I however
 * follow the MVC pattern religiously and never deviate by design. The MVC pattern is
 * by far IMHO the most fluent and robust structural pattern when done right. The
 * global scope in PHP is not hackable and does not persist after a connection is closed.
 * This makes it ideal for manipulating data that is only needed during the current request.
 *
 * @link http://php.net/manual/en/functions.arguments.php
 *
 * Passing large arrays between functions is not "slow" because of PHP's built in
 * compiler zero-copy optimisation. Editing an array that you've passed by value
 * costs near double the time complexity. Editing by reference is the most ideal.
 * Passing scopes by object injection also feels rather dumb. My reasoning:
 * If I know my class has a constraint it would seem intuitive to have it automatically
 * fetch the dependency. We assume most routes only require three or four files be loaded from
 * our application folder, we can capitalise on this known precedence.
 *
 * The global map should only be extended from the `Model` namespace. Variables
 * referenced here should be captured and stored by the serialized class. This
 * Is automatically done using the configuration option ['SESSION'][]
 * Models are only run if there are operations that must be done involving the
 * database. Entities grabs the database which is instanced so multiple connections
 * are not left open. To many instancing is also considered bad practice.
 * We gain two major features by allowing instancing:
 *      Cross class database rollbacks
 *      All methods and logic required are extended w/o extra time complexity
 *
 * @link http://carbonphp.com/
 */

namespace Model\Helpers;

use Carbon\Entities;
use Carbon\Helpers\Pipe;

abstract class GlobalMap extends Entities
{
    protected $user = array();
    
    public function __construct()
    {
        parent::__construct();
        global $user;
        $this->user = &$user;
    }

    public static function sendUpdate(string $id, string $uri){
        Pipe::send( $uri, SERVER_ROOT . 'Data/Temp/' . $id . '.fifo' );
    }

}