<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day7 implements SolutionInterface {

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

        foreach($this->data as $item){
            if($item['size'] <= 100000) {
                $solution += $item['size'];
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

        $targetSize = 30000000 - (70000000 - $this->data['/']['size']);

        usort($this->data, function($a, $b){
            return $a['size'] <=> $b['size'];
        });

        foreach ($this->data as $item){
            if(intval($item['size']) >= $targetSize){
                $solution = $item['size'];
                break;
            }
        }

        return $solution;
    }

    /*
     * simply method to imitate cp command on string
     */
    private function updatePath(string $path, string $next): string
    {
        // reduce path to /
        if($next == '/') $path = '/';
        else if($next == '..') {
            // trim path string ex. /one/to/ to one/to
            $trimmed = ltrim(rtrim($path, '/'), '/');

            // split string to array by /
            $pathArray = explode('/', $trimmed);

            // remove last array element
            array_pop($pathArray);

            // create path string again with rest of array
            $path = '/'.implode('/', $pathArray) . ((count($pathArray) > 0) ? '/' : '');
        }
        else {
            // just add next dir to path
            $path .= $next.'/';
        }

        // if path not exists create it
        if(!isset($this->data[$path])){
            $this->data[$path]['files'] = [];
            $this->data[$path]['size'] = 0;
        }

        return $path;
    }

    private function addFile(string $path, string $name, int $size){
        $this->data[$path]['files'][] = [
            'filename' => $name,
            'size' => $size
        ];
    }

    private function computeSize(){

        // sum file size for dir
        foreach($this->data as $path => $item){
            foreach($item['files'] as $file){
                $this->data[$path]['size'] += $file['size'];
            }
        }

        // add size of inner dirs. No recursion needed
        foreach($this->data as $path => $item){
            $this->data[$path]['size'] += $this->getTotalSize($path);
        }
    }

    private function getTotalSize(string $startWith){
        $sum = 0;

        foreach($this->data as $path => $item){
            if(str_starts_with($path, $startWith) && $path != $startWith){
                $sum += $item['size'];
            }
        }

        return $sum;
    }

    private function prepareData()
    {
        $path = '';

        $list = file($this->inputPath);

        // parse only lines with command "cd" and "file" information
        // Other lines of input are redundant
        foreach($list as $line){
            $line = trim($line);

            $fistChar = substr($line, 0, 1);

            if($fistChar == '$')
            {
                $command = explode(' ', $line);

                if($command[1] == 'cd'){
                    $path = $this->updatePath($path, $command[2]);
                }
            }
            else if(is_numeric($fistChar)){
                list($size, $name) = explode(' ', $line);
                $this->addFile($path, $name, intval($size));
            }
        }

        $this->computeSize();
    }
}
