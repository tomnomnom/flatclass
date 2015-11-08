<?php
namespace Test;

class FlattererTest extends \PHPUnit_Framework_TestCase {

    protected function fixture(){
        // We can't go on recreating classes for every test now, can we?
        static $child, $fchild;
        if (isset($child) && isset($fchild)){
            return array($child, $fchild);
        }

        $f = new \Flatclass\Flatterer('\Test\ExampleChild'); 
        $code = $f->flatten('FlattenedChild');
        eval($code);
        
        $child = new ExampleChild('Parent Prop', 'Child Prop');
        $fchild = new FlattenedChild('Parent Prop', 'Child Prop');

        return ($child, $fchild);
    }

    public function testProperties(){
        list($child, $fchild) = $this->fixture();

        $this->assertEquals($child->getChildProperty(), $fchild->getChildProperty());
        $this->assertEquals($child->getParentProperty(), $fchild->getParentProperty());
    }

    public function testInterfaces(){
        list($child, $fchild) = $this->fixture();

        $this->assertTrue($child instanceof ExampleInterface);
        $this->assertTrue($fchild instanceof ExampleInterface);
    }

    public function testDefaultProperties(){
        list($child, $fchild) = $this->fixture();

        $this->assertEquals('default', $child->defaultProperty);
        $this->assertEquals('default', $fchild->defaultProperty);
    }

    public function testOrphan(){
        $f = new \Flatclass\Flatterer('\Test\ExampleOrphan');
        $code = $f->flatten('FlattenedOrphan');
        eval($code);

        $orphan = new FlattenedOrphan("The Prop");
        $this->assertEquals("The Prop", $orphan->property);
    }

}

interface ExampleInterface {
    public function getParentProperty();
}

class ExampleParent implements ExampleInterface {
    public $defaultProperty = 'default';
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
