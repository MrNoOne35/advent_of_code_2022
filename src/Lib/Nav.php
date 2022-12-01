<?php

namespace App\Lib;

use Smarty;

class Nav {

    const DAYS = 25;
    const PUZZLES = 2;

    public function __construct(private Smarty $smarty)
    {

    }

    public function init()
    {
        $days = [];

        for($d = 1; $d <= self::DAYS; $d++){
            $dayPath = sprintf('public/puzzles/day%d', $d);
            if(is_readable($dayPath)){
                $day = [
                    'url' => sprintf('%s?day=%d', APP_URL, $d)
                ];

                for($p = 1; $p <= self::PUZZLES; $p++){
                    $puzzlePath = sprintf('public/puzzles/day%d/puzzle%d', $d, $p);

                    if(is_readable($puzzlePath)){
                        $day['puzzles'][$p] = sprintf('%s?day=%d&puzzle=%d', APP_URL, $d, $p);
                    }
                }

                $days[$d] = $day;
            }
        }

        $this->smarty->assign('days', $days);
    }

}