<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day16 implements SolutionInterface
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

        // get all distances to rooms where there is preasure only
        $distances = [];
        foreach ($this->data as $startRoom => $startRoomData) {
            foreach ($this->data as $endRoom => $endRoomData) {
                if (($startRoom === 'AA' || $startRoomData['rate'] > 0) && $startRoom !== $endRoom) {
                    if ($endRoomData['rate'] > 0) {
                        $distances[$startRoom][$endRoom] = count($this->bfs($this->data, $startRoom, $endRoom)) - 1;
                    }
                }
            }
        }

        // get all rooms with valves that have preasure
        $roomsWithValves = [];
        foreach ($this->data as $roomName => $roomData) {
            if ($roomData['rate'] > 0) $roomsWithValves[] = $roomName;
        }

        // get all possibilities
        $possibilities = $this->getPossibilities($this->data, $distances, 'AA', 30, $roomsWithValves);

        // sum all pssibilities data
        $solution = 0;
        foreach ($possibilities as $possibility) {
            $sum = array_sum($possibility);
            if ($solution < $sum) $solution = $sum;
        }

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function puzzle2(): ?array
    {
        $startTime = new \DateTime();

        // get all distances to rooms where there is preasure only
        $distances = [];
        foreach ($this->data as $startRoom => $startRoomData) {
            foreach ($this->data as $endRoom => $endRoomData) {
                if (($startRoom === 'AA' || $startRoomData['rate'] > 0) && $startRoom !== $endRoom) {
                    if ($endRoomData['rate'] > 0) {
                        $distances[$startRoom][$endRoom] = count($this->bfs($this->data, $startRoom, $endRoom)) - 1;
                    }
                }
            }
        }

        // get all rooms with valves that have preasure
        $roomsWithValves = [];
        foreach ($this->data as $roomName => $roomData) {
            if ($roomData['rate'] > 0) $roomsWithValves[] = $roomName;
        }

        // get all possibilities
        $points = [];
        $possibilities = $this->getPossibilities($this->data, $distances, 'AA', 26, $roomsWithValves);

        // Get max values only of the same paths
        foreach ($possibilities as $possibility) {
            if (!empty($possibility)) {
                $keys = array_keys($possibility);
                sort($keys);
                $key = implode(',', $keys);
                $sum = array_sum($possibility);

                if (!isset($points[$key])) $points[$key] = 0;
                $points[$key] = max($sum, $points[$key]);
            }
        }

        // compare user and elephant
        $solution = 0;
        foreach (array_keys($points) as $userPath) {
            foreach (array_keys($points) as $elephantPath) {
                $path = [];

                $userList = explode(',', $userPath);
                foreach ($userList as $room) {
                    if (!in_array($room, $path)) $path[] = $room;
                }

                $elephantList = explode(',', $elephantPath);
                foreach ($elephantList as $room) {
                    if (!in_array($room, $path)) $path[] = $room;
                }

                if (count($path) === (count($userList) + count($elephantList))) {
                    $solution = max($points[$userPath] + $points[$elephantPath], $solution);
                }
            }
        }

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function getPossibilities(array $rooms, array $distances, string $room, int $time, array $roomsWithValves, array $path = []): array
    {
        $result = [$path];

        foreach ($roomsWithValves as $roomIndex => $nextRoom) {
            $nextTime = $time - $distances[$room][$nextRoom] - 1;

            if ($nextTime > 0) {
                $newPath = $path;
                $newPath[$nextRoom] = $nextTime * $rooms[$nextRoom]['rate'];

                $newRoomsWithValves = $roomsWithValves;
                unset($newRoomsWithValves[$roomIndex]);

                $result = array_merge($result, $this->getPossibilities($rooms, $distances, $nextRoom, $nextTime, $newRoomsWithValves, $newPath));
            }
        }

        return $result;
    }

    // BFS for path finding

    /**
     * @param array $rooms
     * @param string $start
     * @param string $end
     * @return array|string[]|void|null
     */
    private function bfs(array $rooms, string $start, string $end)
    {
        $queue = [];
        $visited = [];
        $visited[] = $start;

        if ($start == $end) return [$start];

        $queue[] = [$start];

        while (count($queue) > 0) {
            $path = array_shift($queue);
            $node = $path[count($path) - 1];

            foreach ($rooms[$node]['connected'] as $room) {
                if (in_array($room, $visited)) continue;

                if ($room === $end) return array_merge($path, [$room]);

                $visited[] = $room;

                $queue[] = array_merge($path, [$room]);
            }
        }
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $line = trim($line);

            preg_match_all('/^Valve ([A-Z]{2}) has flow rate=(\d+); (.*?) (.*?) to (.*?) (.*?)$/', $line, $matches);
            $this->data[$matches[1][0]] = [
                'rate' => $matches[2][0],
                'connected' => explode(', ', $matches[6][0])
            ];
        }
    }

    private function getExecutionTime(\DateTime $startTime): string
    {
        $endTime = new \DateTime();
        $diff = $startTime->diff($endTime);
        return sprintf('%dm %ds', $diff->i, $diff->s);
    }
}
