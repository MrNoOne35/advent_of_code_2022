<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// Not optimal. Used 4gb of ram :)
// Need to find another solution for this next time

class Day14 implements SolutionInterface
{
    const START_SIGN = '+';
    const WALL_SIGN = '#';
    const EMPTY_SIGN = '.';
    const ROCK_SIGN = 'o';
    const START_Y = 0;
    const START_X = 500;

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
        $map = $this->generateMap();

        $solution = $this->simulateRocks($map);

        $this->sortMap($map);

        return ['solution' => $solution, 'draw' => $this->draw($map)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $map = $this->generateMap();

        // Add floor
        $this->generateObjects($map, $map['size']['y2'] + 2, $map['size']['x1'] - 130, $map['size']['y2'] + 2, $map['size']['x2'] + 160);
        $this->sortMap($map);
        $this->calculateSize($map);

        $solution = $this->simulateRocks($map);

        $this->sortMap($map);

        return ['solution' => $solution, 'draw' => $this->draw($map)];
    }

    /**
     * @throws Exception
     */
    private function simulateRocks(&$map, int $y = null, int $x = null, int $step = 0): int
    {
        if (is_null($y) || is_null($x)) {
            $y = self::START_Y;
            $x = self::START_X;

            if (empty($map['objects'][$y][$x]) || $map['objects'][$y][$x] == self::START_SIGN) {

                $map['objects'][$y][$x] = self::ROCK_SIGN;

                return $this->simulateRocks($map, $y, $x, $step);
            } else {
                return $step;
            }

        } else {

            $ny = null;
            $nx = null;

            // Check bottom
            if (empty($map['objects'][$y + 1][$x])) {
                $ny = $y + 1;
                $nx = $x;
            } // Check bottom left
            else if (empty($map['objects'][$y + 1][$x - 1])) {
                $ny = $y + 1;
                $nx = $x - 1;
            } // Check bottom right
            else if (empty($map['objects'][$y + 1][$x + 1])) {
                $ny = $y + 1;
                $nx = $x + 1;
            }

            if ($ny && $nx) {
                unset($map['objects'][$y][$x]);

                // out of bounds
                if($ny > $map['size']['y2'] || $nx < $map['size']['x1'] || $nx > $map['size']['x2']){
                    return $step;
                }

                $map['objects'][$ny][$nx] = self::ROCK_SIGN;

                return $this->simulateRocks($map, $ny, $nx, $step);
            } else {
                return $this->simulateRocks($map, null, null, $step + 1);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function getStartPoint(array $map): array
    {
        foreach ($map['objects'] as $y => $objects) {
            foreach ($objects as $x => $sign) {
                if ($sign === self::START_SIGN) return [$y, $x];
            }
        }

        throw new Exception('There is no start pointer');
    }

    private function generateMap(): array
    {
        $map = [];

        $map['size'] = ['y1' => null, 'x1' => null, 'y2' => null, 'x2' => null];
        $map['objects'][0][500] = self::START_SIGN;

        foreach ($this->data as $key => $points) {
            for ($i = 0; $i < count($points); $i++) {
                $ni = $i + 1;

                if (isset($points[$ni])) {
                    $this->generateObjects($map, $points[$i]['y'], $points[$i]['x'], $points[$ni]['y'], $points[$ni]['x']);
                }
            }
        }

        $this->sortMap($map);
        $this->calculateSize($map);

        return $map;
    }

    private function generateObjects(&$map, $startY, $startX, $endY, $endX)
    {
        $y1 = ($startY <= $endY) ? $startY : $endY;
        $y2 = ($startY >= $endY) ? $startY : $endY;
        $x1 = ($startX <= $endX) ? $startX : $endX;
        $x2 = ($startX >= $endX) ? $startX : $endX;

        for ($y = $y1; $y <= $y2; $y++) {
            for ($x = $x1; $x <= $x2; $x++) {
                $map['objects'][$y][$x] = self::WALL_SIGN;
            }
        }

    }

    private function sortMap(&$map)
    {
        ksort($map['objects']);

        foreach ($map['objects'] as &$object) {
            ksort($object);
        }
    }

    private function calculateSize(&$map)
    {
        $map['size']['y1'] = array_key_first($map['objects']);
        $map['size']['y2'] = array_key_last($map['objects']);

        foreach ($map['objects'] as &$object) {
            if (is_null($map['size']['x1']) || $map['size']['x1'] > array_key_first($object)) $map['size']['x1'] = array_key_first($object);
            if (is_null($map['size']['x2']) || $map['size']['x2'] < array_key_last($object)) $map['size']['x2'] = array_key_last($object);
        }
    }

    private function draw(array $map): string
    {
        $output = '';

        for ($y = $map['size']['y1']; $y <= $map['size']['y2']; $y++) {

            if(empty($output)){
                $output .= "\t";
                for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                    $output .= substr($x, 0, 1);
                }
                $output .= "\n";
                $output .= "\t";
                for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                    $output .= substr($x, 1, 1);
                }
                $output .= "\n";
                $output .= "\t";
                for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                    $output .= substr($x, 2, 1);
                }
                $output .= "\n";
            }


            $output .= $y."\t";

            for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                if (empty($map['objects'][$y][$x])) $output .= self::EMPTY_SIGN;
                else $output .= $map['objects'][$y][$x];
            }
            $output .= "\n";
        }

        return $output;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $key => $line) {
            $line = trim($line);

            $positions = explode(' -> ', $line);

            foreach ($positions as $position) {
                list($x, $y) = explode(',', $position);
                $point = ['x' => $x, 'y' => $y];
                $this->data[$key][] = $point;
            }
        }
    }
}
