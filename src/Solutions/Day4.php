<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day4 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

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

        foreach ($this->data as $pairs){
            if($pairs['lf'] >= $pairs['rf'] && $pairs['lt'] <= $pairs['rt'] || $pairs['rf'] >= $pairs['lf'] && $pairs['rt'] <= $pairs['lt']){
                $solution++;
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
        $solution = 0;

        foreach ($this->data as $pairs){
            if(
                $pairs['lf'] >= $pairs['rf'] && $pairs['lf'] <= $pairs['rt'] ||
                $pairs['lt'] >= $pairs['rf'] && $pairs['lt'] <= $pairs['rt'] ||
                $pairs['rf'] >= $pairs['lf'] && $pairs['rf'] <= $pairs['lt'] ||
                $pairs['rt'] >= $pairs['lf'] && $pairs['rt'] <= $pairs['lt']
            ){
                $solution++;
            }
        }

        return $solution;
    }

    private function prepareData()
    {
        $input = file($this->inputPath);

        foreach($input as $key => $entry){
            $pair = explode(',', trim($entry));

            list($lf, $lt) = explode('-', $pair[0]);
            list($rf, $rt) = explode('-', $pair[1]);

            $this->data[$key]['lf'] = $lf;
            $this->data[$key]['lt'] = $lt;
            $this->data[$key]['rf'] = $rf;
            $this->data[$key]['rt'] = $rt;
        }
    }
}
