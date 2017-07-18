<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/1/17
 * Time: 3:09 AM
 */


function beth ($string = "bitch") {
    print "Beth is a " . $string . PHP_EOL;
}

beth();

function kyle($thought = null)
{
    switch ($thought) {
        case "pissed":
            echo "Fuck ";
        case "sad":
            echo " Shit tits! ";
            break;
        case "happy":
            print " Love you ";
            break;
        default:
            echo " NO Fucks ";
    }

}

kyle( '' );

