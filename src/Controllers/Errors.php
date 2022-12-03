<?php

namespace App\Controllers;

use App\Interfaces\StartInterface;
use Smarty;
use SmartyException;

class Errors implements StartInterface {

    public function __construct(private Smarty $smarty)
    {

    }

    /**
     * @throws SmartyException
     */
    public function start()
    {
        $this->smarty->display('Errors.tpl');
    }
}