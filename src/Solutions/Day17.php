<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day17 implements SolutionInterface
{
    const EMPTY_SIGN = '.';
    const BLOCK_SIGN = '#';

    const BLOCKS = [
        [0 => [0 => self::BLOCK_SIGN, 1 => self::BLOCK_SIGN, 2 => self::BLOCK_SIGN, 3 => self::BLOCK_SIGN]],
        [
            -2 => [1 => self::BLOCK_SIGN],
            -1 => [0 => self::BLOCK_SIGN, 1 => self::BLOCK_SIGN, 2 => self::BLOCK_SIGN],
            0 => [1 => self::BLOCK_SIGN]
        ],
        [
            -2 => [2 => self::BLOCK_SIGN],
            -1 => [2 => self::BLOCK_SIGN],
            0 => [0 => self::BLOCK_SIGN, 1 => self::BLOCK_SIGN, 2 => self::BLOCK_SIGN],
        ],
        [
            -3 => [0 => self::BLOCK_SIGN],
            -2 => [0 => self::BLOCK_SIGN],
            -1 => [0 => self::BLOCK_SIGN],
            0 => [0 => self::BLOCK_SIGN]
        ],
        [
            -1 => [0 => self::BLOCK_SIGN, 1 => self::BLOCK_SIGN],
            0 => [0 => self::BLOCK_SIGN, 1 => self::BLOCK_SIGN]
        ]
    ];

    private string $data;
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

        $maxBlocks = 2022;
        $stage = $this->initStage();
        $jets = str_split($this->data);
        $solution = 0;

        for($step = 0; $step < $maxBlocks; $step++){
            $solution = $this->run(self::BLOCKS[$step % 5], $stage, $jets);
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

        $rounds = 1000000000000;
        $stage = $this->initStage();
        $jets = str_split($this->data);
        $height = $i = $toAdd = 0;
        $states = [];

        while ($i < $rounds) {
            $height = $this->run(self::BLOCKS[$i % 5], $stage, $jets);

            $state = implode(array_map(fn ($row) => implode($row), array_slice($stage, -10000)));
            $key = md5(sprintf('%s-%d-%s', implode($jets), $i % 5, $state));

            if (isset($states[$key]) && $i >= count($jets) / 5) {
                $diffRounds = $i - $states[$key][0];
                $remaining = floor(($rounds - $i) / $diffRounds);
                $toAdd += $remaining * ($height - $states[$key][1]);
                $i += $remaining * $diffRounds;
            }
            $states[$key] = [$i, $height];
            ++$i;
        }

        $solution = (int)($height + $toAdd);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function run(array $block, array &$stage, array &$jets)
    {
        $bottom = count($stage) - 1;

        // put block into the game and set tmp position
        $tmpBlockPosition = [];
        foreach ($block as $y => $row) {
            foreach ($row as $x => $value) {
                $moveX = $x + 2;

                // create temporary block position
                $tmpBlockPosition[$y][$moveX] = $value;
            }
        }

        // update block position
        $block = $tmpBlockPosition;

        // while
        $finish = false;
        while(!$finish){
            // move block left or right
            // get next jet direction and add it to end of array to create jet loop
            $dir = array_shift($jets);
            $jets[] = $dir;

            $tmpBlockPosition = [];
            foreach ($block as $y => $row) {
                foreach ($row as $x => $value) {
                    $moveX = $x + ($dir === '>' ? 1 : -1);

                    // if next move x is out of bounds, dont move block
                    if ($moveX > 6 || $moveX < 0) {
                        $tmpBlockPosition = $block;
                        break 2;
                    }

                    if (isset($stage[$y][$moveX]) && $stage[$y][$moveX] !== '.') {
                        $tmpBlockPosition = $block;
                        break 2;
                    }

                    // create temporary block position
                    $tmpBlockPosition[$y][$moveX] = $value;
                }
            }

            // update block position
            $block = $tmpBlockPosition;

            // now move block down
            $tmpBlockPosition = [];
            foreach ($block as $y => $row) {
                foreach ($row as $x => $value) {
                    $moveY = $y + 1;

                    if ($moveY > $bottom) {
                        $tmpBlockPosition = $block;
                        $finish = true;
                        break 2;
                    }

                    if (isset($stage[$moveY][$x]) && $stage[$moveY][$x] !== '.') {
                        $tmpBlockPosition = $block;
                        $finish = true;
                        break 2;
                    }

                    // create temporary block position
                    $tmpBlockPosition[$moveY][$x] = $value;
                }
            }

            // update block position
            $block = $tmpBlockPosition;
        }

        $minY = 5;
        foreach ($block as $y => $row) {
            $minY = min($minY, $y);
            foreach ($row as $x => $value) {
                $stage[$y][$x] = $value;
            }
        }

        // calculate needed height
        $needed = 0;
        for ($i = 0; $i < 4; ++$i) {
            if (in_array('#', $stage[$i])) {
                $needed = 4 - $i;
                break;
            }
        }

        // get height of tower
        $height = count($stage) - (4 - $needed);

        // add new rows above
        for ($i = 0; $i < $needed; ++$i) {
            array_unshift($stage, array_fill(0, 7, '.'));
        }

        return $height;
    }

    private function initStage(): array
    {
        $stage = [];

        for ($y = 0; $y < 4; $y++) {
            for ($x = 0; $x < 7; $x++) {
                $stage[$y][$x] = self::EMPTY_SIGN;
            }
        }

        return $stage;
    }

    private function draw(array $stage): string
    {
        $result = '';

        foreach($stage as $lines){
            $result .= '|'.implode('', $lines)."|\n";
        }

        $result .= "+-------+\n";

        return $result;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $this->data = trim($line);
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dm %ds', $diff->i, $diff->s);
    }
}
