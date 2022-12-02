<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;

class Day1Puzzle1 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
    }

    public function execute(): array
    {
        $this->prepareData();

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

        // get top winner
        $winner[array_key_first($elves)] = reset($elves);

        return [
            'solution' => reset($elves),
            'elves' => $elves,
            'winner' => $winner
        ];
    }

    private function prepareData()
    {
        $this->data = file($this->inputPath);

        foreach($this->data as $key => $entry){
            $this->data[$key] = trim($entry);
        }
    }
}
