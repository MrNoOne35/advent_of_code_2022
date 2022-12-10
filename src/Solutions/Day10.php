<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day10 implements SolutionInterface {

    private string $inputPath;
    private array $data;
    private array $crt = [];
    private int $crtMaxLength = 40;

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

        $x = 1;
        $threshold = 20;
        $maxThreshold = 220;

        for($i = 0; $i < count($this->data); $i++){
            $cycle = $i + 1;

            if($cycle == $threshold || (($threshold + $cycle) % 40) == 0){
                if($maxThreshold >= $cycle){
                    $solution += $x * $cycle;
                }
            }

            $x += $this->data[$i];
        }

        return $solution;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function puzzle2(): ?string
    {
        $this->initCrt();

        $x = 1;

        for($i = 0; $i < count($this->data); $i++){
            $this->setPixels($i, $x);

            $x += $this->data[$i];
        }

        return $this->draw();
    }

    private function draw(): string
    {
        $result = '';

        foreach($this->crt as $line){
            $result .= implode('', $line)."\n";
        }

        return $result;
    }

    private function setPixels(int $cycle, int $pos){
        $y = floor($cycle / $this->crtMaxLength);
        $pos += $y * $this->crtMaxLength;

        if($pos - 1 <= $cycle && $pos + 1 >= $cycle){
            $this->crt[$y][$cycle] = '#';
        }
    }

    private function initCrt(){
        $max = 240;

        for($y = 0; $y < $max; $y += $this->crtMaxLength){
            for($x = $y; $x < $y + $this->crtMaxLength; $x++){
                $this->crt[floor($y/$this->crtMaxLength)][$x] = '.';
            }
        }
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach($list as $line){
            $line = trim($line);

            if($line == 'noop'){
                $this->data[] = 0;
            }
            else {
                list($cmd, $value) = explode(' ', $line);
                $this->data[] = 0;
                $this->data[] = $value;
            }
        }

    }
}
