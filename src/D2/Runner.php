<?php

namespace JohannesQvarford\AdventOfCode2017\D2;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename)
    {
        $linesOfNumbers = self::readLinesOfNumbers($filename);
        $checksum = self::calculateChecksum($linesOfNumbers);
        $checksum2 = self::calculateChecksum2($linesOfNumbers);
        printf("The first checksum is %d.%s", $checksum, PHP_EOL);
        printf("The second checksum is %d.%s", $checksum2, PHP_EOL);
    }

    public static function calculateChecksum2($linesOfNumbers)
    {
        $checksum = 0;
        foreach ($linesOfNumbers as $numbers) {
            $count = count($numbers);

            for ($i = 0; $i < $count; ++$i) {
                $x = $numbers[$i];
                for ($j = 0; $j < $count; ++$j) {
                    $y = $numbers[$j];

                    if ($i !== $j && $x % $y === 0) {
                        $checksum += $x / $y;
                    }
                }
            }
        }
        return $checksum;
    }

    public static function calculateChecksum($linesOfNumbers)
    {
        $checksum = 0;
        foreach ($linesOfNumbers as $numbers) {
            $checksum += max($numbers) - min($numbers);
        }
        return $checksum;
    }

    public static function readLinesOfNumbers($filename)
    {
        $linesOfNumbers = [];
        foreach(Common::lines($filename) as $line) {
            // We have to trim the trailing newline.
            // Otherwise, $strings last element will be an empty string.
            $strings = preg_split("/\s/", rtrim($line));
            $numbers = array_map(function ($s) { return intval($s, 10); }, $strings);
            array_push($linesOfNumbers, $numbers);
        }
        return $linesOfNumbers;
    }
}