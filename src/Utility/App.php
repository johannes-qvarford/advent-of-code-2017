<?php
namespace JohannesQvarford\AdventOfCode2017\Utility;

use JohannesQvarford\AdventOfCode2017\D1;
use JohannesQvarford\AdventOfCode2017\D2;
use JohannesQvarford\AdventOfCode2017\D12;
use JohannesQvarford\AdventOfCode2017\D13;


class App
{
    public static function run($argc, $argv)
    {
        error_reporting(E_ALL);
        set_error_handler(function ($severity, $message, $file, $line) { App::exceptionErrorHandler($severity, $message, $file, $line); });

        $challenges = array(
            1 => function() use ($argv) { D1\Runner::run($argv[2]); },
            2 => function() use ($argv) { D2\Runner::run($argv[2]); },
            12 => function() use ($argv) { D12\Runner::run($argv[2]); },
            13 => function() use ($argv) { D13\Runner::run($argv[2]); },
        );

        $challengeId = $argc > 1 ? $argv[1] : "";

        # intval returns 0 in case of error. Luckily, none of our challenges uses that id.
        $idAsInt = intval($challengeId, 10);

        if (!key_exists($idAsInt, $challenges)) {
            fwrite(STDERR, sprintf("Unexpected challenge id '%s'. Expected a number between 1 and 24.", $argv[1]));
            exit(1);
        }

        $challenges[$idAsInt]();
    }

    private static function exceptionErrorHandler($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
}