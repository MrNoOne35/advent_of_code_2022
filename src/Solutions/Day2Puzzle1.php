<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;

class Day2Puzzle1 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

    private array $mappings = [
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'X' => 1,
        'Y' => 2,
        'Z' => 3
    ];

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
    }

    public function execute(): array
    {
        $this->prepareData();

        $solution = 0;

        foreach($this->data as $match){
            $enemy = $this->mappings[$match[0]];
            $player = $this->mappings[$match[1]];

            $solution += $this->getPlayerPoints($enemy, $player) + $player;
        }

        return [
            'solution' => $solution
        ];
    }

    /**
     * Return value based on win/lose/draw
     * @param int $enemy
     * @param int $player
     * @return int
     */
    public function getPlayerPoints(int $enemy, int $player): int
    {
        // Draw
        if($enemy == $player){
            return 3;
        }
        // Lost
        else if(($player + 1) % 3 == $enemy % 3){
            return 0;
        }
        // Win
        else {
            return 6;
        }
    }

    private function prepareData()
    {
        $this->data = file($this->inputPath);

        foreach($this->data as $key => $entry){
            $this->data[$key] = explode(' ', trim($entry));
        }
    }
}
