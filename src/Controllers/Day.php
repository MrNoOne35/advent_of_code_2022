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
    private ?string $input = null;
    private Nav $nav;
    private Inputs $inputs;

    public function __construct(
        Smarty $smarty,
        int $day = null,
        string $input = null
    )
    {
        $this->smarty = $smarty;
        $this->day = $day;
        $this->input = $input;
        $this->nav = new Nav($smarty);
        $this->inputs = new Inputs($smarty, $day);

        $this->nav->init();
        $this->inputs->init();
    }

    /**
     * @throws SmartyException
     * @throws Exception
     */
    public function start()
    {
        if($this->day && $this->input){
            $classname = sprintf('App\Solutions\Day%d', $this->day);
            $puzzleinput = sprintf('public/puzzles/%d/%s.txt', $this->day, $this->input);

            if(!class_exists($classname)){
                throw new Exception(sprintf('There is no solution with class name %s', $classname));
            }

            if(!is_readable($puzzleinput)){
                throw new Exception(sprintf('There is no input (%s) for solution %s', $puzzleinput, $classname));
            }

            /** @var SolutionInterface $solution */
            $solution = new $classname($puzzleinput);
            $puzzle1 = $solution->puzzle1();
            $puzzle2 = $solution->puzzle2();

            $this->smarty->assign('puzzle1', $puzzle1);
            $this->smarty->assign('puzzle2', $puzzle2);
        }

        $this->smarty->display('Day.tpl');
    }
}