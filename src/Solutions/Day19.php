<?php

namespace App\Solutions;

use App\Interfaces\SolutionInterface;
use Exception;

// All solutions needs around 4gb of ram :/...
// Part 1 execution time is around 15 seconds
// Part 2 execution time is around 1 minute

class Day19 implements SolutionInterface
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

        $solution = 0;

        $bluePrints = $this->data;

        foreach ($bluePrints as $bluePrint) {
            $solution += $bluePrint['id'] * $this->calculate($bluePrint, 24);
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

        $solution = 1;

        $bluePrints = array_slice($this->data, 0, 3);

        foreach ($bluePrints as $bluePrint) {
            $solution *= $this->calculate($bluePrint, 32);
        }

        return ['solution' => $solution, 'time' => $this->getExecutionTime($startTime)];
    }

    private function calculate(array $bluePrint, int $time)
    {
        // get maximum costs of each ore
        $maxOreCost = max($bluePrint['robotsPrice']['ore']['ore'], $bluePrint['robotsPrice']['clay']['ore'], $bluePrint['robotsPrice']['obsidian']['ore'], $bluePrint['robotsPrice']['geode']['ore']);
        $maxClayCost = max($bluePrint['robotsPrice']['ore']['clay'], $bluePrint['robotsPrice']['clay']['clay'], $bluePrint['robotsPrice']['obsidian']['clay'], $bluePrint['robotsPrice']['geode']['clay']);
        $maxObsidianCost = max($bluePrint['robotsPrice']['ore']['obsidian'], $bluePrint['robotsPrice']['clay']['obsidian'], $bluePrint['robotsPrice']['obsidian']['obsidian'], $bluePrint['robotsPrice']['geode']['obsidian']);

        // set current status
        $status = [
            'robots' => $bluePrint['robots'],
            'resources' => $bluePrint['resources'],
            'time' => $time,
        ];

        $queue = new \SplQueue();
        $queue->enqueue($status);

        $visited = [];
        $result = 0;
        while (count($queue) > 0) {
            $status = $queue->dequeue();

            // get max geode if time finished
            if ($status['time'] == 0) {
                $result = max($result, $status['resources']['geode']);
                continue;
            }

            $status['robots']['ore'] = min($status['robots']['ore'], $maxOreCost);
            $status['robots']['clay'] = min($status['robots']['clay'], $maxClayCost);
            $status['robots']['obsidian'] = min($status['robots']['obsidian'], $maxObsidianCost);

            $status['resources']['ore'] = min($status['resources']['ore'], $status['time'] * $maxOreCost - $status['robots']['ore'] * ($status['time'] - 1));
            $status['resources']['clay'] = min($status['resources']['clay'], $status['time'] * $maxClayCost - $status['robots']['clay'] * ($status['time'] - 1));
            $status['resources']['obsidian'] = min($status['resources']['obsidian'], $status['time'] * $maxObsidianCost - $status['robots']['obsidian'] * ($status['time'] - 1));

            // unique key.
            $key = md5($status['time'] . implode('|', $status['resources']) . implode('|', $status['robots']));
            if (isset($visited[$key])) {
                continue;
            }
            $visited[$key] = true;

            foreach ($this->shopping($status, $bluePrint['robotsPrice']) as $nextStatus) {
                --$nextStatus['time'];
                $nextStatus['resources']['ore'] += $status['robots']['ore'];
                $nextStatus['resources']['clay'] += $status['robots']['clay'];
                $nextStatus['resources']['obsidian'] += $status['robots']['obsidian'];
                $nextStatus['resources']['geode'] += $status['robots']['geode'];

                $queue->enqueue($nextStatus);
            }
        }

        return $result;
    }

    private function shopping(array $status, array $prices): iterable
    {
        // buy geode as soon as we can
        if ($status['resources']['ore'] >= $prices['geode']['ore'] && $status['resources']['obsidian'] >= $prices['geode']['obsidian']) {
            $newStatus = $status;
            $newStatus['resources']['ore'] -= $prices['geode']['ore'];
            $newStatus['resources']['obsidian'] -= $prices['geode']['obsidian'];

            ++$newStatus['robots']['geode'];

            yield $newStatus;
        }

        // if not enough resources for geode try buy obsidian
        if ($status['resources']['ore'] >= $prices['obsidian']['ore'] && $status['resources']['clay'] >= $prices['obsidian']['clay']) {
            $newStatus = $status;
            $newStatus['resources']['ore'] -= $prices['obsidian']['ore'];
            $newStatus['resources']['clay'] -= $prices['obsidian']['clay'];

            ++$newStatus['robots']['obsidian'];

            yield $newStatus;
        }

        // if not enough resources for obsidian try buy clay
        if ($status['resources']['ore'] >= $prices['clay']['ore']) {
            $newStatus = $status;
            $newStatus['resources']['ore'] -= $prices['clay']['ore'];

            ++$newStatus['robots']['clay'];

            yield $newStatus;
        }

        // if not enough resources for clay try buy ore
        if ($status['resources']['ore'] >= $prices['ore']['ore']) {
            $newStatus = $status;
            $newStatus['resources']['ore'] -= $prices['ore']['ore'];

            ++$newStatus['robots']['ore'];

            yield $newStatus;
        }

        yield $status;
    }

    private function prepareData()
    {
        $list = file($this->inputPath);

        foreach ($list as $line) {
            $line = trim($line);

            preg_match('/Blueprint (?<id>.*): Each ore robot costs (?<oreCostOre>.*) ore. Each clay robot costs (?<clayCostOre>.*) ore. Each obsidian robot costs (?<obsidianCostOre>.*) ore and (?<obsidianCostClay>.*) clay. Each geode robot costs (?<geodeCostOre>.*) ore and (?<geodeCostObsidian>.*) obsidian./', $line, $matches);

            $this->data[] = [
                'id' => (int)$matches['id'],
                'robotsPrice' => [
                    'ore' => ['ore' => (int)$matches['oreCostOre'], 'clay' => 0, 'obsidian' => 0, 'geode' => 0],
                    'clay' => ['ore' => (int)$matches['clayCostOre'], 'clay' => 0, 'obsidian' => 0, 'geode' => 0],
                    'obsidian' => ['ore' => (int)$matches['obsidianCostOre'], 'clay' => (int)$matches['obsidianCostClay'], 'obsidian' => 0, 'geode' => 0],
                    'geode' => ['ore' => (int)$matches['geodeCostOre'], 'clay' => 0, 'obsidian' => (int)$matches['geodeCostObsidian'], 'geode' => 0]
                ],
                'robots' => ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0],
                'resources' => ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0]
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
