<?php

namespace App\Lib;

use Smarty;

class Inputs {

    private Smarty $smarty;
    private ?int $day = null;
    private ?int $puzzle = null;

    public function __construct(
        Smarty $smarty,
        int $day = null,
        int $puzzle = null
    )
    {
        $this->smarty = $smarty;
        $this->day = $day;
        $this->puzzle = $puzzle;
    }

    public function init()
    {
        $inputs = [];

        if($this->day && $this->puzzle){
            $path = sprintf('public/puzzles/day%d/puzzle%d', $this->day, $this->puzzle);
            $files = array_diff(scandir($path), array('..', '.'));

            foreach($files as $file){
                $filepath = $path.'/'.$file;
                $info = pathinfo($filepath);
                $inputs[] = [
                    'name' => $info['filename'],
                    'url' => sprintf('%s?day=%d&puzzle=%d&input=%s', APP_URL, $this->day, $this->puzzle, $info['filename'])
                ];
            }
        }

        $this->smarty->assign('inputs', $inputs);
    }

}