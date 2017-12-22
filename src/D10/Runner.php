<?php
namespace JohannesQvarford\AdventOfCode2017\D10;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($subChallenge, $listSize, $filename) {
        if ($subChallenge === 1) {
            $lengths = self::readListOfLengths($filename);
            $list = self::createList($listSize);
            $hash = self::knotHash($list, $lengths);
    
            printf("The hash is: '%d'\n", $hash);
        } else {
            $lengthsWithoutSuffix = self::readAsciiLengths($filename);
            $lengths = array_merge($lengthsWithoutSuffix, [17, 31, 73, 47, 23]);
            $list = self::createList($listSize);
            
            $p = 0;
            $skip = 0;
            for ($i = 0; $i < 64; $i++) {
                self::knotHash($list, $lengths, $p, $skip);
            }
            // wrong 2bc9cc044957a0db3babd57ad9e8d8

            $denseHashSize = (int)sqrt($listSize);
            $chunkSize = $denseHashSize;
            $chunks = array_chunk($list, $chunkSize);
            $denseHash = array_map(function ($chunk) {
                return array_reduce($chunk, function ($acc, $x) {
                    return $acc ^ $x;
                });
            }, $chunks);
            $hexs = array_map(function ($n) {
                return sprintf("%02x", $n);
            }, $denseHash);
            $hash = array_reduce($hexs, function ($acc, $hex) {
                return $acc . $hex;
            }, "");

            printf("The complicated hash is: '%s'\n", $hash);
        }
    }


    public static function createList($length = 256)
    {
        $list = [];
        for ($i = 0; $i < $length; ++$i) {
            $list[$i] = $i;
        }
        return $list;
    }

    public static function knotHash(array &$list, array $lengths, &$p = 0, &$skip = 0)
    {
        foreach ($lengths as $length) {
            self::reverse($list, $p, $length);
            $p = ($p + $length + $skip) % count($list);
            $skip++;
        }

        return $list[0] * $list[1];
    }

    public static function reverse(array &$list, $p, $c)
    {
        // c = count
        $its = $c / 2;
        $m = count($list);
        for ($i = 0; $i < $its; ++$i) {
            $x = ($p + $i) % $m;
            $y = (($p + $c) - ($i + 1)) % $m;
            $temp = $list[$x];
            $list[$x] = $list[$y];
            $list[$y] = $temp;
        }
    }

    public static function readListOfLengths($filename)
    {
        $line = Common::getContents($filename);
        $strings = explode(",", $line);
        $lengths = array_map(function ($x) { return intval($x, 10); }, $strings);
        return $lengths;
    }

    public static function readAsciiLengths($filename) {
        $line = Common::getContents($filename);
        $array = array_filter(str_split($line, 1), function ($x) { return $x !== ""; });
        $asciiLengths = array_map(function ($x) { return ord($x); }, $array);
        return $asciiLengths;
    }
}