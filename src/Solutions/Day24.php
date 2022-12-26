<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;
use JetBrains\PhpStorm\Pure;

// Part 1 && 2 execution time is around 2 minutes

class Day24 implements SolutionInterface
{
    const SIGN_EMPTY = '.';
    const SIGN_WALL = '#';
    const DIRECTIONS = [
        '>' => [0, 1],
        'v' => [1, 0],
        '<' => [0, -1],
        '^' => [-1, 0],
    ];

    private string $inputPath;
    private array $map;
    private array $blizzards = [];
    private int $cycles = 0;

    private int $solutionPart2 = 0;

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

        $map = $this->map;
        $blizzards = $this->blizzards;

        $solution = $this->findPath($map, $blizzards);

        $this->solutionPart2 = $solution[1];

        return ['solution' => $solution[0], 'time' => $this->getExecutionTime($startTime)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        return ['solution' => $this->solutionPart2, 'time' => $this->getExecutionTime($startTime)];
    }

    private function findPath(array $map, array $blizzards): array
    {
        $this->cycles = 1;

        foreach ($blizzards as $blizzard) {
            $this->cycles = $this->lcm($this->cycles, $blizzard['cycle']);
        }

        $hasBlizzard = [];
        for ($step = 0; $step < $this->cycles; ++$step) {
            foreach ($blizzards as $blizzard) {
                [$dy, $dx] = self::DIRECTIONS[$blizzard['direction']] ?? [0, 0];
                $y = $blizzard['startY'] + $dy * (($step + $blizzard['startPos']) % $blizzard['cycle']);
                $x = $blizzard['startX'] + $dx * (($step + $blizzard['startPos']) % $blizzard['cycle']);
                $hash = $x | ($y << 16) | (($step % $this->cycles) << 32);
                $hasBlizzard[$hash] = true;
            }
        }

        $result1 = -1;
        $result2 = -1;

        $q = [[$map['startX'], $map['startY'], 0, 0]];
        $hash = $map['startX'] | ($map['startY'] << 16);
        $visited = [$hash => true];
        $readIdx = 0;

        while (true) {
            [$x, $y, $step, $phase] = $q[$readIdx];
            ++$readIdx;
            ++$step;

            foreach (self::DIRECTIONS as [$dy, $dx]) {
                $y1 = $y + $dy;
                $x1 = $x + $dx;

                if (($x1 < 0) or ($x1 >= $map['maxX']) or ($y1 < 0) or ($y1 >= $map['maxY'])) {
                    continue;
                }

                if ($map['layout'][$y1][$x1] == self::SIGN_WALL) {
                    continue;
                }

                $phase1 = $phase;

                if (($phase == 1) and ($x1 == $map['startX']) and ($y1 == $map['startY'])) {
                    $phase1 = $phase + 1;
                } elseif (($phase != 1) and ($x1 == $map['endX']) and ($y1 == $map['endY'])) {
                    if ($result1 < 0) {
                        $result1 = $step;
                    }
                    if ($phase == 2) {
                        $result2 = $step;
                        break 2;
                    }
                    $phase1 = $phase + 1;
                }

                $hash = $x1 | ($y1 << 16) | (($step % $this->cycles) << 32);
                $hashPhased = $hash | ($phase1 << 48);

                if (isset($hasBlizzard[$hash]) or isset($visited[$hashPhased])) {
                    continue;
                }

                $q[] = [$x1, $y1, $step, $phase1];

                $visited[$hashPhased] = true;
            }

            $phase1 = $phase;
            $hash = $x | ($y << 16) | (($step % $this->cycles) << 32);
            $hashPhased = $hash | ($phase1 << 48);

            if (isset($hasBlizzard[$hash]) or isset($visited[$hashPhased])) {
                continue;
            }

            $q[] = [$x, $y, $step, $phase1];

            $visited[$hashPhased] = true;
        }
        return [strval($result1), strval($result2)];
    }

    private function gcd(int $a, int $b): int
    {
        $a1 = max($a, $b);
        $b1 = min($a, $b);

        while ($b1 != 0) {
            $t = $b1;
            $b1 = $a1 % $b1;
            $a1 = $t;
        }

        return $a1;
    }

    #[Pure] private function lcm(int $a, int $b): int
    {
        return abs($a) * intdiv(abs($b), $this->gcd($a, $b));
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $y => $line) {
            $this->map['layout'][] = trim($line);
        }

        $this->map['maxY'] = count($this->map['layout']);
        $this->map['maxX'] = strlen($this->map['layout'][0] ?? '');
        $this->map['startY'] = 0;
        $this->map['startX'] = strpos($this->map['layout'][$this->map['startY']], self::SIGN_EMPTY);
        $this->map['endY'] = $this->map['maxY'] - 1;
        $this->map['endX'] = strpos($this->map['layout'][$this->map['endY']], self::SIGN_EMPTY);

        foreach ($this->map['layout'] as $y => $row) {
            for ($x = 0; $x < $this->map['maxX']; ++$x) {
                if ($row[$x] == self::SIGN_EMPTY || $row[$x] == self::SIGN_WALL) continue;

                [$dy, $dx] = self::DIRECTIONS[$row[$x]];

                $startY = $y;
                $startX = $x;
                $startPos = 0;

                while (true) {
                    $startY -= $dy;
                    $startX -= $dx;

                    if (($startY < 0) || ($startY >= $this->map['maxY']) || ($startX < 0) || ($startX >= $this->map['maxX'])) {
                        break;
                    }
                    if ($this->map['layout'][$startY][$startX] == self::SIGN_WALL) {
                        break;
                    }
                    ++$startPos;
                }

                $startY += $dy;
                $startX += $dx;
                $endY = $y;
                $endX = $x;
                $cycle = $startPos + 1;

                while (true) {
                    $endX += $dx;
                    $endY += $dy;
                    if (($endY < 0) or ($endY >= $this->map['maxY']) || ($endX < 0) or ($endX >= $this->map['maxX'])) {
                        break;
                    }
                    if ($this->map['layout'][$endY][$endX] == self::SIGN_WALL) {
                        break;
                    }

                    ++$cycle;
                }

                $this->blizzards[] = [
                    'direction' => $row[$x],
                    'startX' => $startX,
                    'startY' => $startY,
                    'startPos' => $startPos,
                    'cycle' => $cycle
                ];
            }
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dh %dm %ds', $diff->h, $diff->i, $diff->s);
    }
}
