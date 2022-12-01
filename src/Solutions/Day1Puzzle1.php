<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;

class Day1Puzzle1 implements SolutionInterface {

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

        // get top winner
        $winner[array_key_first($elves)] = reset($elves);

        return [
            'solution' => reset($elves),
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
