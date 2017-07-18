<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 6/1/17
 * Time: 2:48 AM
 */

$hope = 5;

echo $hope . PHP_EOL;

$hope *= 5;

echo $hope . PHP_EOL;

$closure = function ($x) use ($hope) {
  return 8 * $hope * $x;
};

$hope = 1;


echo $hope . "\n" . $closure(9) . PHP_EOL . "\n";


echo 5 * 5 * 8 * 9;



for($i = 0; $i <= 18; $i++)
  echo $i . "\t";


$i =0;
echo PHP_EOL;

while ($i != 20) echo ++$i . " \n";


$i =0;
echo PHP_EOL;

do echo ++$i . " \n";
while ($i != 20);



