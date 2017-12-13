<?php
namespace JohannesQvarford\AdventOfCode2017\D13;
use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename)
    {
        /** @var array scanners
         * [int => int] array which maps layer index to depth.
         */
        $scanners = self::readScanners($filename);
        /** @var integer severity
         */
        $severity = self::calculateSeverity($scanners);

        /** @var integer delay
         */
        $delay = self:: calculateDelayForNoDetection($scanners);

        echo sprintf("Severity is %d.%s", $severity, PHP_EOL);
        echo sprintf("Delay to wait for no detection is %d.%s", $delay, PHP_EOL);
    }

    public static function calculateDelayForNoDetection(array $scanners)
    {
        $delay = 0;
        while (self::getsCaught($scanners, $delay)) {
            $delay++;
        }
        return $delay;
    }

    public static function getsCaught($scanners, $delay = 0) {
        foreach ($scanners as $i => $value) {
            $rotation = ($value - 1) * 2;
            // example: if a scanner has a depth of 4, it takes 2*(4-1) picoseconds for it to return
            // to the original position.
            $caught = ($i + $delay) % $rotation === 0;
            if ($caught) {
                return true;
            }
        }
        return false;
    }

    public static function calculateSeverity($scanners, $delay = 0)
    {
        $severity = 0;
        foreach ($scanners as $i => $value) {
            $rotation = ($value - 1) * 2;
            // example: if a scanner has a depth of 4, it takes 2*(4-1) picoseconds for it to return
            // to the original position.
            $severity += ($i + $delay) % $rotation === 0 ? $i * $value : 0;
        }
        return $severity;
    }

    public static function readScanners($filename)
    {
        $scanners = [];
        foreach (Common::lines($filename) as $line) {
            $parts = explode(": ", $line);
            $layer = intval($parts[0], 10);
            $depth = intval($parts[1], 10);
            $scanners[$layer] = $depth;
        }
        return $scanners;
    }
}