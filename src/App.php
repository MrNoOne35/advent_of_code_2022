<?php

namespace App;

use App\Controllers\Day;
use App\Controllers\Index;
use Smarty;

class App {

    private ?int $day = null;
    private ?int $puzzle = null;
    private ?string $input = null;
    private $view = null;
    private Smarty $smarty;

    function __construct()
    {
        $this->initRouter();
        $this->initSmarty();
        $this->initLoad();
        $this->initVars();
    }

    private function initRouter()
    {
        if(isset($_GET['day']) && !empty($_GET['day']) && is_numeric($_GET['day'])){
            $this->day = intval($_GET['day']);
        }

        if(isset($_GET['puzzle']) && !empty($_GET['puzzle']) && is_numeric($_GET['puzzle'])){
            $this->puzzle = intval($_GET['puzzle']);
        }

        if(isset($_GET['input']) && !empty($_GET['input'])){
            $this->input = $_GET['input'];
        }
    }

    private function initSmarty(){
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir('./src/Templates');
        $this->smarty->setCacheDir('./tmp/smarty/cache');
        $this->smarty->setCompileDir('./tmp/smarty/compile');
        $this->smarty->setConfigDir('./tmp/smarty/config');
    }

    private function initLoad(){
        if($this->day){
            $this->view = new Day($this->smarty, $this->day, $this->puzzle, $this->input);
            return;
        }

        $this->view = new Index($this->smarty);
    }

    private function initVars(){
        $this->smarty->assign('param_day', $this->day);
        $this->smarty->assign('param_puzzle', $this->puzzle);
        $this->smarty->assign('param_input', $this->input);
    }

    public function run()
    {
        $this->view->start();
    }

}