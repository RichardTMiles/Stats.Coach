<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 11/13/17
 * Time: 8:56 AM
 *
 * You can execute by typing the following in terminal.
 * php main.php
 *
 * Data was taken from Barbers Hill Middle School South.
 * Contact: Chylle Miles, Girls Athletic Coordinator
 *
 *
 * Overview of Information Received
 *  612 subs per week. We have 7 schools and subs are needed for teachers and Aides.
 *
 *  Daily Teacher Rates
 *  Certified - $105.00
 *  Degreed/Non-Certified - $95.00
 *  Non-Degreed (60+ college hrs) - $90.00
 *  Daily Instructional Aide Rate (non college required)
 *  No College - $70.00
 *  Substitute teachers do not get overtime.
 */


// for loops on arrays are `foreach` and may also be denoted foreach ($array as $value)
// if the keys are negligible. Normally I use syntactically small grammar and show variations of the php language
// Excluding the $key in
//      foreach ($array as $key => $value)
// is slower to process by the php 7 interpreter. It is returned as value


$min = function ($argv = false) {
    static $min = false;
    return (!$argv ? $min : $min = (!!$min && $min < $argv ? $min : $argv));
};

$max = function ($argv = false) {
    static $max = false;
    return (!$argv ? $max : $max = (!!$max && $max > $argv ? $max : $argv));
};

if ($argc < 2) {
    print "You may add a `Custom Frequency` by passing arguments to script\n ie >> \t php main.php 302 984 290 670 ...\n\n";

    for ($i = 0; $i <= 30; $i++)
        $subsRequested[] = $j = rand(200, 1000) and $min($j) and $max($j);

    for (; $i <= 40; $i++)
        $subsRequested[] = $j = rand(550, 850) and $min($j) and $max($j);

    $max = $max();      // max no longer holds a function but an int
    $min = $min() - 1;  // We want our array to start at 1, so our histogram requires a lower range

    $argc = count($subsRequested);
    print "Pay rate for substitutes :: ";
    $payRateSubs = readline();                          // 70
    print "Pay rate for teachers' overtime :: ";
    $Certified = readline();                            // 105

} else {
    print "Pay rate for substitutes :: ";
    $payRateSubs = readline();
    print "Pay rate for teachers' overtime :: ";
    $Certified = readline();

    array_shift($argv);
    $subsRequested = $argv;
    $max = max($subsRequested);
    $min = min($subsRequested);
    $argc = count($subsRequested);
}

print "Max =>$max\nMin =>$min\n\n";

print "Requesting Frequency => [ \n\t";
foreach ($subsRequested as $key => $value)
    print $value . ", ";
print "\n];\n\n";

// build histogram
$Histogram = [];
$HistogramLevels = ($max - $min) / 10;                                                  // 10 intervals
foreach ($subsRequested as $key => $value) {
    $i = (int)ceil(($value - $min) / $HistogramLevels);
    $Histogram[$i] = ($Histogram[$i] ?? 0) + 1;
}

$CumulativePercentage = 0;
print "Cumulative Percentage => [ \n\t";
for ($i = 1; $i <= 10; $i++)
    print ($Histogram[$i] = (int)ceil(($CumulativePercentage += ($Histogram[$i] ?? 0)) / $argc * 100)) . ', ';      // Cumulative %. ", ";
print "\n];\n\n";

asort($Histogram);

$minimum = 1000000;
$position = null;
$subsRequested = function () use ($Histogram, $min, $max, $HistogramLevels) {
    $i = 10;
    do {
        if ($Histogram[$i] <= ($j = rand($min, $max)))
            return $j + rand(0, $HistogramLevels);
    } while ($i--);
};

###########################         This here

$step = 1;
do {
    switch ($step++) {
        default:
        case 1:
            $start = $min;
            $end = $max;
            $increment = 50;
            $runs = 10000;
            break;
        case 2:
            $start = $position - 50;
            $end = $position + 50;
            $increment = 10;
            $runs = 100000;
            break;
        case 3:
            $start = $position - 10;
            $end = $position + 10;
            $increment = 1;
            $runs = 1000000;
            break;

    }

    for ($PoolSize = $start; $PoolSize < $end; $PoolSize += $increment) {
        $totalCost = 0;
        $minCost = 1000000;

        for ($LargeSample = 0; $LargeSample < $runs; $LargeSample++) {
            $x = $subsRequested();

            $totalCost += (($payRateSubs * $PoolSize) +
                (($x < $PoolSize) ? 0 : (($x - $PoolSize) * $Certified)));

            if ($minCost > $totalCost) {
                $minCost = $totalCost;
            }
            if ($minCost < $minimum) {
                $minimum = $minCost;
                $position = $PoolSize;
            }
        }
        $totalCost /= $runs;

        print "Subs => $PoolSize \t Average Cost => $totalCost\t Min Cost => $minCost\n";
    }
    print "\nMinimum Cost => $minimum evaluated at $position\n\n";


} while ($step < 4);