<?php
namespace Test;

class FlattererTest extends \PHPUnit_Framework_TestCase {
    public function testSimple(){
        $f = new \Flatclass\Flatterer('\Test\ExampleChild'); 
        $code = $f->flatten('FlattenedChild');
        eval($code);
        
        $child = new ExampleChild('Parent Prop', 'Child Prop');
        $fchild = new FlattenedChild('Parent Prop', 'Child Prop');

        $this->assertEquals($child->getChildProperty(), $fchild->getChildProperty());
        $this->assertEquals($child->getParentProperty(), $fchild->getParentProperty());
    }

    public function testOrphan(){
        $f = new \Flatclass\Flatterer('\Test\ExampleOrphan');
        $code = $f->flatten('FlattenedOrphan');
        eval($code);

        $orphan = new FlattenedOrphan("The Prop");
        $this->assertEquals("The Prop", $orphan->property);
    }

}

class ExampleParent {
    public $parentProperty;

    public function __construct($prop){
        $this->parentProperty = $prop;
    }

    public function getParentProperty(){
        return $this->parentProperty;
    }
}

class ExampleChild extends ExampleParent {
    public $childProperty;

    public function __construct($parentProp, $childProp){
        $this->childProperty = $childProp;
        parent::__construct($parentProp);
    }

    public function getChildProperty(){
        return $this->childProperty;
    }
}

class ExampleOrphan {
    public $property;

    public function __construct($prop){
        $this->property = $prop;
    }

    public function getProperty(){
        return $this->property;
    }
}
