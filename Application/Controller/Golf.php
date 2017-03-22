<?php
namespace Controller;


class Golf
{

    public function Golf()
    {

    }

    public function PostScore()
    {
        sortDump($_POST);

        if (!empty($_POST)) {
            sortDump($_POST);
            die();

        }

    }

    public function AddCourse()
    {
        if (!empty($_POST)) {
               sortDump( $_POST );
            die();
        }
    }


}
