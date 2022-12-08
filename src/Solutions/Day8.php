<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day8 implements SolutionInterface {

    private string $inputPath;
    private array $data;

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
        $this->prepareData();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function puzzle1(): ?int
    {
        $solution = 0;

        for($row = 0; $row < count($this->data); $row++){
            for($col = 0; $col < count($this->data[$row]); $col++){
                if($this->isVisible($row, $col, $this->data[$row][$col])){
                    $solution++;
                }
            }
        }

        return $solution;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function puzzle2(): ?int
    {
        $stack = [];

        for($row = 0; $row < count($this->data); $row++){
            for($col = 0; $col < count($this->data[$row]); $col++){
                $key = sprintf('%d-%d-%d', $row, $col, $this->data[$row][$col]);
                $stack[$key] = $this->measureDistance($row, $col, $this->data[$row][$col]);
            }
        }

        rsort($stack);

        return reset($stack);
    }

    public function measureDistance(int $row, int $col, int $value): float|int
    {
        $left = 0;
        $right = 0;
        $up = 0;
        $down = 0;

        // check left
        for($i = $col; $i >= 0; $i--){
            if($i == $col) continue;
            if($this->data[$row][$i] >= $value){
                $left += 1;
                break;
            }
            else $left++;
        }

        // check right
        for($i = $col; $i < count($this->data[$row]); $i++){
            if($i == $col) continue;
            if($this->data[$row][$i] >= $value){
                $right += 1;
                break;
            }
            else $right++;
        }

        // check up
        for($i = $row; $i >= 0; $i--){
            if($i == $row) continue;
            if($this->data[$i][$col] >= $value){
                $up += 1;
                break;
            }
            else $up++;
        }

        // check down
        for($i = $row; $i < count($this->data); $i++){
            if($i == $row) continue;
            if($this->data[$i][$col] >= $value){
                $down += 1;
                break;
            }
            else $down++;
        }

        return $left * $right * $up * $down;
    }

    public function isVisible(int $row, int $col, int $value): bool
    {
        if($row == 0 || $row == count($this->data) - 1 || $col == 0 || $col == count($this->data[$row]) - 1){
            return true;
        }

        $visibility = 4;

        // check left
        for($i = $col - 1; $i >= 0; $i--){
            if($this->data[$row][$i] >= $value){
                $visibility--;
                break;
            }
        }

        // check right
        for($i = $col + 1; $i < count($this->data[$row]); $i++){
            if($this->data[$row][$i] >= $value){
                $visibility--;
                break;
            }
        }

        // check up
        for($i = $row - 1; $i >= 0; $i--){
            if($this->data[$i][$col] >= $value){
                $visibility--;
                break;
            }
        }

        // check down
        for($i = $row + 1; $i < count($this->data); $i++){
            if($this->data[$i][$col] >= $value){
                $visibility--;
                break;
            }
        }

        return $visibility > 0;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach($list as $line){
            $this->data[] = str_split(trim($line));
        }
    }
}
