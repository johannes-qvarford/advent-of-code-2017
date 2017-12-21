<?php
namespace JohannesQvarford\AdventOfCode2017\D5;
use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $offsets = self::readOffsets($filename);
        $stepsToReachExit = self::stepsToReachExit($offsets, function ($x) {
            return 1;
        });
        $stepsToReachStrangeExit = self::stepsToReachExit($offsets, function ($x) {
            return $x >= 3 ? -1 : 1;
        });

        printf("Steps to reach exit: '%d'%s", $stepsToReachExit, PHP_EOL);
        printf("Steps to reach exit with strange jumps: '%d'%s", $stepsToReachStrangeExit, PHP_EOL);
    }

    public static function stepsToReachExit(array $offsets, $getDifference) {
        $position = 0;
        $steps = 0;
        while ($position >= 0 && $position < count($offsets)) {
            $toJump = $offsets[$position];
            // Since the hard challenge takes a really long time to complete,
            // it would probably be worth it to copy this method and inline the respective getDifference lambdas.
            $offsets[$position] += $getDifference($toJump);
            $position += $toJump;
            $steps++;
        }
        return $steps;
    }

    public static function readOffsets($filename) {
        $offsets = [];
        foreach (Common::lines($filename) as $line) {
            array_push($offsets, intval($line, 10));
        }
        return $offsets;
    }
}