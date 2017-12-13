<?php
namespace JohannesQvarford\AdventOfCode2017\D1;
use JohannesQvarford\AdventOfCode2017\Utility\Common;
class Runner
{
    public static function run($filename)
    {
        $contents = Common::getContents($filename);
        $digits = self::readDigitsFromString($contents);
        $pairsSum = self::sumOfSamePairs($digits);
        $oppositesSum = self::sumOfSameOpposites($digits);
        printf("Input was: %s.%s", $contents, PHP_EOL);
        printf("The sum of same pairs is %d.%s", $pairsSum, PHP_EOL);
        printf("The sum of same opposites is %d.%s", $oppositesSum, PHP_EOL);
    }

    public static function sumOfSamePairs(array $digits)
    {
        return self::sumOfSameNumbersByOffset($digits, 1);
    }

    public static function sumOfSameOpposites(array $digits)
    {
        return self::sumOfSameNumbersByOffset($digits, count($digits) / 2);
    }

    public static function sumOfSameNumbersByOffset(array $digits, $offset = 1)
    {
        $sum = 0;
        foreach ($digits as $cur => $curValue) {
            $other = ($cur + $offset) % count($digits);
            $otherValue = $digits[$other];
            if ($curValue === $otherValue) {
                $sum += $curValue;
            }
        }
        return $sum;
    }

    public static function readDigitsFromString($str)
    {
        $chars = str_split($str);
        $digits = array_map(function ($c) { return ord($c) - ord("0"); }, $chars);
        return $digits;
    }
}