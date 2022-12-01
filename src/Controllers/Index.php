<?php

namespace App\Controllers;

use App\Interfaces\StartInterface;
use App\Lib\Nav;
use Smarty;
use SmartyException;

class Index implements StartInterface {

    private Nav $nav;

    public function __construct(private Smarty $smarty)
    {
        $this->nav = new Nav($smarty);
        $this->nav->init();
    }

    /**
     * @throws SmartyException
     */
    public function start()
    {
        $this->smarty->display('Index.tpl');
    }
}