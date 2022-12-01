<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;

class Day1Puzzle2 implements SolutionInterface {

    private string $inputPath;
    private $data = [];

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
    }

    public function execute()
    {
        $this->prepeareData();

        $elves = [];
        $number = 1;

        // loop throught data, set elves and sum theirs calories into the array
        foreach($this->data as $calorie){
            if(is_numeric($calorie)){
                if(!isset($elves[$number])){
                    $elves[$number] = 0;
                }

                $elves[$number] += $calorie;
            }
            else {
                $number++;
            }
        }

        // Sort elves desc by theirs calories
        arsort($elves);

        // get 3 highest calories
        $winner = array_slice($elves, 0, 3, true);

        // sum 3 highest calories
        $sum = array_sum($winner);

        return [
            'solution' => $sum,
            'elves' => $elves,
            'winner' => $winner
        ];
    }

    private function prepeareData()
    {
        $this->data = file($this->inputPath);

        foreach($this->data as $key => $entry){
            $this->data[$key] = trim($entry);
        }
    }
}
