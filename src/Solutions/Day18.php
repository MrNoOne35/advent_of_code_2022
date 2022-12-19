<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day18 implements SolutionInterface
{
    const DIRECTIONS = [
        [0, 0, 1],
        [0, 0, -1],
        [0, 1, 0],
        [0, -1, 0],
        [1, 0, 0],
        [-1, 0, 0],
    ];

    private array $cubes;
    private array $ranges;
    private array $memory;
    private string $inputPath;

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

        foreach($this->cubes as $cube){
            $solution += $this->countSides($cube);
        }

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

        foreach($this->cubes as $cube){
            $solution += $this->countSides($cube, true);
        }

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function countSides(array $cube, bool $recurse = false){
        $visible = 0;

        foreach (self::DIRECTIONS as [$dirX, $dirY, $dirZ]) {
            $x = $cube[0] + $dirX;
            $y = $cube[1] + $dirY;
            $z = $cube[2] + $dirZ;

            if (in_array([$x, $y, $z], $this->cubes)) {
                continue;
            }

            if ($recurse && !$this->isTrapped([$x, $y, $z])) {
                continue;
            }

            $visible++;
        }

        return $visible;
    }

    private function isTrapped($cube): bool
    {
        $key = sprintf('%d-%d-%d', $cube[0], $cube[1], $cube[2]);
        if (isset($this->memory[$key])) {
            return $this->memory[$key];
        }

        $queue = new \SplQueue();
        $queue->enqueue($cube);
        $visited = [$key];
        $trapped = false;

        while ($queue->count() > 0) {
            $cube = $queue->dequeue();

            if ($this->isOutOfRange($cube)) {
                $trapped = true;
                break;
            }

            foreach (self::DIRECTIONS as [$dx, $dy, $dz]) {
                $neighbour = [$cube[0] + $dx, $cube[1] + $dy, $cube[2] + $dz];
                $neighbourKey = sprintf('%d-%d-%d', $neighbour[0], $neighbour[1], $neighbour[2]);
                if (!in_array($neighbourKey, $visited) && !in_array($neighbour, $this->cubes)) {
                    $visited[] = $neighbourKey;
                    $queue->enqueue($neighbour);
                }
            }
        }

        // add visited to memory
        foreach ($visited as $vKey) {
            $this->memory[$vKey] = $trapped;
        }

        return $trapped;
    }

    private function isOutOfRange(array $cube): bool
    {
        return !(
            $cube[0] >= $this->ranges[0][0] && $cube[0] <= $this->ranges[0][1] &&
            $cube[1] >= $this->ranges[1][0] && $cube[1] <= $this->ranges[1][1] &&
            $cube[2] >= $this->ranges[2][0] && $cube[2] <= $this->ranges[2][1])
            ;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $this->cubes[] = explode(',', trim($line));
        }

        $this->ranges = [[INF, 0], [INF, 0], [INF, 0]];

        foreach ($this->cubes as $cube) {
            $this->ranges[0][0] = min($this->ranges[0][0], $cube[0]);
            $this->ranges[0][1] = max($this->ranges[0][1], $cube[0]);

            $this->ranges[1][0] = min($this->ranges[1][0], $cube[1]);
            $this->ranges[1][1] = max($this->ranges[1][1], $cube[1]);

            $this->ranges[2][0] = min($this->ranges[2][0], $cube[2]);
            $this->ranges[2][1] = max($this->ranges[2][1], $cube[2]);
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dm %ds', $diff->i, $diff->s);
    }
}
