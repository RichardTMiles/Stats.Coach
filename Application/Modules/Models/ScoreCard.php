<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 8/23/16
 * Time: 3:01 PM
 */

namespace App\Modules\Models;


class ScoreCard
{
    public function validEntry()
    {
        // q logic
        // we mush check which rows are filled/ should be counted valid
        // we must see if those rows have every input
        // we must store those rows into an app array.
        // each row will be a different row in our bd as well
        // some even dif tables

        for ($i = 1; $i <= 18; $i++) {
            if (empty($_POST["par_$i"]) || empty($_POST["tee_1_$i"]))
                throw new \Exception( 'B' );
        }

        if ($_POST["tee_2_$i"] != "none") {

        }

        if ($_POST["tee_3_$i"] != "none") {

        }

        if ($_POST["tee_4_$i"] != "none") {

        }

        if ($_POST["tee_5_$i"] != "none") {

        }

        if ($_POST["hc_1_type"] != "none") {

        }

    }

}