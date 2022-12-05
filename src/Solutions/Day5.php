<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day5 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
        $this->prepareData();
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function puzzle1(): ?string
    {
        return $this->getSolution(true);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function puzzle2(): ?string
    {
        return $this->getSolution();
    }

    /**
     * @param bool $reverse
     * @return string
     */
    private function getSolution(bool $reverse = false): string
    {
        $solution = '';

        $layout = $this->data['layout'];

        foreach($this->data['commands'] as $command){
            // Copy creates
            $cut = array_slice($layout[$command['from']], count($layout[$command['from']]) - $command['move']);

            // Reverse creates
            if($reverse) krsort($cut);

            // Append creates
            $layout[$command['to']] = array_merge($layout[$command['to']], $cut);

            // Remove creates
            $layout[$command['from']] = array_slice($layout[$command['from']], 0, count($layout[$command['from']]) - $command['move']);
        }

        foreach($layout as $column){
            $solution .= array_pop($column);
        }

        return $solution;
    }

    private function prepareData()
    {
        $input = file($this->inputPath);

        $isMoves = false;
        $layout = [];

        foreach($input as $key => $entry){

            // Find point between crates layout and commands.
            if(empty(trim($entry))){
                $isMoves = true;
                continue;
            }

            // If parsing layout, trim everything to simple array
            if(!$isMoves) {
               $line = str_split(rtrim($entry), 4);
               $line = array_map(function($item){
                   return str_replace(['[', ']'], '', trim($item));
               }, $line);
               $layout[] = $line;
            }

            // If parsing moves preg match moves into array of "moves", "from" and "to"
            else {
                preg_match_all('!\d+!', trim($entry), $matches);
                $match = reset($matches);

                $this->data['commands'][] = [
                    'move' => $match[0],
                    'from' => $match[1],
                    'to' => $match[2]
                ];
            }
        }

        // Sort layout array by key desc. We want to start from bottom to top
        krsort($layout);

        // remove first element of array. We dont need column numbers. We get it by array index instead.
        $layout = array_slice($layout, 1);

        // loop through layout columns and create arrays of column with letters from bottom to top
        for($i = 0; $i < count(reset($layout)); $i++){
            $columnNumber = $i + 1;

            foreach($layout as $item){
                if(isset($item[$i]) && !empty($item[$i])){
                    $this->data['layout'][$columnNumber][] = $item[$i];
                }
            }
        }
    }
}
