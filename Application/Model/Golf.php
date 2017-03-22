<?php

namespace Model;


use Model\Helpers\GolfRelay;
use Psr\Singleton;

class Golf
{
    use Singleton;

    private $request;

    public function __construct()
    {
        $this->request = new GolfRelay();
    }


    public function Golf()
    {
        
    }

    public function PostScore($state = false)
    {
        if ($state != false) $this->courses = $this->request->courseByState($state);
    }

    public function AddCourse()
    {


        

    }


}



