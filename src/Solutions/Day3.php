<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day3 implements SolutionInterface {

    private string $inputPath;
    private array $data = [];

    private array $characters;

    public function __construct(string $inputPath)
    {
        $this->inputPath = $inputPath;
        $this->prepareData();
        $this->prepareLetters();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function puzzle1(): ?int
    {
        $solution = 0;

        foreach($this->data['puzzle1'] as $items){
            $character = $this->getSameCharacter($items['left'], $items['right']);

            if(!$character){
                throw new Exception('Character not found');
            }

            $solution += $this->characters[$character];
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

        foreach($this->data['puzzle2'] as $groups){
            $character = $this->getSameCharacterInGroup($groups);

            if(!$character){
                throw new Exception('Character not found');
            }

            $solution += $this->characters[$character];
        }

        return $solution;
    }

    /**
     * @param string $left
     * @param string $right
     * @return string|null
     */
    private function getSameCharacter(string $left, string $right): ?string
    {
        $inputArray = str_split($left);

        foreach ($inputArray as $character){
            $pos = strpos($right, $character);

            if($pos !== false){
                return $character;
            }
        }

        return null;
    }

    /**
     * @param array $groups
     * @return string|null
     */
    private function getSameCharacterInGroup(array $groups): ?string
    {
        $inputArray = str_split($groups[0]);

        foreach ($inputArray as $character){
            $pos1 = strpos($groups[1], $character);
            $pos2 = strpos($groups[2], $character);

            if($pos1 !== false && $pos2 !== false){
                return $character;
            }
        }

        return null;
    }

    private function prepareLetters(){
        // Handy php range function create arrays of letters from a to Z. First element is dummy element to remove zero index in next step.
        $this->characters = array_merge([0], range('a', 'z'), range('A', 'Z'));
        // Remove dummy index 0
        unset($this->characters[0]);
        // Flip array to have letter index and score as value.
        $this->characters = array_flip($this->characters);
    }

    private function prepareData()
    {
        $input = file($this->inputPath);

        foreach($input as $key => $entry){
            $length = strlen(trim($entry)) / 2;
            $this->data['puzzle1'][$key]['left'] = substr($entry, 0, $length);
            $this->data['puzzle1'][$key]['right'] = substr($entry, $length);
            $this->data['puzzle2'][floor($key / 3)][] = trim($entry);
        }
    }
}
