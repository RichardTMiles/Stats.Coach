<?php

namespace Psr;

// Singleton

trait Singleton
{
    protected static $getInstance;  // A Temporary variable for 'quick data'
    protected $methods = array();   // Anonymous Function Declaration
    private $storage;               // Instance of the Container

    public static function __callStatic($methodName, $arguments = array())
    {
        return self::getInstance()->Skeleton( $methodName, $arguments );
    }

    public static function newInstance()
    {
        // Start a new instance of the class and pass any arguments
        $class = new \ReflectionClass( get_called_class() );
        self::$getInstance = $class->newInstanceArgs( func_get_args() );
        return self::$getInstance;
    }

    public static function getInstance()
    {   // see if the class has already been called this run
        if (!empty(self::$getInstance))
            return self::$getInstance;
        $calledClass = get_called_class();
        // check if the object has been sterilized in the session
        if (array_key_exists( $calledClass, $_SESSION )) {
            // This will invoke the __wake up operator
            if (is_object( self::$getInstance = unserialize( $_SESSION[$calledClass] ) ))
                return self::$getInstance;
            throw new \Exception( 'Unserialize failed on ' . $calledClass . ' using Singleton' );
        } // Start a new instance of the class and pass any arguments
        $class = new \ReflectionClass( get_called_class() );
        self::$getInstance = $class->newInstanceArgs( func_get_args() );
        return self::$getInstance;
    }

    /**
     * @param null $object
     * @return object|null
     */
    public static function clearInstance($object = null)
    {
        self::$getInstance = (is_object( $object ) ? $object : null);
        if (array_key_exists( __CLASS__, $_SESSION ))
            unset($_SESSION[__CLASS__]);
        return self::$getInstance;
    }


    public function __call($methodName, $arguments = array())
    {
        return $this->Skeleton( $methodName, $arguments );
    }

    private function Skeleton($methodName, $arguments = array())
    {
        // Have we used addMethod() to override an existing method
        if (key_exists( $methodName, $this->methods ))
            return (null === ($result = call_user_func_array( $this->methods[$methodName], $arguments )) ? $this : $result);
        // Is the method in the current scope ( public, protected, private ).
        // Note declaring the method as private is the only way to ensure single instancing
        if (method_exists( $this, $methodName )) {
            return (null === ($result = call_user_func_array( array($this, $methodName), $arguments )) ? $this : $result);
        }
        if (key_exists( 'closures', $GLOBALS ) && key_exists( $methodName, $GLOBALS['closures'] )) {
            $function = $GLOBALS['closures'][$methodName];
            $this->addMethod( $methodName, $function );
            return (null === ($result = call_user_func_array( $this->methods[$methodName], $arguments )) ? $this : $result);
        } throw new \Exception( "There is valid method or closure with the given name '$methodName' to call" );
    }

    private function addMethod($name, $closure)
    {
        if (is_callable( $closure )):
            $this->methods[$name] = \Closure::bind( $closure, $this, get_called_class() );
        else: // Nested to ensure Singleton returns the correct value of self
            throw new \Exception( "New Method Must Be A Valid Closure" );
        endif;
    }

    public function __wakeup()
    {
        if (method_exists( $this, '__construct' )) self::__construct();
        $object = get_object_vars( $this );
        foreach ($object as $item => $value)    // TODO - were really going to try and objectify everything?
            if(is_object( $temp = @unserialize($this->$item)))
                $this->$item = $temp;
    }

    // for auto class serialization add: const Singleton = true; to calling class
    public function __sleep()
    {
        if (!defined( 'self::Singleton' ) || !self::Singleton) return null;
        $object = get_object_vars( $this );
        foreach ($object as $key => $value) {
            if (is_object( $value )) {
                if (!method_exists( $value, '__sleep' )) continue;
                try { $this->$key = @serialize( $this->$key );
                } catch (\Exception $e){ continue; }                // Database object we need to catch the error thrown.
            } if (empty($value) || empty($this->$key)) continue;    // The object could be null from serialization?
            $onlyKeys[] = $key;
        } return (isset($onlyKeys) ? $onlyKeys : null);
    }

    public function __destruct()
    {   // We require a sleep function to be set manually for singleton to manage utilization
        if ($this->__sleep() != null) $_SESSION[__CLASS__] = serialize( $this );
        elseif (array_key_exists( __CLASS__, $_SESSION ))
            unset($_SESSION[__CLASS__]);
    }

    // The rest of the methods are for the sake of methods
    public function &__get($variable)
    {
        return $GLOBALS[$variable];
    }

    public function __set($variable, $value)
    {
        $GLOBALS[$variable] = $value;
    }

    public function __isset($variable)
    {
        return array_key_exists( $variable, $GLOBALS );
    }

    public function __unset($variable)
    {
        unset($GLOBALS[$variable]);
    }

    public function __invoke()
    {
        return $this->storage;
    }

    private function set($name, $value = null)
    {
        $this->storage = null;
        if ($value == null) {
            if (is_array( $name )) $this->storage = $name;
            else $this->storage[] = $name;
        } else $this->storage[$name] = $value;
        return $this;
    }

    private function get($variable = null)
    {
        return ($variable == null ?
            $this->storage :
            $this->{$variable});
    }

    private function has($variable)
    {
        return isset($this->$variable);
    }
}
