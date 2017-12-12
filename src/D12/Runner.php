<?php
namespace JohannesQvarford\AdventOfCode2017\D12;

use \JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename)
    {
        $pipesForApps = self::readPipesForApps($filename);
        $memberCount = self::memberCountInGroupContaining0($pipesForApps);
        $groupCount = self::groupCount($pipesForApps);

        echo sprintf("There are %d members in the group containing app 0.%s", $memberCount, PHP_EOL);
        echo sprintf("There are %d groups.%s", $groupCount, PHP_EOL);
    }

    private static function memberCountInGroupContaining0(array $pipesForApps)
    {
        $visited = self::membersInGroupContainingMember($pipesForApps, 0);
        return count($visited);
    }

    private static function groupCount(array $pipesForApps)
    {
        $groupCount = 0;
        $unvisited = array_keys($pipesForApps);
        while (!empty($unvisited)) {
            $groupCount++;
            $start = $unvisited[0];
            $newVisited = self::membersInGroupContainingMember($pipesForApps, $start);
            # We need to remove holes with array_values here, otherwise the expression $unvisited[0] may not reference
            # an existing element.
            $unvisited = array_values(array_diff($unvisited, $newVisited));
        }
        return $groupCount;
    }

    private static function membersInGroupContainingMember(array $pipesForApps, $member)
    {
        $visited = [];
        $toVisit = [$member];

        while (!empty($toVisit)) {
            $newToVisit = Common::flatten(array_map(function ($x) use($pipesForApps) {
                    return $pipesForApps[$x];
                }, $toVisit));
            $newToVisitExceptVisited = array_diff($newToVisit, $visited);
            $visited = array_unique(array_merge($visited, $newToVisitExceptVisited));
            $toVisit = $newToVisitExceptVisited;
        }
        return $visited;
    }


    private static function readPipesForApps($filename)
    {
        /** @var $pipesForApps
        maps numbers to sets of numbers.
         */
        $pipesForApps = array();

        foreach (Common::lines($filename) as $line) {
            $line = preg_replace('/\s+/', '', $line);
            if ($line === "") {
                continue;
            }
            $parts = explode("<->", $line);

            $app = intval($parts[0], 10);

            $pipes = explode(",", $parts[1]);
            $pipes = array_map(function ($x) { return intval($x, 10); }, $pipes);

            $pipesForApps[$app] = $pipes;
        }
        return $pipesForApps;
    }
}
