<?php
class Reports extends Controller{
    public function index(\Base $f3){
        $f3->set('template','pages/home.htm');
    }
}