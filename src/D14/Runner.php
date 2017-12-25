<?php

namespace JohannesQvarford\AdventOfCode2017\D14;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($input)
    {
        $grid = self::grid($input);
        $bitGrid = self::gridToBitGrid($grid);
        $sum = 
            array_sum(
                array_map(function ($r) { 
                    return array_sum(
                        array_map(function ($hex) {
                            return self::getBitCount($hex);
                        }, $r));
                }, $grid));
        $regions = self::regions($bitGrid);
        printf("The number of used squares is: '%d'\n", $sum);
        printf("The number of regions is: '%d'\n", $regions);
    }

    public static function gridToBitGrid($grid)
    {
        return array_map(function ($row) {
            $m = array_map(function ($hex) {
                return [
                    ($hex & 0x80) >> 7,
                    ($hex & 0x40) >> 6,
                    ($hex & 0x20) >> 5,
                    ($hex & 0x10) >> 4,
                    ($hex & 0x08) >> 3,
                    ($hex & 0x04) >> 2,
                    ($hex & 0x02) >> 1,
                    ($hex & 0x01) >> 0,  
                ];
            }, $row);
            $n = array_reduce($m, function ($acc, $x) {
                return array_merge($acc, $x);
            }, []);
            return $n;
        }, $grid);
    }

    public static function regions($bitGrid) {
        $regions = [];
        for ($i = 0; $i < 128; ++$i) {
            $regions[$i] = [];
            for ($j = 0; $j < 128; ++$j) {
                $regions[$i][$j] = PHP_INT_MAX;
            }
        }

        $currentColor = 0;
        while(self::colorGrid($bitGrid, $regions, $currentColor)) {
        }

        $flatRegions = array_merge(...$regions);
        $distinctColors = array_unique($flatRegions);
        // ignore 0xFF color
        return count($distinctColors) - 1;
    }

    public static function colorGrid(array &$bitGrid, array &$regions, &$currentColor) {
        $didColor = false;
        for ($i = 0; $i < 128; ++$i) {
            for ($j = 0; $j < 128; ++$j) {
                // not occupied
                if ($bitGrid[$i][$j] === 0) {
                    continue;
                }

                $color = $regions[$i][$j];
                $neighbourColors = [
                    //self::colorAtPosition($regions, $i - 1, $j - 1),
                    self::colorAtPosition($regions, $i - 1, $j),
                    //self::colorAtPosition($regions, $i - 1, $j + 1),
                    self::colorAtPosition($regions, $i, $j - 1),
                    // self::colorAtPosition($regions, $i, $j),
                    self::colorAtPosition($regions, $i, $j + 1),
                    //self::colorAtPosition($regions, $i + 1, $j - 1),
                    self::colorAtPosition($regions, $i + 1, $j),
                    //self::colorAtPosition($regions, $i + 1, $j + 1)
                ];
                $lowestColor = min($neighbourColors);
                if ($color > $lowestColor) {
                    $didColor = true;
                    $regions[$i][$j] = $lowestColor;
                } else if ($color === PHP_INT_MAX) {
                    $didColor = true;
                    $regions[$i][$j] = $currentColor;
                    $currentColor++;
                }
            }
        }
        return $didColor;
    }

    public static function colorAtPosition(array &$regions, $i, $j) {
        
        return $i < 0 || $j < 0 || $i > 127 || $j > 127 ? PHP_INT_MAX
            : $regions[$i][$j];
    }

    public static function grid($input)
    {
        $grid = [];
        for ($i = 0; $i < 128; ++$i) {
            $grid[$i] = self::denseKnotHash(sprintf("%s-%d", $input, $i));
        }
        return $grid;
    }

    public static function getBitCount($value)
    {
        $count = 0;
        while($value) {
            $count += ($value & 1);
            $value = $value >> 1;
        }
        return $count;
    }

    /*
        128 hashes (calculated)
        128 rows
        bits of hash indicate free or used.
        hashofrow(x) = hash("input-{x}");
        each 1 bit in hashofrow(x) added to count
        how many?
    */
    public static function denseKnotHash($input)
    {
        $lengthsWithoutSuffix = self::readAsciiLengths($input);
        $lengths = array_merge($lengthsWithoutSuffix, [17, 31, 73, 47, 23]);
        $list = self::createList(256);
        
        $p = 0;
        $skip = 0;
        for ($i = 0; $i < 64; $i++) {
            self::knotHash($list, $lengths, $p, $skip);
        }

        $denseHashSize = (int)sqrt(256);
        $chunkSize = $denseHashSize;
        $chunks = array_chunk($list, $chunkSize);
        $denseHash = array_map(function ($chunk) {
            return array_reduce($chunk, function ($acc, $x) {
                return $acc ^ $x;
            });
        }, $chunks);
        return $denseHash;
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

    public static function createList($length = 256)
    {
        $list = [];
        for ($i = 0; $i < $length; ++$i) {
            $list[$i] = $i;
        }
        return $list;
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

    public static function readAsciiLengths($content)
    {
        $line = $content;
        $array = array_filter(str_split($line, 1), function ($x) { return $x !== ""; });
        $asciiLengths = array_map(function ($x) { return ord($x); }, $array);
        return $asciiLengths;
    }
}