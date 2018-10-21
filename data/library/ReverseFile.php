<?php


$sql = file_get_contents('ChapterTitle.sql');

$sql = array_reverse(explode(PHP_EOL, $sql));

$num = \count($sql);

ob_start();

foreach ($sql as $value){
    print $value . PHP_EOL;
}
$sql = ob_get_clean();

file_put_contents('ChapterTitle.sql', $sql);
