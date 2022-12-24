<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// Part 1 execution time is around 24 seconds
// Part 2 execution time is around 3 seconds

class Day22 implements SolutionInterface
{
    const NOTHING_SIGN = ' ';
    const EMPTY_SIGN = '.';
    const WALL_SIGN = '#';
    const ARROWS = [
        '>', 'v', '<', '^'
    ];
    const TEST_CUBE = [
        0 => [
            'pos' => ['y1' => 0, 'y2' => 3, 'x1' => 8, 'x2' => 11],
            'move' => [
                0 => [
                    'side' => 5,
                    'y' => ['map' => 'y', 'type' => 'reverse'],
                    'x' => ['map' => 'x', 'type' => 'end'],
                    'direction' => 2
                ],
                2 => [
                    'side' => 2,
                    'y' => ['map' => 'x', 'type' => 'normal'],
                    'x' => ['map' => 'y', 'type' => 'start'],
                    'direction' => 1
                ],
                3 => [
                    'side' => 1,
                    'y' => ['map' => 'y', 'type' => 'start'],
                    'x' => ['map' => 'x', 'type' => 'reverse'],
                    'direction' => 1
                ]
            ]
        ],
        1 => [
            'pos' => ['y1' => 4, 'y2' => 7, 'x1' => 0, 'x2' => 3
            ],
            'move' => [
                1 => [
                    'side' => 4,
                    'x' => ['map' => 'x', 'type' => 'reverse'],
                    'y' => ['map' => 'y', 'type' => 'end'],
                    'direction' => 3
                ],
                2 => [
                    'side' => 5,
                    'x' => ['map' => 'y', 'type' => 'end'],
                    'y' => ['map' => 'x', 'type' => 'reverse'],
                    'direction' => 3
                ],
                3 => [
                    'side' => 0,
                    'x' => ['map' => 'x', 'type' => 'reverse'],
                    'y' => ['map' => 'y', 'type' => 'start'],
                    'direction' => 1
                ]
            ]
        ],
        2 => [
            'pos' => ['y1' => 4, 'y2' => 7, 'x1' => 4, 'x2' => 7],
            'move' => [
                1 => [
                    'side' => 4,
                    'x' => ['map' => 'y', 'type' => 'reverse'],
                    'y' => ['map' => 'x', 'type' => 'start'],
                    'direction' => 0
                ],
                3 => [
                    'side' => 0,
                    'x' => ['map' => 'y', 'type' => 'normal'],
                    'y' => ['map' => 'x', 'type' => 'start'],
                    'direction' => 0
                ]
            ]
        ],
        3 => [
            'pos' => [ 'y1' => 4, 'y2' => 7, 'x1' => 8, 'x2' => 11],
            'move' => [
                0 => [
                    'side' => 5,
                    'x' => ['map' => 'y', 'type' => 'start'],
                    'y' => ['map' => 'x', 'type' => 'reverse'],
                    'direction' => 1
                ]
            ]
        ],
        4 => [
            'pos' => ['y1' => 8, 'y2' => 11, 'x1' => 8, 'x2' => 11],
            'move' => [
                1 => [
                    'side' => 1,
                    'x' => ['map' => 'x', 'type' => 'reverse'],
                    'y' => ['map' => 'y', 'type' => 'end'],
                    'direction' => 3
                ],
                2 => [
                    'side' => 2,
                    'x' => ['map' => 'y', 'type' => 'end'],
                    'y' => ['map' => 'x', 'type' => 'reverse'],
                    'direction' => 3
                ]
            ]
        ],
        5 => [
            'pos' => [
                'y1' => 8,
                'y2' => 11,
                'x1' => 12,
                'x2' => 15
            ],
            'move' => [
                0 => [
                    'side' => 0,
                    'x' => ['map' => 'x', 'type' => 'end'],
                    'y' => ['map' => 'y', 'type' => 'reverse'],
                    'direction' => 2
                ],
                1 => [
                    'side' => 1,
                    'x' => ['map' => 'y', 'type' => 'reverse'],
                    'y' => ['map' => 'x', 'type' => 'start'],
                    'direction' => 0
                ],
                3 => [
                    'side' => 3,
                    'x' => ['map' => 'y', 'type' => 'reverse'],
                    'y' => ['map' => 'x', 'type' => 'end'],
                    'direction' => 2
                ]
            ]
        ]
    ];

    const INPUT_CUBE = [
        0 => [
            'pos' => ['y1' => 0, 'y2' => 49, 'x1' => 50, 'x2' => 99],
            'move' => [
                2 => [
                    'side' => 3,
                    'y' => ['map' => 'y', 'type' => 'reverse'],
                    'x' => ['map' => 'x', 'type' => 'start'],
                    'direction' => 0
                ],
                3 => [
                    'side' => 5,
                    'y' => ['map' => 'x', 'type' => 'start'],
                    'x' => ['map' => 'y', 'type' => 'normal'],
                    'direction' => 0
                ]
            ]
        ],
        1 => [
            'pos' => ['y1' => 0, 'y2' => 49, 'x1' => 100, 'x2' => 149],
            'move' => [
                0 => [
                    'side' => 4,
                    'x' => ['map' => 'x', 'type' => 'end'],
                    'y' => ['map' => 'y', 'type' => 'reverse'],
                    'direction' => 2
                ],
                1 => [
                    'side' => 2,
                    'x' => ['map' => 'y', 'type' => 'normal'],
                    'y' => ['map' => 'x', 'type' => 'end'],
                    'direction' => 2
                ],
                3 => [
                    'side' => 5,
                    'x' => ['map' => 'x', 'type' => 'normal'],
                    'y' => ['map' => 'y', 'type' => 'end'],
                    'direction' => 3
                ]
            ]
        ],
        2 => [
            'pos' => ['y1' => 50, 'y2' => 99, 'x1' => 50, 'x2' => 99],
            'move' => [
                0 => [
                    'side' => 1,
                    'x' => ['map' => 'y', 'type' => 'end'],
                    'y' => ['map' => 'x', 'type' => 'normal'],
                    'direction' => 3
                ],
                2 => [
                    'side' => 3,
                    'x' => ['map' => 'y', 'type' => 'start'],
                    'y' => ['map' => 'x', 'type' => 'normal'],
                    'direction' => 1
                ]
            ]
        ],
        3 => [
            'pos' => [ 'y1' => 100, 'y2' => 149, 'x1' => 0, 'x2' => 49],
            'move' => [
                2 => [
                    'side' => 0,
                    'x' => ['map' => 'x', 'type' => 'start'],
                    'y' => ['map' => 'y', 'type' => 'reverse'],
                    'direction' => 0
                ],
                3 => [
                    'side' => 2,
                    'x' => ['map' => 'y', 'type' => 'normal'],
                    'y' => ['map' => 'x', 'type' => 'start'],
                    'direction' => 0
                ]
            ]
        ],
        4 => [
            'pos' => ['y1' => 100, 'y2' => 149, 'x1' => 50, 'x2' => 99],
            'move' => [
                0 => [
                    'side' => 1,
                    'x' => ['map' => 'x', 'type' => 'end'],
                    'y' => ['map' => 'y', 'type' => 'reverse'],
                    'direction' => 2
                ],
                1 => [
                    'side' => 5,
                    'x' => ['map' => 'y', 'type' => 'normal'],
                    'y' => ['map' => 'x', 'type' => 'end'],
                    'direction' => 2
                ]
            ]
        ],
        5 => [
            'pos' => ['y1' => 150, 'y2' => 199, 'x1' => 0, 'x2' => 49],
            'move' => [
                0 => [
                    'side' => 4,
                    'x' => ['map' => 'y', 'type' => 'end'],
                    'y' => ['map' => 'x', 'type' => 'normal'],
                    'direction' => 3
                ],
                1 => [
                    'side' => 1,
                    'x' => ['map' => 'x', 'type' => 'normal'],
                    'y' => ['map' => 'y', 'type' => 'start'],
                    'direction' => 1
                ],
                2 => [
                    'side' => 0,
                    'x' => ['map' => 'y', 'type' => 'start'],
                    'y' => ['map' => 'x', 'type' => 'normal'],
                    'direction' => 1
                ]
            ]
        ]
    ];

    private array $map = [
        'width' => 0,
        'height' => 0,
        'grid' => [],
        'commands' => []
    ];
    private array $directions = [
        ['y' => 0, 'x' => 1], // y,x right
        ['y' => 1, 'x' => 0], // y,x down
        ['y' => 0, 'x' => -1], // y,x left
        ['y' => -1, 'x' => 0] // y,x up
    ];
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

        $map = $this->map;
        $start = ['y' => array_key_first($map['grid']), 'x' => array_key_first($map['grid'][0])];

        if($this->inputPath == 'public/puzzles/22/input.txt') $test = false;
        else $test = true;

        $path = $this->getPath($map, $start, false, $test);

        $solution = (($path['last']['position']['y'] + 1) * 1000) + (($path['last']['position']['x'] + 1) * 4) + $path['last']['direction'];

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime), 'draw' => $this->draw($map, $path)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        $map = $this->map;
        $start = ['y' => array_key_first($map['grid']), 'x' => array_key_first($map['grid'][0])];

        if($this->inputPath == 'public/puzzles/22/input.txt') $test = false;
        else $test = true;

        $path = $this->getPath($map, $start, true, $test);

        $solution = (($path['last']['position']['y'] + 1) * 1000) + (($path['last']['position']['x'] + 1) * 4) + $path['last']['direction'];

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime), 'draw' => $this->draw($map, $path)];
    }

    private function getPath(array $map, array $position, bool $part2 = false, bool $test = false){
        $path = [
            'history' => [],
            'last' => [
                'position' => [],
                'direction' => null
            ]
        ];

        $cube = ($test) ? self::TEST_CUBE : self::INPUT_CUBE;

        foreach ($map['commands'] as $command) {
            if(!isset($command['dir'])) $dir = 0;
            else {
                if($command['dir'] === 'R') $dir++;
                else $dir--;

                if($dir > 3) $dir = 0;
                if($dir < 0) $dir = 3;
            }

            for($i = 0; $i < $command['moves']; $i++){
                $nextY = $position['y'] + $this->directions[$dir]['y'];
                $nextX = $position['x'] + $this->directions[$dir]['x'];
                $nextDir = null;

                if (!isset($map['grid'][$nextY][$nextX])) {
                    if($part2){
                        $currentSide = $this->getCurrentSide($position, $cube);
                        $move = $currentSide['move'][$dir];
                        $nextSide = $cube[$move['side']];

                        if(isset($move['direction'])) $nextDir = $move['direction'];

                        if(isset($move['y'])) {
                            if($move['y']['map'] == 'y'){
                                if($move['y']['type'] == 'start'){
                                    $nextY = $nextSide['pos']['y1'];
                                }
                                if($move['y']['type'] == 'end'){
                                    $nextY = $nextSide['pos']['y2'];
                                }
                                if($move['y']['type'] == 'normal'){
                                    $diff = $position['y'] - $currentSide['pos']['y1'];
                                    $nextY = $nextSide['pos']['y1'] + $diff;
                                }
                                if($move['y']['type'] == 'reverse'){
                                    $diff = $position['y'] - $currentSide['pos']['y1'];
                                    $nextY = $nextSide['pos']['y2'] - $diff;
                                }
                            }

                            if($move['y']['map'] == 'x'){
                                if($move['y']['type'] == 'start') {
                                    $nextX = $nextSide['pos']['x1'];
                                }
                                if($move['y']['type'] == 'end') {
                                    $nextX = $nextSide['pos']['x2'];
                                }
                                if($move['y']['type'] == 'normal') {
                                    $diff = $position['y'] - $currentSide['pos']['y1'];
                                    $nextX = $nextSide['pos']['x1'] + $diff;
                                }
                                if($move['y']['type'] == 'reverse') {
                                    $diff = $position['y'] - $currentSide['pos']['y1'];
                                    $nextX = $nextSide['pos']['x2'] - $diff;
                                }
                            }
                        }

                        if(isset($move['x'])) {
                            if($move['x']['map'] == 'x'){
                                if($move['x']['type'] == 'start'){
                                    $nextX = $nextSide['pos']['x1'];
                                }
                                if($move['x']['type'] == 'end'){
                                    $nextX = $nextSide['pos']['x2'];
                                }
                                if($move['x']['type'] == 'normal'){
                                    $diff = $position['x'] - $currentSide['pos']['x1'];
                                    $nextX = $nextSide['pos']['x1'] + $diff;
                                }
                                if($move['x']['type'] == 'reverse'){
                                    $diff = $position['x'] - $currentSide['pos']['x1'];
                                    $nextX = $nextSide['pos']['x2'] - $diff;
                                }
                            }

                            if($move['x']['map'] == 'y'){
                                if($move['x']['type'] == 'start'){
                                    $nextY = $nextSide['pos']['y1'];
                                }
                                if($move['x']['type'] == 'end'){
                                    $nextY = $nextSide['pos']['y2'];
                                }
                                if($move['x']['type'] == 'normal'){
                                    $diff = $position['x'] - $currentSide['pos']['x1'];
                                    $nextY = $nextSide['pos']['y1'] + $diff;
                                }
                                if($move['x']['type'] == 'reverse'){
                                    $diff = $position['x'] - $currentSide['pos']['x1'];
                                    $nextY = $nextSide['pos']['y2'] - $diff;
                                }
                            }
                        }
                    }
                    else {
                        if ($dir == 0) $nextX = array_key_first($map['grid'][$nextY]);
                        if ($dir == 2) $nextX = array_key_last($map['grid'][$nextY]);

                        if ($dir == 1) {
                            $nextY = array_key_first(array_filter($map['grid'], function ($value) use ($nextX) {
                                return isset($value[$nextX]);
                            }, ARRAY_FILTER_USE_BOTH));
                        }
                        if ($dir == 3) {
                            $nextY = array_key_first(array_filter(array_reverse($map['grid'], true), function ($value) use ($nextX) {
                                return isset($value[$nextX]);
                            }, ARRAY_FILTER_USE_BOTH));
                        }
                    }
                }

                if (isset($map['grid'][$nextY][$nextX])) {
                    $path['history'][$position['y']][$position['x']] = self::ARROWS[$dir];

                    $cell = $map['grid'][$nextY][$nextX];

                    if ($cell == self::EMPTY_SIGN) {
                        $position['y'] = $nextY;
                        $position['x'] = $nextX;

                        if(!is_null($nextDir)) $dir = $nextDir;

                        $path['history'][$position['y']][$position['x']] = self::ARROWS[$dir];
                        $path['last']['position'] = $position;
                        $path['last']['direction'] = $dir;
                    } else if ($cell == self::WALL_SIGN) {
                        $path['last']['direction'] = $dir;
                        break;
                    }
                }
            }
        }

        return $path;
    }

    private function getCurrentSide(array $position, array $cube) {
        foreach($cube as $cubeData){
            if($position['y'] >= $cubeData['pos']['y1'] && $position['y'] <= $cubeData['pos']['y2'] && $position['x'] >= $cubeData['pos']['x1'] && $position['x'] <= $cubeData['pos']['x2']) {
                return $cubeData;
            }
        }

        return null;
    }

    private function draw(array $map, array $path = []): string
    {
        $output = '';

        for ($y = 0; $y < $map['height']; $y++) {

            if (empty($output)) {
                $output .= "\t";
                for ($x = 0; $x <= $map['width']; $x++) {
                    $output .= substr($x, 0, 1);
                }
                $output .= "\n";

                if ($map['width'] > 9) {
                    $output .= "\t";
                    for ($x = 0; $x <= $map['width']; $x++) {
                        if ($x < 10) $output .= ' ';
                        $output .= substr($x, 1, 1);
                    }
                    $output .= "\n";
                }

                if ($map['width'] > 99) {
                    $output .= "\t";
                    for ($x = 0; $x <= $map['width']; $x++) {
                        if ($x < 100) $output .= ' ';
                        $output .= substr($x, 2, 1);
                    }
                    $output .= "\n";
                }

                $output .= "\n";
            }

            $output .= $y . "\t";

            for ($x = 0; $x <= $map['width']; $x++) {
                if (!isset($map['grid'][$y][$x])) $output .= self::NOTHING_SIGN;
                else {
                    if (isset($path['history'][$y][$x])) {
                        $output .= $path['history'][$y][$x];
                    } else $output .= $map['grid'][$y][$x];
                }
            }

            $output .= "\n";
        }

        return $output;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $line = trim($line, "\n\r");

            if (empty($line)) continue;
            if (is_numeric(substr($line, 0, 1))) {
                preg_match_all('/(\d+)|([A-Z])|(\d+)/', $line, $matches);

                $index = 0;

                foreach ($matches[0] as $command) {
                    switch ($command) {
                        case 'R':
                        case 'L':
                            $index++;
                            $this->map['commands'][$index]['dir'] = $command;
                            break;
                        default:
                            $this->map['commands'][$index]['moves'] = $command;
                            break;
                    }
                }

                continue;
            }
            $row = str_split($line);
            $row = array_filter($row, fn($v) => $v != ' ');

            $width = array_key_last($row) + 1;

            if ($width > $this->map['width']) $this->map['width'] = $width;
            $this->map['height']++;

            $this->map['grid'][] = $row;
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dm %ds', $diff->i, $diff->s);
    }
}
