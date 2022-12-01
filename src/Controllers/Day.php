<?php

namespace App\Controllers;

use App\Interfaces\SolutionInterface;
use App\Interfaces\StartInterface;
use App\Lib\Inputs;
use App\Lib\Nav;
use Exception;
use Smarty;
use SmartyException;

class Day implements StartInterface
{
    private Smarty $smarty;
    private ?int $day = null;
    private ?int $puzzle = null;
    private ?string $input = null;
    private Nav $nav;
    private Inputs $inputs;

    public function __construct(
        Smarty $smarty,
        int $day = null,
        int $puzzle = null,
        string $input = null
    )
    {
        $this->smarty = $smarty;
        $this->day = $day;
        $this->puzzle = $puzzle;
        $this->input = $input;
        $this->nav = new Nav($smarty);
        $this->inputs = new Inputs($smarty, $day, $puzzle);

        $this->nav->init();
        $this->inputs->init();
    }

    /**
     * @throws SmartyException
     * @throws Exception
     */
    public function start()
    {
        if($this->day && $this->puzzle && $this->input){
            $classname = sprintf('App\Solutions\Day%dPuzzle%d', $this->day, $this->puzzle);
            $puzzleinput = sprintf('public/puzzles/day%d/puzzle%d/%s.txt', $this->day, $this->puzzle, $this->input);

            if(!class_exists($classname)){
                throw new Exception(sprintf('There is no solution with class name %s', $classname));
            }

            if(!is_readable($puzzleinput)){
                throw new Exception(sprintf('There is no input (%s) for solution %s', $puzzleinput, $classname));
            }

            /** @var SolutionInterface $solution */
            $solution = new $classname($puzzleinput);
            $result = $solution->execute();

            $this->smarty->assign('result', $result);
        }

        $this->smarty->display('Day.tpl');
    }
}