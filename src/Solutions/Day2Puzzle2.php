<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;

class Day2Puzzle2 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

    private array $mappings = [
        'A' => 1,
        'B' => 2,
        'C' => 3
    ];

    private array $outcome = [
        'X' => -1, // Lose
        'Y' => 0,  // Draw
        'Z' => 1   // Win
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
            $outcome = $this->outcome[$match[1]];
            $player = $this->getPlayerChoice($enemy, $outcome);

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
        // Tie
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

    /**
     * Return player choice based on outcome
     * @param int $enemy
     * @param int $outcome
     * @return int
     */
    public function getPlayerChoice(int $enemy, int $outcome): int
    {
        // Must win
        if($outcome > 0){
            return ($enemy % 3) + 1;
        }
        // Must lose
        else if ($outcome < 0){
            return ($enemy - 1) % 3 ?: 3;
        }
        // Must draw
        else return $enemy;
    }

    private function prepareData()
    {
        $this->data = file($this->inputPath);

        foreach($this->data as $key => $entry){
            $this->data[$key] = explode(' ', trim($entry));
        }
    }
}
