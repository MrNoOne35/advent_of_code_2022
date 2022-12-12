<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;
use JetBrains\PhpStorm\Pure;

class Day12 implements SolutionInterface
{
    const DUNGEON_ENTRY_SIGN = 'S';
    const DUNGEON_EXIT_SIGN = 'E';

    private string $inputPath;
    private array $data;
    private array $signs;
    private array $doors = [
        [-1, 0], // up
        [1, 0], // down
        [0, -1], // left
        [0, 1]  // right
    ];

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
        $dungeon = $this->data;
        $looted = array_fill(0, count($this->data), array_fill(0, count($this->data[0]), false));
        $path = [];
        $entryRoom = $this->getDungeonEntry($dungeon);

        $looted[$entryRoom[0]][$entryRoom[1]] = true;
        $path[][] = $entryRoom;

        return $this->findPath($dungeon, $path, $looted);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function puzzle2(): ?string
    {
        $dungeon = $this->data;
        $looted = array_fill(0, count($this->data), array_fill(0, count($this->data[0]), false));
        $path = [];
        $entryRoom = $this->getDungeonExit($dungeon);

        $looted[$entryRoom[0]][$entryRoom[1]] = true;
        $path[][] = $entryRoom;

        return $this->findPath($dungeon, $path, $looted, true);
    }

    /**
     * @throws Exception
     */
    private function getDungeonEntry(array $dungeon): ?array
    {
        return $this->getFirstRoomBySign($dungeon, self::DUNGEON_ENTRY_SIGN);
    }

    /**
     * @throws Exception
     */
    private function getDungeonExit(array $dungeon): ?array
    {
        return $this->getFirstRoomBySign($dungeon, self::DUNGEON_EXIT_SIGN);
    }

    /**
     * @throws Exception
     */
    private function getFirstRoomBySign(array $dungeon, string $sign): array
    {
        foreach($dungeon as $row => $rows){
            foreach($rows as $col => $room){
                if($room === $sign){
                    return [$row, $col];
                }
            }
        }

        throw new Exception(sprintf('Room with sign "%s" not found :(', $sign));
    }

    /**
     * @throws Exception
     */
    private function findPath(array $dungeon, array $path, array $looted, bool $reverse = false): int
    {
        $searchRooms = end($path);
        $nextRooms = [];

        foreach($searchRooms as $searchRoom){
            $searchRoomLvl = $this->signs[$dungeon[$searchRoom[0]][$searchRoom[1]]];

            foreach($this->doors as $door){
                $checkRoom = [$searchRoom[0] + $door[0], $searchRoom[1] + $door[1]];

                // Check if next room is in dungeon floor
                if($checkRoom[0] >= 0 && $checkRoom[0] < count($dungeon) && $checkRoom[1] >= 0 && $checkRoom[1] < count($dungeon[0])) {

                    // Check room lvl. If it is to high skip and check another door. This is reversed for part 2 puzzle
                    $checkRoomLvl = $this->signs[$dungeon[$checkRoom[0]][$checkRoom[1]]];

                    if($reverse){
                        if($checkRoomLvl < $searchRoomLvl - 1) continue;

                        // If found entry return
                        if($dungeon[$checkRoom[0]][$checkRoom[1]] === self::DUNGEON_ENTRY_SIGN || $dungeon[$checkRoom[0]][$checkRoom[1]] === 'a') {
                            return count($path);
                        }
                    }
                    else {
                        if($checkRoomLvl > $searchRoomLvl + 1) continue;

                        // If found exit return
                        if($dungeon[$checkRoom[0]][$checkRoom[1]] === self::DUNGEON_EXIT_SIGN) {
                            return count($path);
                        }
                    }

                    // Check if room is looted. If not loot it !!!
                    if(!$looted[$checkRoom[0]][$checkRoom[1]]) {
                        $nextRooms[] = $checkRoom;
                        $looted[$checkRoom[0]][$checkRoom[1]] = true;
                    };
                }
            }
        }

        $path[] = $nextRooms;

        return $this->findPath($dungeon, $path, $looted, $reverse);
    }


    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $line = trim($line);
            $this->data[] = str_split($line);
        }

        $this->signs = array_flip(range('a', 'z'));
        $this->signs['S'] = $this->signs['a'];
        $this->signs['E'] = $this->signs['z'];
    }
}
