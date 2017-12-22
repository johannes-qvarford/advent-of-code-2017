<?php
namespace JohannesQvarford\AdventOfCode2017\D11;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $steps = self::readSteps($filename);
        $from = self::positionAfterWalking($steps);
        $to = new Point(0, 0);
        $shortest = self::findShortestPath($from, $to);
        $furthest = self::furthestAwayFromHome($from, $steps);
        printf("The shortest path is '%d' steps.\n", $shortest);
        printf("The child was at most '%d' steps away from home.\n", $furthest);
    }

    public static function furthestAwayFromHome($from, array $steps) {
        $maxDistance = 0;
        $position = $from;
        foreach ($steps as $step) {
            $position = $position->add($step);
            $maxDistance = max([$maxDistance, self::findShortestPath($from, $position)]);
        }
        return $maxDistance;
    }

    public static function findShortestPath($from, $to) {
        // we first try to walk diagonally until we are vertically aligned with our target position.
        // we then try to walk up or down.
        // If we could have reach our destination simply by walking diagonally, we don't walk vertically at all.
        // otherwise, when walking diagonally, we walk upwards or downwards depending on which puts us closer to our destination,
        // then we walk the remaining distance vertically, 2 units at a time.
        $vx = abs($from->x - $to->x);
        $vy = abs($from->y - $to->y);
        $vy = max([0, (($vy - $vx) / 2)]);
        return $vx + $vy;
    }

    public static function positionAfterWalking($steps) {
        return array_reduce($steps, function ($acc, $cur) {
            return $acc->add($cur);
        }, new Point(0, 0));
    }

    public static function readSteps($filename) {
        $contents = Common::getContents($filename);
        $directions = explode(",", $contents);
        $steps = array_map(function ($direction) {
            switch ($direction) {
                case "s": return new Point(0, -2);
                case "n": return new Point(0, 2);
                case "sw": return new Point(-1, -1);
                case "se": return new Point(1, -1);
                case "nw": return new Point(-1, 1);
                case "ne": return new Point(1, 1);
                default: throw new Error(sprintf("Unexpected direction: '%s'\n", $direction));
            }
        }, $directions);
        return $steps;
    }
}

class Visit
{
    public $point = null;
    public $cost = -1;

    public function __construct($point, $cost) {
        $this->point = $point;
        $this->cost = $cost;
    }
}

class Point
{
    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public $x = 0;
    public $y = 0;

    public function add($p) {
        return new Point($this->x + $p->x, $this->y + $p->y);
    }

    public function toKey() {
        return sprintf("%d,%d", $this->x, $this->y);
    }
}