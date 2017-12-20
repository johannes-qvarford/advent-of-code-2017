<?php
namespace JohannesQvarford\AdventOfCode2017\D4;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $wordLines = self::readWordLines($filename);
        $noDuplicatesCount = self::validWordLineCount($wordLines, function ($x) { 
            return self::wordLineHasNoDuplicates($x); 
        });
        $noPalindromesCount = self::validWordLineCount($wordLines, function ($x) {
            return self::wordLinesHasNoPalindromes($x);
        });

        printf("Number of no duplicate word lines: '%d'%s", $noDuplicatesCount, PHP_EOL);
        printf("Number of no palindrome word lines: '%d'%s", $noPalindromesCount, PHP_EOL);
    }

    public static function validWordLineCount(array $wordLines, $isValid) {
        $count = 0;
        foreach ($wordLines as $line) {
            if ($isValid($line)) {
                $count++;
            }
        }
        return $count;
    }

    public static function wordLineHasNoDuplicates(array $wordLine) {
        $unique = array_unique($wordLine);
        return count($unique) === count($wordLine);
    }

    public static function wordLinesHasNoPalindromes(array $wordLine) {
        for ($i = 0; $i < count($wordLine); ++$i) {
            $b1 = self::charCountBag($wordLine[$i]);
            for ($j = $i + 1; $j < count($wordLine); ++$j) {
                $b2 = self::charCountBag($wordLine[$j]);
                $aKeys = array_keys($b1);
                $bKeys = array_keys($b2);
                // if keys are the same.
                if (count(array_diff($aKeys, $bKeys)) === 0 && count(array_diff($bKeys, $aKeys)) === 0) {
                    $isPalindrome = true;
                    
                    // if counts of all chars match - that's a palindrome.
                    $isPalindrome = count(array_filter($aKeys, function ($k) use ($b1, $b2) {
                        return $b1[$k] === $b2[$k];
                    })) === count($aKeys);
                    
                    if ($isPalindrome) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function charCountBag($word) {
        // a char count bag is a hash which maps characters to the number of times they appear in a word.
        $chars = str_split($word);
        $bag = [];
        foreach ($chars as $c) {
            if (!isset($bag[$c])) {
                $bag[$c] = 0;
            }
            $bag[$c]++;
        }
        return $bag;
    }

    public static function readWordLines($filename) {
        $wordLines = [];
        foreach (Common::lines($filename) as $line) {
            $trim = trim($line);
            $ex = explode(" ", $trim);
            array_push($wordLines, $ex);
        }
        return $wordLines;
    }
}