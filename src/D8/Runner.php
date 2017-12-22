<?php
namespace JohannesQvarford\AdventOfCode2017\D8;

use JohannesQvarford\AdventOfCode2017\Utility\Common;

class Runner
{
    public static function run($filename) {
        $opLines = self::readOpLines($filename);
        $largestEver = -1;
        $largest = self::largestValueInRegisterAfterRunning($opLines, $largestEver);

        printf("Largest value in a register after running program: '%d'\n", $largest);
        printf("Largest values in a register during program execution: '%d'\n", $largestEver);
    }

    public static function largestValueInRegisterAfterRunning($opLines, &$largestEver) {
        $reg = self::initializeRegisters($opLines);
        $maxReg = self::initializeRegisters($opLines);
        self::runProgram($opLines, $reg, $maxReg);
        $largest = max(array_values($reg));
        $largestEver = max(array_values($maxReg));
        return $largest;
    }

    public static function initializeRegisters(array $opLines) {
        $reg = [];
        foreach ($opLines as $line) {
            $reg[$line->condition->a] = 0;
            $reg[$line->statement->a] = 0;
        }
        return $reg;
    }

    public static function runProgram(array $opLines, array &$reg, array &$maxReg) {
        foreach ($opLines as $line) {
            if (self::evaluateOp($line->condition, $reg, $maxReg)) {
                self::evaluateOp($line->statement, $reg, $maxReg);
            }
        }
    }

    public static function evaluateOp($op, array &$reg, array &$maxReg) {
        switch ($op->opCode) {
            case OpCode::INC:
                $reg[$op->a] += $op->b;
                $maxReg[$op->a] = max([$reg[$op->a], $maxReg[$op->a]]);
                return null;
            case OpCode::DEC:
                $reg[$op->a] -= $op->b;
                $maxReg[$op->a] = max([$reg[$op->a], $maxReg[$op->a]]);
                return null;
            case OpCode::GT:
                return $reg[$op->a] > $op->b;
            case OpCode::GE:
                return $reg[$op->a] >= $op->b;
            case OPCode::LT:
                return $reg[$op->a] < $op->b;
            case OpCode::LE:
                return $reg[$op->a] <= $op->b;
            case OpCode::EQ:
                return $reg[$op->a] === $op->b;
            case OpCode::NE:
                return $reg[$op->a] !== $op->b;
        }
    }

    public static function readOpLines($filename) {
        $opLines = [];
        foreach (Common::lines($filename) as $line) {
            $parts = explode(" ", $line);
            $opLine = new OpLine();

            $opLine->condition = new Op();
            $opLine->condition->opCode = OpCode::parse($parts[5]);
            $opLine->condition->a = $parts[4];
            $opLine->condition->b = intval($parts[6], 10);

            $opLine->statement = new Op();
            $opLine->statement->opCode = OpCode::parse($parts[1]);
            $opLine->statement->a = $parts[0];
            $opLine->statement->b = intval($parts[2], 10);

            array_push($opLines, $opLine);
        }
        return $opLines;
    }
}

class OpLine
{
    public $condition = null;
    public $statement = null;
}

class Op
{
    public $opCode = OpCode::INC;
    public $a = null;
    public $b = null;
}

class OpCode
{
    const INC = 0;
    const DEC = 1;
    const GT = 2;
    const GE = 3;
    const LT = 4;
    const LE = 5;
    const EQ = 6;
    const NE = 7;

    public static function parse($name) {
        $lower = strtolower($name);
        switch ($lower) {
            case "inc": return self::INC;
            case "dec": return self::DEC;
            case ">": return self::GT;
            case ">=": return self::GE;
            case "<": return self::LT;
            case "<=": return self::LE;
            case "==": return self::EQ;
            case "!=": return self::NE;
            default: throw new Error(sprintf("unknown opcode: '%s'", $name));
        }
    }
}