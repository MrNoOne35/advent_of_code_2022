<?php

namespace App;

use App\Controllers\Day;
use App\Controllers\Errors;
use App\Controllers\Index;
use Smarty;

class App {

    private array $errors = [];
    private ?int $day = null;
    private ?string $input = null;
    private $view = null;
    private Smarty $smarty;

    function __construct()
    {
        $this->initInputs();
        $this->initRouter();
        $this->initSmarty();
        $this->initLoad();
        $this->initVars();
    }

    private function initInputs(){
        if(!defined('APP_PUZZLES_PATH')) {
            $this->errors[] = 'You need to define puzzle input path in index.php.';
        } else {
            if(!is_readable(APP_PUZZLES_PATH)){
                $this->errors[] = sprintf('Puzzle inputs path "%s" is not readible', APP_PUZZLES_PATH);
            }
        }

        if(!defined('APP_URL')) {
            $this->errors[] = 'You need to define app url in index.php.';
        }


    }

    private function initRouter()
    {
        if(isset($_GET['day']) && !empty($_GET['day']) && is_numeric($_GET['day'])){
            $this->day = intval($_GET['day']);
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
        if(!empty($this->errors)){
            $this->view = new Errors($this->smarty);
        }
        else {
            if($this->day){
                $this->view = new Day($this->smarty, $this->day, $this->input);
                return;
            }

            $this->view = new Index($this->smarty);
        }
    }

    private function initVars(){
        $this->smarty->assign('param_day', $this->day);
        $this->smarty->assign('param_input', $this->input);
        $this->smarty->assign('param_errors', $this->errors);
    }

    public function run()
    {
        $this->view->start();
    }

}