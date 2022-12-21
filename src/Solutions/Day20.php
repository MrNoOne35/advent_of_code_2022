<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// Part 1 execution time is around 0 seconds
// Part 2 execution time is around 3 seconds

class Day20 implements SolutionInterface
{
    private array $data;
    private string $inputPath;

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
        $this->prepareData();
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle1(): ?array
    {
        $startTime = new \DateTime();

        $numbers = $this->data;
        $breaks = [1000, 2000, 3000];
        $loops = count($this->data);

        $solution = $this->decode($numbers, $breaks, $loops);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        $numbers = $this->data;
        $breaks = [1000, 2000, 3000];
        $loops = count($this->data);

        $numbers = array_map(fn ($v) => $v * 811589153, $numbers);
        $loops *= 10;

        $solution = $this->decode($numbers, $breaks, $loops);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function decode(array &$numbers, array $breaks, int $loops){
        $count = 1;
        $finished = false;
        $length = count($numbers) - 1;
        $result = 0;

        while (!$finished) {
            $index = ($count - 1) % ($length + 1);
            $value = $numbers[$index];

            if($value !== 0){
                // get offsets to the left and right
                $offsetLeft = array_search($index, array_keys($numbers));
                $offsetRight = $length - $offsetLeft;

                $position = 0;

                if ($value > 0) {
                    $position = ($value - $offsetRight) % $length;
                    if($position == 0) $position = $length;
                } else if ($value < 0) {
                    $position = ($offsetLeft + $value) % $length;
                    if($position == 0) $position = $length;
                }

                // remove element from array
                unset($numbers[$index]);

                // get left array chunk
                $left = array_slice($numbers, 0, $position, true);

                // set new element
                $middle = [$index => $value];

                // get right array chunk
                $right = array_slice($numbers, $position, null, true);

                // merge all
                $numbers = array_replace_recursive($left, $middle, $right);
            }

            if ($count == $loops) $finished = true;

            ++$count;
        }

        foreach($breaks as $break){
            $zeroIndex = array_search(0, $numbers);
            $offsetLeft = array_search($zeroIndex, array_keys($numbers));
            $offsetRight = count($numbers) - $offsetLeft;
            $position = ($break - $offsetRight) % ($length + 1);

            $result += array_slice($numbers, $position, 1)[0];
        }

        return $result;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $line = trim($line);

            $this->data[] = (int)$line;
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dm %ds', $diff->i, $diff->s);
    }
}
