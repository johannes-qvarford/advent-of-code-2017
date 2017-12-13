<?php

namespace JohannesQvarford\AdventOfCode2017\D3;

class Runner
{
    public static function run($sourceNumber) {
        $totalDistance = self::getDistanceToCenter($sourceNumber);
        $larger = self::getFirstLargerNumber($sourceNumber);
        printf("Source number was %d.%s", $sourceNumber, PHP_EOL);
        printf("Steps to walk is %d.%s", $totalDistance, PHP_EOL);
        printf("First value larger than source is %d.%s", $larger, PHP_EOL);
    }

    public static function getFirstLargerNumber($n) {
        // We will traverse the memory matrix procedurally.
        // we update our position (p), set the value (v) by reading surrounding squares of the memory (mem)
        //  and update our movement direction (off).
        // We change direction every number of steps (stepsLen).
        //  Every second time we change direction, we increase the amount of steps.
        //  We keep track of how many times we have walked in the same direction (curSteps).
        // We start at (0, 0)
        //  with 1 in the square
        //  walking right
        //  not having walked right before
        //  changing direction every step.
        // Every time we move, we check if the value on the square is larger than the given number (n).
        //  If it is, then that number is returned.

        $mem = [[1]];
        $p = [0, 0];
        $offs = [[1, 0], [0, 1], [-1, 0], [0, -1]];
        $stepsLen = 1;
        $curSteps = 0;
        $off = 0;

        $v = 1;
        while ($v <= $n) {
            $p = self::addPoints($p, $offs[$off]);
            $v = array_sum(self::getSurroundingSum($mem, $p));
            self::setValue($mem, $p, $v);

            $curSteps++;
            if ($curSteps === $stepsLen) {
                $curSteps = 0;
                $off = ($off + 1) % count($offs);
                if ($off % 2 === 0) {
                    $stepsLen++;
                }
            }
        }
        return $v;
    }

    private static function addPoints(array $p, array $q)
    {
        return [
            $p[0] + $q[0],
            $p[1] + $q[1]
        ];
    }

    private static function setValue(array &$mem, array $p, $v)
    {
        self::allocateSquare($mem, $p);
        $mem[$p[0]][$p[1]] = $v;
    }

    private static function getValue(array $mem, array $p)
    {
        return $mem[$p[0]][$p[1]];
    }

    private static function getSurroundingSum(array &$mem, array $p)
    {
        $squares = [
            [$p[0]+1, $p[1]],
            [$p[0]+1, $p[1]+1],
            [$p[0], $p[1]+1],
            [$p[0]-1, $p[1]+1],
            [$p[0]-1, $p[1]],
            [$p[0]-1, $p[1]-1],
            [$p[0], $p[1]-1],
            [$p[0]+1, $p[1]-1],
        ];
        return array_map(function($q) use ($mem) {
            self::allocateSquare($mem, $q);
            return self::getValue($mem, $q);
        }, $squares);
    }


    private static function allocateSquare(&$mem, $p)
    {
        if (!key_exists($p[0], $mem)) {
            $mem[$p[0]] = [];
        }
        if (!key_exists($p[1], $mem[$p[0]])) {
            $mem[$p[0]][$p[1]] = 0;
        }
    }

    public static function getDistanceToCenter($sourceNumber) {
        // find source layer by checking if smaller than layer*layer.
        // calculate distance to center of given side of layer.
        //  if layer is L, these are the centers:
        //  L*L-(L/2),
        //  L*L-((L/2+(L-1)),
        //  L*L-((L/2)+2(L-1)),
        //  L*L-((L/2)+3(L-1)),
        // add distance to the layer index, and that is the full distance.

        $n = $sourceNumber;
        $l = 1;
        while ($n > $l*$l) {
            $l += 2;
        }

        $bottomCenter = ($l*$l)-(floor($l/2));
        $stepsBetweenSides = $l - 1;
        $centers = [
            $bottomCenter,
            $bottomCenter - $stepsBetweenSides,
            $bottomCenter - (2 * $stepsBetweenSides),
            $bottomCenter - (3 * $stepsBetweenSides)
        ];
        $stepsToWalkOnEdge = abs($n - $centers[0]);
        foreach ($centers as $c) {
            $stepsToWalkOnEdge = min([abs($n - $c), $stepsToWalkOnEdge]);
        }

        $totalDistance = $stepsToWalkOnEdge + (($l - 1) / 2);
        return $totalDistance;
    }
}