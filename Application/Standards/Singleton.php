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
            throw new \Exception( 'Bad Unserialize in Singleton' );
        }

        // Start a new instance of the class and pass any arguments
        $class = new \ReflectionClass( get_called_class() );
        self::$getInstance = $class->newInstanceArgs( func_get_args() );
        return self::$getInstance;
    }
    public static function clearInstance()
    {
        if (!empty(self::$getInstance))
            return self::$getInstance = null;
    }

    public function __call($methodName, $arguments = array())
    {
        return $this->Skeleton( $methodName, $arguments );
    }

    private function Skeleton($methodName, $arguments = array())
    {
        // Have we used addMethod() to override an existing method
        if (key_exists( $methodName, $this->methods ))
            return (null === ($result = call_user_func_array( $this->methods[$methodName], $arguments )) ?
                $this :
                $result);

        // Is the method in the current scope ( public, protected, private ).
        // Note declaiming the method as private is the only way to ensure single instancing
        if (method_exists($this , $methodName)) {
            return (null === ($result = call_user_func_array( array($this, $methodName), $arguments )) ?
                $this : $result);
        }
        

        if (key_exists( 'closures', $GLOBALS ) && key_exists( $methodName, $GLOBALS['closures'] )) {
            $function = $GLOBALS['closures'][$methodName];
            $this->addMethod( $methodName, $function );
            return (null === ($result = call_user_func_array( $this->methods[$methodName], $arguments )) ?
                $this : $result);
        }
        throw new \Exception( "There is valid method or closure with the given name '$methodName' to call" );
    }

    private function addMethod($name, $closure)
    {
        if (is_callable( $closure )):
            $this->methods[$name] = \Closure::bind( $closure, $this, get_called_class() );
        else: // Nested to ensure Singleton returns the correct value of self
            throw new \Exception( "New Method Must Be A Valid Closure" );
        endif;
    }

    public function __sleep()
    {
        return 0;   // This will stop serialization automatically
    }


    public function __destruct()
    {
        // We require a sleep function to be set manually for singleton to manage utilization
        if ($this->__sleep() !== 0) $_SESSION[__CLASS__] = serialize( $this );
        else if(array_key_exists( __CLASS__, $_SESSION )) unset($_SESSION[__CLASS__]);
    }

    public function &__get($variable)
    {
        if (array_key_exists( $variable, $GLOBALS ))
            return $GLOBALS[$variable];

        throw new \Exception( "The variable `$variable` was not defined in the global scope");
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
        if (empty($value)) 
            $value = $this->storage;
        $this->$name = $value;
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
