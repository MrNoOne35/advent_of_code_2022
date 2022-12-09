<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day9 implements SolutionInterface {

    private string $inputPath;
    private array $data;
    private array $path;

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
        return $this->getSolution(10);
    }

    /**
     * @throws Exception
     */
    private function getSolution(int $ropeLength = 2): int
    {
        $this->initPath($ropeLength);

        $positions = [];

        foreach ($this->data as $commands){
            for($i = 0; $i < $commands['move']; $i++){
                $this->moveHead($commands['dir']);
                $this->moveTail();

                $lastPos = end($this->path);
                $position = sprintf('%d,%d', $lastPos[$ropeLength-1]['x'], $lastPos[$ropeLength-1]['y']);

                if(!in_array($position, $positions)) {
                    $positions[] = $position;
                }
            }
        }

        return count($positions);
    }

    private function moveHead(string $dir){
        $nextPos = end($this->path);

        switch($dir){
            case 'U':
                $nextPos[0]['y']--;
                break;
            case 'D':
                $nextPos[0]['y']++;
                break;
            case 'L':
                $nextPos[0]['x']--;
                break;
            case 'R':
                $nextPos[0]['x']++;
                break;
        }

        $this->path[] = $nextPos;
    }

    private function moveTail(int $chunk = 1){
        $lastMove = end($this->path);
        $key = array_key_last($this->path);
        $prevChunk = $chunk - 1;

        $hx = $lastMove[$prevChunk]['x'];
        $hy = $lastMove[$prevChunk]['y'];
        $tx = $lastMove[$chunk]['x'];
        $ty = $lastMove[$chunk]['y'];

        $vectorLen = floor(sqrt(abs(($hx - $tx)**2) + abs(($hy - $ty)**2)));

        // Move chunk if vector is longer than 1
        if($vectorLen > 1){
            // move only horizontal
            if($hy == $ty && abs($hx - $tx) > 1) {
                if($hx > $tx) $this->path[$key][$chunk]['x']++;
                else $this->path[$key][$chunk]['x']--;
            }

            // move only vertical
            if($hx == $tx && abs($hy - $ty) > 1) {
                if($hy > $ty) $this->path[$key][$chunk]['y']++;
                else $this->path[$key][$chunk]['y']--;
            }

            // move only diagonal
            if($hy != $ty && $hx != $tx){
                $diffY = abs($hy - $ty);
                $diffX = abs($hx - $tx);

                // as head moves only in 4 directions, tail can sometimes move diagonal in 8 directions. We need to cover that here
                if($diffY > 1 && $diffX > 1){
                    if($hy > $ty) $this->path[$key][$chunk]['y']++;
                    else $this->path[$key][$chunk]['y']--;
                    if($hx > $tx) $this->path[$key][$chunk]['x']++;
                    else $this->path[$key][$chunk]['x']--;
                }
                else if($diffY > 1){
                    $this->path[$key][$chunk]['x'] = $hx;
                    if($hy > $ty) $this->path[$key][$chunk]['y']++;
                    else $this->path[$key][$chunk]['y']--;
                }
                else if($diffX > 1){
                    $this->path[$key][$chunk]['y'] = $hy;
                    if($hx > $tx) $this->path[$key][$chunk]['x']++;
                    else $this->path[$key][$chunk]['x']--;
                }
            }
        }

        // update position for next chunks
        if($chunk < count($this->path[$key]) - 1){
            $this->moveTail($chunk + 1);
        }
    }

    /**
     * @throws Exception
     */
    private function initPath(int $len){
        if($len < 2){
            throw new Exception('Length must be of 2 or more');
        }

        $this->path = [];

        $path = [];

        for($i = 0; $i < $len; $i++){
            $path[$i] = ['x' => 0, 'y' => 0];
        }

        $this->path[] = $path;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach($list as $line){
            list($dir, $move) = explode(' ', trim($line));
            $this->data[] = [
                'dir' => $dir,
                'move' => $move
            ];
        }

    }
}
