<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// Part 1 execution time is around 43 seconds
// Part 2 execution time is around 43 minutes xD

class Day25 implements SolutionInterface
{
    const DIVIDER = 5;
    const SNAFU_NUMBERS = '=-012';
    const SNAFU_VALUES = ['=' => -2, '-' => -1, '0' => 0, '1' => 1, '2' => 2];

    private string $inputPath;
    private array $data;

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
        $this->prepareData();
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle1(): ?array
    {
        $startTime = new \DateTime();

        $solution = 0;

        $values = [];
        foreach($this->data as $snafu){
            $values[] = $this->snafuToValues($snafu);
        }

        $value = array_sum($values);

        $solution = $this->valueToSnafu($value);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        $solution = 0;

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function snafuToValues(string $snafu): int
    {
        $result = 0;
        $divider = 1;
        for ($i = strlen($snafu) - 1; $i >= 0; --$i) {
            $result += $divider * self::SNAFU_VALUES[$snafu[$i]];
            $divider *= self::DIVIDER;
        }
        return $result;
    }

    private function valueToSnafu(int $value): string
    {
        if ($value == 0) return '0';

        $v = abs($value);
        $result = '';
        $halfBase = intdiv(self::DIVIDER - 1, 2);

        while ($v != 0) {
            $r = ($v + $halfBase) % self::DIVIDER;
            $result .= self::SNAFU_NUMBERS[$r];

            if ($r < $halfBase) {
                $v += self::DIVIDER;
            }

            $v = intdiv($v, self::DIVIDER);
        }

        if ($value < 0) {
            $result = strtr($result, self::SNAFU_NUMBERS, strrev(self::SNAFU_NUMBERS));
        }

        return strrev($result);
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $y => $line) {
            $line = trim($line);

            $this->data[] = $line;
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dh %dm %ds', $diff->h, $diff->i, $diff->s);
    }
}
