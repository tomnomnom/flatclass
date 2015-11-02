<?php
abstract class Animal implements Nameable {
    protected $name;

    public function __construct($name){
        $this->name = $name; 
    }

    // Get the animal's name
    public function getName(){
        return $this->name;
    }
}

interface Nameable {
    public function getName();
}
