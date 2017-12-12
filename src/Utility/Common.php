<?php
namespace JohannesQvarford\AdventOfCode2017\Utility;

class Common
{
    /**
     * Flatten a multidimentional array to a single dimensional array.
     * @param array $array The array to flatten.
     * @return array The flattened array.
     */
    public static function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    /**
     * Generate a sequence of lines from a given file.
     * @param string $filename The filename of the file to read lines from.
     * @return \Generator The generator for the sequence of lines.
     * @throws \Exception Thrown if the file could not be opened.
     */
    public static function lines($filename) {
        $f = fopen($filename, "r");
        if ($f) {
            while (($line = fgets($f)) !== false) {
                yield $line;
            }
        } else {
            throw new \Exception(sprintf("Could not open file '%s' for reading.", $filename));
        }
        fclose($f);
    }


}