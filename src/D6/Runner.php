<?php
namespace JohannesQvarford\AdventOfCode2017\D6;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $banks = self::readMemoryBanks($filename);
        $restart = self::cyclesUntilLoopRestart($banks);
        $loop = self::cyclesInLoop($banks);

        printf("Cycles until loop restart: '%d'\n", $restart);
        printf("Cycles in loop: '%d'\n", $loop);
    }

    public static function cyclesInLoop($banks) {
        // we move the banks to the state where it loops, and then we check cycles until it restarts again.
        $restart = self::cyclesUntilLoopRestart($banks);
        self::advance($banks, $restart);
        
        $loop = self::cyclesUntilLoopRestart($banks);
        return $loop;
    }

    public static function cyclesUntilLoopRestart($banks) {
        $cache = [];
        $cycles = 0;
        // 215 to low
        $compressKey = self::compressBanks($banks);
        do {
            $cache[$compressKey] = 0;
            self::advance($banks, 1);
            $cycles++;
            $compressKey = self::compressBanks($banks);

        } while (!isset($cache[$compressKey]));
        return $cycles;
    }

    public static function advance(&$banks, $steps = 1) {
        for ($i = 0; $i < $steps; $i++) {
            $indexOfBiggestBank = array_reduce(array_keys($banks), function ($a, $b) use ($banks) {
                return $banks[$a] >= $banks[$b] ? $a : $b; 
            }, 0);

            $amount = $banks[$indexOfBiggestBank];
            $banks[$indexOfBiggestBank] = 0;
            for ($j = 0; $j < $amount; ++$j) {
                $target = ($j + $indexOfBiggestBank + 1) % count($banks);
                $banks[$target]++;
            }
        }
    }

    public static function compressBanks($banks) {
        $n = "";
        for ($i = 0; $i < count($banks); ++$i) {
            $n .= strval($banks[$i]) . "_";
        }
        return $n;
    }

    public static function readMemoryBanks($filename) {
        $s = Common::getContents($filename);
        $trimmed = trim($s);
        $split = preg_split("/\\s+/", $trimmed);
        $banks = array_map(function ($x) { return intval($x, 10); }, $split);
        return $banks;
    }
}