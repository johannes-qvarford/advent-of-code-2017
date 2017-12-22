<?php
namespace JohannesQvarford\AdventOfCode2017\D9;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $stream = Common::getContents($filename);
        $p = 0;
        $nonCancelledGarbage = 0;
        $score = self::scoreForStream($stream, $p, 1, $nonCancelledGarbage);
        printf("Score for stream is: '%d'\n", $score);
        printf("Non cancelled garbage: '%d'\n", $nonCancelledGarbage);
    }

    public static function scoreForStream($stream, &$p, $depth = 1, &$nonCancelledGarbage) {
        $score = 0;
        $inGarbage = false;
        while ($p < strlen($stream)) {
            if ($inGarbage) {
                switch ($stream[$p]) {
                    case "!": $p++; break;
                    case ">": $inGarbage = false; break;
                    default: $nonCancelledGarbage++; break;
                }
            } else {
                switch ($stream[$p]) {
                    case "!":
                        $p++;
                        break;
                    case "<":
                        $inGarbage = true;
                        break;
                    case "}":
                        // recursive call ends on the } character;
                        return $score;
                    case "{":
                        $p++;
                        $score += $depth;
                        // recursive call start inside group.
                        $score += self::scoreForStream($stream, $p, $depth + 1, $nonCancelledGarbage);
                        break; 
                }
            }
            $p++;
        }
        return $score;
    }
}