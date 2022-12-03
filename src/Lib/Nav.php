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
            $dayPath = sprintf('public/puzzles/%d', $d);
            if(is_readable($dayPath)){
                $day = [
                    'url' => sprintf('%s?day=%d', APP_URL, $d)
                ];

                $days[$d] = $day;
            }
        }

        $this->smarty->assign('days', $days);
    }

}