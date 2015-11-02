<?php
class Dog extends Animal {
    public $colour = 'brown';

    public function bark(){
        // I just needed a comment here
        echo "Woof! I'm {$this->getName()}\n";
    }
}
