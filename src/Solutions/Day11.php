<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

class Day11 implements SolutionInterface
{

    private string $inputPath;
    private array $data;
    private int $bigModulo = 0;

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

        return $this->getSolution(20);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function puzzle2(): ?string
    {
        return $this->getSolution(10000, false);
    }

    /**
     * @throws Exception
     */
    private function getSolution(int $rounds, bool $divider = true): int
    {
        $monkeys = $this->data;

        for ($r = 0; $r < $rounds; $r++) {
            foreach ($monkeys as $number => &$monkey) {
                foreach ($monkey['items'] as $itemKey => $item) {
                    $item = $this->getOperationResult($item, $monkey['operation'], $divider);

                    $target = ($item % $monkey['test'] == 0) ? $monkey['result'][1] : $monkey['result'][0];

                    $monkeys[$target]['items'][] = $item;

                    unset($monkeys[$number]['items'][$itemKey]);

                    $monkeys[$number]['inspected']++;
                }
            }
        }

        usort($monkeys, function ($a, $b) {
            return $b['inspected'] <=> $a['inspected'];
        });

        return $monkeys[0]['inspected'] * $monkeys[1]['inspected'];
    }

    /**
     * @throws Exception
     */
    private function getOperationResult(float $item, array $operation, bool $divider = true): float|int
    {
        if ($operation['left'] == 'old') $operation['left'] = $item;
        if ($operation['right'] == 'old') $operation['right'] = $item;

        $result = match ($operation['sign']) {
            '+' => ($divider) ? floor(($operation['left'] + $operation['right']) / 3) : $operation['left'] + $operation['right'],
            '*' => ($divider) ? floor(($operation['left'] * $operation['right']) / 3) : $operation['left'] * $operation['right'],
            default => throw new Exception(sprintf('Sign %s not supported', $operation['sign'])),
        };

        if (!$divider) {
            $result = $result % $this->bigModulo;
        }

        return $result;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);
        $monkey = null;

        foreach ($list as $line) {
            $line = trim($line);

            preg_match_all('/^Monkey (\d+):/', $line, $matches);
            if (!empty($matches[1])) $monkey = intval($matches[1][0]);

            preg_match_all('/^Starting items: (.*?)$/', $line, $matches);
            if (!empty($matches[1])) $this->data[$monkey]['items'] = explode(', ', $matches[1][0]);

            preg_match_all('/^Operation: new = (.*?)$/', $line, $matches);
            if (!empty($matches[1])) {
                list($this->data[$monkey]['operation']['left'], $this->data[$monkey]['operation']['sign'], $this->data[$monkey]['operation']['right']) = explode(' ', $matches[1][0]);
            }

            preg_match_all('/^Test: divisible by (\d+)$/', $line, $matches);
            if (!empty($matches[1])) $this->data[$monkey]['test'] = $matches[1][0];

            preg_match_all('/^If true: throw to monkey (\d+)$/', $line, $matches);
            if (!empty($matches[1])) $this->data[$monkey]['result'][1] = $matches[1][0];

            preg_match_all('/^If false: throw to monkey (\d+)$/', $line, $matches);
            if (!empty($matches[1])) $this->data[$monkey]['result'][0] = $matches[1][0];

            $this->data[$monkey]['inspected'] = 0;
        }

        $this->bigModulo = array_product(array_column($this->data, 'test'));
    }
}
