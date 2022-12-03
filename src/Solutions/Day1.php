<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;

class Day1 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
        $this->prepareData();
    }

    public function puzzle1(): ?int
    {
        $elves = $this->getSortedElves();

        // Sort elves desc by theirs calories
        arsort($elves);

        return reset($elves);
    }

    public function puzzle2(): ?int
    {
        $elves = $this->getSortedElves();

        // Sort elves desc by theirs calories
        arsort($elves);

        // get 3 highest calories

        $winner = array_slice($elves, 0, 3, true);

        // sum 3 highest calories
        $sum = array_sum($winner);

        return $sum;
    }

    private function getSortedElves(): array
    {
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

        return $elves;
    }

    private function prepareData()
    {
        $this->data = file($this->inputPath);

        foreach($this->data as $key => $entry){
            $this->data[$key] = trim($entry);
        }
    }
}
