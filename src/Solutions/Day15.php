<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// Part1 0s
// Part2 22s

class Day15 implements SolutionInterface
{
    private array $data;
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

        // For each puzzle there is different row to check
        if($this->inputPath == 'public/puzzles/15/test1.txt') $row = 10;
        else $row = 2000000;

        $leftMost = 0;
        $rightMost = 0;

        foreach($this->data as $entry){
            $distance = $this->getDistance($entry['sensor'], $entry['beacon']);
            $sensorYLength = abs($row - $entry['sensor']['y']);

            if($distance > $sensorYLength){
                $lengthX = abs($distance - $sensorYLength);

                $left = $entry['sensor']['x'] - $lengthX;
                $right = $entry['sensor']['x'] + $lengthX;

                if($leftMost > $left) $leftMost = $left;
                if($rightMost < $right) $rightMost = $right;
            }
        }

        $solution = abs($rightMost - $leftMost);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        $lonelyBeacon = [];

        // For each puzzle there is different range bounds
        if($this->inputPath == 'public/puzzles/15/test1.txt') $maxBounds = 20;
        else $maxBounds = 4000000;

        foreach($this->data as $entry){
            $distance = $this->getDistance($entry['sensor'], $entry['beacon']);
            $sx = $entry['sensor']['x'];
            $sy = $entry['sensor']['y'];

            // loop through sensor range
            // if found empty spot then stop everything and return lonely beacon coordinates
            for($i = $distance; $i >= 0; $i--){
                $vertical = abs($distance - $i);

                $checkX = $sx + $i + 1;
                $checkY = $sy - $vertical;
                if($this->checkRange($checkX, $checkY, $maxBounds)) {
                    $lonelyBeacon = ['x' => $checkX, 'y' => $checkY];
                    break 2;
                }

                $checkX = $sx - $i - 1;
                $checkY = $sy + $vertical;
                if($this->checkRange($checkX, $checkY, $maxBounds)) {
                    $lonelyBeacon = ['x' => $checkX, 'y' => $checkY];
                    break 2;
                }
            }
        }

        $solution = $lonelyBeacon['x'] * 4000000 + $lonelyBeacon['y'];

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    /**
     * Check if coordinates are outside sensor range
     *
     * @param int $x
     * @param int $y
     * @param int $maxBounds
     * @return bool
     */
    private function checkRange(int $x, int $y, int $maxBounds): bool
    {
        if ($x < 0 || $x > $maxBounds || $y < 0 || $y > $maxBounds) return false;

        foreach($this->data as $entry) {
            $distanceLeft = $this->getDistance($entry['sensor'], $entry['beacon']);
            $distanceRight = $this->getDistance(['x' => $x, 'y' => $y], $entry['sensor']);
            if ($distanceLeft >= $distanceRight) return false;
        }

        return true;
    }

    /**
     * Get distance between two points
     *
     * @param array $point1
     * @param array $point2
     * @return float|int
     */
    private function getDistance(array $point1, array $point2): float|int
    {
        return abs($point1['x'] - $point2['x']) + abs($point1['y'] - $point2['y']);
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $line = trim($line);

            // get only coordinates
            preg_match_all('/Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)/', $line, $matches);

            $this->data[] = [
                'sensor' => [
                    'x' => $matches[1][0],
                    'y' => $matches[2][0],
                ],
                'beacon' => [
                    'x' => $matches[3][0],
                    'y' => $matches[4][0]
                ]
            ];
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dm %ds', $diff->i, $diff->s);
    }
}
