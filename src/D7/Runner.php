<?php
namespace JohannesQvarford\AdventOfCode2017\D7;
use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $infos = self::readInformations($filename);
        $bottom = self::nameOfBottomProgram($infos);
        $weight = self::correctWeightOfFaultyDisk($infos);
        printf("The name of the bottom program is: '%s'\n", $bottom);
        printf("The correct weight of the faulty disk: '%d'\n", $weight);
    }

    public static function correctWeightOfFaultyDisk(array $infos) {
        foreach (array_keys($infos) as $name) {
            self::assignCorrectWeightToDisk($infos, $name);
        }

        $suspectName = self::nameOfBottomProgram($infos);

        $visitsLeft = [new Visit($infos[$suspectName], $infos[$suspectName]->totalWeight)];

        while (!empty($visitsLeft)) {
            $visit = array_pop($visitsLeft);
            $suspect = $visit->suspect;
            $expectedWeight = $visit->expectedWeight;

            $cs = array_map(function ($x) use ($infos) {
                return $infos[$x];
            }, $suspect->children);
            $ws = array_map(function ($x){ return $x->totalWeight; }, $cs);

            // is the tower a leaf node? leaf nodes are guilty if their weight isn't what's expected.
            if (empty($cs)) {
                if ($expectedWeight !== $suspect->weight) {
                    return  ($expectedWeight - $suspect->totalWeight) + $suspect->weight;
                }
                continue;
            }

            // regular child tower weight.
            // If one of the two first elements has an odd value,
            // we know that the third is good because only one tower is faulty.
            $w = $ws[0] !== $ws[1] ? $ws[2] : $ws[0];

            // are all towers balanced?
            $allSameWeight = array_reduce($ws, function ($acc, $x) use ($w) {
                return $acc && $x === $w;
            }, true);

            // if the towers are balanced, and the weight doesn't match, we got our faulty disk.
            if ($allSameWeight && $expectedWeight !== $suspect->totalWeight) {
                return ($expectedWeight - $suspect->totalWeight) + $suspect->weight;
            }

            // If the suspect is cleared, one of the child towers may hide the faulty disk.
            foreach ($cs as $c) {
                array_push($visitsLeft, new Visit($c, $w));
            }
        }
    }

    public static function assignCorrectWeightToDisk(array &$infos, $name) {
        // We set the total weight depth first, because the nodes closer to the root need the weights of nodes further down.
        // We also employ simplistic memoization.
        if ($infos[$name]->totalWeight !== 0) {
            return;
        }

        $infos[$name]->totalWeight = $infos[$name]->weight;
        foreach ($infos[$name]->children as $child) {
            self::assignCorrectWeightToDisk($infos, $child);
            $infos[$name]->totalWeight += $infos[$child]->totalWeight;
        }
    }

    public static function nameOfBottomProgram(array $infos) {
        $hasParent = [];

        foreach ($infos as $name => $info) {
            foreach ($info->children as $child) {
                $hasParent[$child] = true;
            }
        }

        $filtered = array_filter(array_keys($infos), function ($x) use ($hasParent) {
            return !isset($hasParent[$x]);
        });

        return array_values($filtered)[0];
    }

    public static function readInformations($filename) {
        $infos = [];
        foreach (Common::lines($filename) as $line) {
            
            $trim = trim($line);
            $mainParts = explode(" (", $trim);
            $name = $mainParts[0];
            $subParts = explode(")", $mainParts[1]);
            $weight = intval($subParts[0], 10);
            $children = [];
            if ($subParts[1] !== "") {
                $noArrow = str_replace(" -> ", "", $subParts[1]);
                $children = explode(", ", $noArrow);
            }

            $info = new Info();
            $info->name = $name;
            $info->weight = $weight;
            $info->children = $children;
            $infos[$name] = $info;
        }
        return $infos;
    }

    
}

class Visit
{
    public function __construct($suspect, $expectedWeight) {
        $this->suspect = $suspect;
        $this->expectedWeight = $expectedWeight;
    }
    public $disk = null;
    public $expectedWeight = 0;
}

class Info 
{
    public $name = "";
    public $weight = 0;
    public $totalWeight = 0;
    public $children = [];
};