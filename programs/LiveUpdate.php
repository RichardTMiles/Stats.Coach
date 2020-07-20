<?php


namespace Programs;

use CarbonPHP\interfaces\iCommand;


/* @author Richard Tyler Miles
 *
 *      This is close, but I think it doesnt need to live in this repo.
 */
class LiveUpdate implements iCommand
{

    public static function colorCode(string $message, string $color = 'green'): void
    {
        $failed = false;
        $buffer = '\e[1;';
        $colors = [
            'black' => '30',
            'red' => '31',
            'green' => '32',
            'yellow' => '33',
            'blue' => '34',
            'magenta' => '35',
            'cyan' => '36',
            'white' => '37',
            'background_black' => '40',
            'background_red' => '41',
            'background_green' => '42',
            'background_yellow' => '43',
            'background_blue' => '44',
            'background_magenta' => '45',
            'background_cyan' => '46',
            'background_white' => '47',
        ];

        if (!array_key_exists($color, $colors)) {
            $buffer .= $colors['red'] . 'mColor Coding Failed, invalid color.';
        } else {
            $buffer .= "$colors[$color]m    $message";
        }

        print shell_exec('echo "' . $buffer . '\e[0m"');

        if ($failed) {
            exit(1);
        }
    }

    public function __construct(array $CONFIG)
    {
        self::colorCode("Starting Live Update \n\n");
    }

    public function usage(): void
    {
        // TODO - improve documentation
        print 'Designed to be run as a web hook.';
    }

    public static function executeAndCheckStatus($command): void
    {
        $output = [];
        $return_var = null;
        self::colorCode('Running CMD >> ' . $command . PHP_EOL . PHP_EOL . ' ');
        exec($command, $output, $return_var);
        if ($return_var > 0) {
            self::colorCode("The command >>  $command \n\t returned with a status code (" . $return_var . '). ', 'red');
            $output = implode(PHP_EOL, $output);
            self::colorCode("\n\n\tCommand output::\n\n $output \n\n", 'cyan');
            exit(1);
        }
    }

    public function run($argv): void
    {
        self::executeAndCheckStatus('composer liveUpdate');
        self::colorCode("\n\n\tThe live update finished successfully!\n\n", 'yellow');
    }

    public function cleanUp($argv): void
    {
        // nothing
    }
}