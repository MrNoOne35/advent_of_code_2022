<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day6 implements SolutionInterface {

    private string $inputPath;
    private string $data;

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
        return $this->getSolution();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function puzzle2(): ?int
    {
        return $this->getSolution(14);
    }

    /**
     * @param int $length
     * @return int
     */
    private function getSolution(int $length = 4): int
    {
        $solution = 0;

        for($i = 0; $i < strlen($this->data) - $length - 1; $i++){
            $chunk = substr($this->data, $i, $length);

            if(strlen(count_chars($chunk, 3)) == $length){
                $solution = $i + $length;
                break;
            }
        }

        return $solution;
    }

    private function prepareData()
    {
        $this->data = trim(file_get_contents($this->inputPath));
    }
}
