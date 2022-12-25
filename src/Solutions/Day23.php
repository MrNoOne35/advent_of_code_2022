<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// Part 1 execution time is around 43 seconds
// Part 2 execution time is around 43 minutes xD

class Day23 implements SolutionInterface
{
    const LOOK = [
        ['y' => [-1], 'x' => [-1, 0, 1]],
        ['y' => [1], 'x' => [-1, 0, 1]],
        ['y' => [-1, 0, 1], 'x' => [-1]],
        ['y' => [-1, 0, 1], 'x' => [1]]
    ];
    const MOVE = [
        ['y' => -1, 'x' => 0],
        ['y' => 1, 'x' => 0],
        ['y' => 0, 'x' => -1],
        ['y' => 0, 'x' => 1]
    ];

    private string $inputPath;
    private array $map;

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

        $map = $this->map;

        $this->moveElves($map, 10, false);
        $this->resize($map);
        $solution = $this->countEmptySpaces($map);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime), $this->draw($map)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        $map = $this->map;

        $solution = $this->moveElves($map, 10, true);

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function moveElves(array &$map, int $rounds, bool $part2 = false): int
    {
        $elvesCount = count($map['elves']);
        $round = 0;

        while (true) {
            $skipCount = count(array_filter($map['elves'], fn($elvesData) => $elvesData['skip']));

            if(!$part2){
                if ($elvesCount == $skipCount || $round == $rounds) {
                    break;
                }
            }
            else {
                if ($elvesCount == $skipCount){
                    return $round;
                }
            }


            foreach ($map['elves'] as &$elfData) {
                $elfData['skip'] = !$this->isNerByElf($map['elves'], $elfData['y'], $elfData['x']);

                if ($elfData['skip']) {
                    continue;
                }

                // Look
                $elfData['move'] = null;
                $look = $elfData['look'];

                for ($i = 0; $i < 4; $i++) {
                    $lookData = self::LOOK[$look];
                    $found = 0;

                    for ($y = 0; $y < count($lookData['y']); $y++) {
                        for ($x = 0; $x < count($lookData['x']); $x++) {
                            $lookY = $elfData['y'] + $lookData['y'][$y];
                            $lookX = $elfData['x'] + $lookData['x'][$x];
                            //echo sprintf('LookY: %d, LookX: %d', $lookY, $lookX)."\n";
                            $found += count(array_filter($map['elves'], function ($elf) use ($lookX, $lookY) {
                                return $lookX == $elf['x'] && $lookY == $elf['y'];
                            }));


                        }
                    }

                    if ($found === 0) {
                        $elfData['move'] = ['y' => $elfData['y'] + self::MOVE[$look]['y'], 'x' => $elfData['x'] + self::MOVE[$look]['x']];
                        break;
                    }

                    $look++;
                    if ($look > 3) $look = 0;
                }
            }

            // Stop movement to the same cell
            foreach ($map['elves'] as $key1 => $leftElf) {
                if (is_null($leftElf['move'])) continue;
                foreach ($map['elves'] as $key2 => $rightElf) {
                    if (is_null($rightElf['move'])) continue;
                    if ($key1 === $key2) continue;
                    if ($leftElf['move']['y'] == $rightElf['move']['y'] && $leftElf['move']['x'] == $rightElf['move']['x']) {
                        $map['elves'][$key1]['move'] = null;
                        $map['elves'][$key2]['move'] = null;
                    }
                }
            }

            // Move and update look direction
            foreach ($map['elves'] as &$elves) {
                if (!is_null($elves['move'])) {
                    $elves['y'] = $elves['move']['y'];
                    $elves['x'] = $elves['move']['x'];
                    $elves['move'] = null;
                }

                $elves['look']++;
                if ($elves['look'] > 3) $elves['look'] = 0;
            }

            $round++;
        }

        return $rounds;
    }

    private function isNerByElf(array $elves, int $elfY, int $elfX): bool
    {
        $y1 = $elfY - 1;
        $y2 = $elfY + 1;
        $x1 = $elfX - 1;
        $x2 = $elfX + 1;
        //echo sprintf('Elft: %d, Elfx: %d, y1: %d, y2: %d', $elfY, $elfX, $y1, $y2)."\n";
        foreach ($elves as $elfData) {
            if($elfData['y'] == $elfY && $elfData['x'] == $elfX) continue;
            for($y = $y1; $y <= $y2; $y++){
                for($x = $x1; $x <= $x2; $x++){
                    if($elfData['y'] == $y && $elfData['x'] == $x){
                        return true;
                    }
                }
            }

        }

        return false;
    }

    private function resize(array &$map){
        $map['size']['y1'] = null;
        $map['size']['y2'] = null;
        $map['size']['x1'] = null;
        $map['size']['x2'] = null;

        foreach($map['elves'] as $elf){
            if (is_null($map['size']['y1'])) $map['size']['y1'] = $elf['y'];
            if (is_null($map['size']['y2'])) $map['size']['y2'] = $elf['y'];
            if (is_null($map['size']['x1'])) $map['size']['x1'] = $elf['x'];
            if (is_null($map['size']['x2'])) $map['size']['x2'] = $elf['x'];

            if ($map['size']['y1'] > $elf['y']) $map['size']['y1'] = $elf['y'];
            if ($map['size']['y2'] < $elf['y']) $map['size']['y2'] = $elf['y'];
            if ($map['size']['x1'] > $elf['x']) $map['size']['x1'] = $elf['x'];
            if ($map['size']['x2'] < $elf['x']) $map['size']['x2'] = $elf['x'];
        }
    }

    private function countEmptySpaces(array $map): int
    {
        $result = 0;

        for($y = $map['size']['y1']; $y <= $map['size']['y2']; $y++){
            for($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++){
                $found = count(array_filter($map['elves'], function ($elf) use ($y, $x) {
                    return  $y == $elf['y'] && $x == $elf['x'];
                }));

                if($found == 0) $result++;
            }
        }

        return $result;
    }

    private function draw(array $map): string
    {
        $output = '';

        for ($y = $map['size']['y1']; $y <= $map['size']['y2']; $y++) {

            if (empty($output)) {
                $output .= "\t";
                for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                    $output .= substr($x, 0, 1);
                }
                $output .= "\n";

                if ($x > 9) {
                    $output .= "\t";
                    for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                        if ($x < 10) $output .= ' ';
                        $output .= substr($x, 1, 1);
                    }
                    $output .= "\n";
                }

                if ($x > 99) {
                    $output .= "\t";
                    for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                        if ($x < 100) $output .= ' ';
                        $output .= substr($x, 2, 1);
                    }
                    $output .= "\n";
                }

                $output .= "\n";
            }

            $output .= $y . "\t";

            for ($x = $map['size']['x1']; $x <= $map['size']['x2']; $x++) {
                $isElf = array_filter($map['elves'], fn($value) => $value['y'] == $y && $value['x'] == $x);

                if (!empty($isElf)) {
//                    $output .= reset($isElf)['id'];
                    $output .= '#';
                } else $output .= '.';
            }

            $output .= "\n";
        }

        return $output;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        $this->map['size'] = ['y1' => 0, 'y2' => 0, 'x1' => 0, 'x2' => 0];
        $id = 0;

        foreach ($list as $y => $line) {
            $line = trim($line);
            $cells = str_split($line);

            for ($x = 0; $x < count($cells); $x++) {
                $cell = $cells[$x];

                if ($cell == '#') {
                    $this->map['elves'][] = [
                        'id' => $id,
                        'y' => $y,
                        'x' => $x,
                        'look' => 0,
                        'move' => null,
                        'skip' => false
                    ];

                    if ($this->map['size']['y2'] < $y) $this->map['size']['y2'] = $y;
                    if ($this->map['size']['x2'] < $x) $this->map['size']['x2'] = $x;
                    $id++;
                }
            }
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dh %dm %ds', $diff->h, $diff->i, $diff->s);
    }
}
