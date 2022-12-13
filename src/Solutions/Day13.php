<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day13 implements SolutionInterface
{
    private string $inputPath;
    private array $data;
    private array $all;

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

        $input = $this->data;

        foreach ($input as $index => $compare) {
            $result = $this->compare($compare['left'], $compare['right']);

            if($result) $solution += $index;
        }

        return $solution;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function puzzle2(): ?int
    {
        $input = $this->all;

        $input[] = [[2]];
        $input[] = [[6]];

        usort($input, function($left, $right){
            return $this->sortPackets($left, $right);
        });

        return (array_search([[2]], $input) + 1) * (array_search([[6]], $input) + 1);
    }

    private function sortPackets($left, $right): ?bool
    {
        if(is_numeric($left) && is_array($right)) {
            $left = [$left];
        }

        if(is_array($left) && is_numeric($right)) {
            $right = [$right];
        }

        foreach($left as $lk => $lv){
            if(!isset($right[$lk])) return true;

            $rv = $right[$lk];

            if($lv === $rv) continue;

            if(is_numeric($lv) && is_numeric($rv)) return $lv > $rv;
            else {
                $result = $this->sortPackets($lv, $rv);

                if(!is_null($result)) return $result;
            }
        }

        if(count($left) < count($right)) return false;

        return null;
    }

    private function compare(array $left, array $right): ?bool
    {
        foreach($left as $lk => $lv){
            if(!isset($right[$lk])) return false;

            $rv = $right[$lk];

            if(is_numeric($lv) && is_numeric($rv)){
                if($lv < $rv) return true;
                else if($lv > $rv) return false;
            }
            else if(is_array($lv) && is_array($rv)) {
                $result = $this->compare($lv, $rv);

                if(!is_null($result)) return $result;
            }
            else if(is_array($lv) && is_numeric($rv)){
                $result = $this->compare($lv, [$rv]);

                if(!is_null($result)) return $result;
            }
            else if(is_numeric($lv) && is_array($rv)){
                $result = $this->compare([$lv], $rv);

                if(!is_null($result)) return $result;
            }
        }

        if(count($left) < count($right)) return true;
        else return null;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $key => $line) {
            $line = trim($line);

            $m = $key % 3;

            if($m === 2) continue;

            $k = floor($key / 3) + 1;

            if ($m == 0) $this->data[$k]['left'] = eval(sprintf("return array(%s);", substr($line, 1, strlen($line) - 2)));
            if ($m == 1) $this->data[$k]['right'] = eval(sprintf("return array(%s);", substr($line, 1, strlen($line) - 2)));

            $this->all[] = eval(sprintf("return array(%s);", substr($line, 1, strlen($line) - 2)));
        }
    }
}
