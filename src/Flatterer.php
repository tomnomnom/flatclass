<?php
namespace Flatclass;

class Flatterer {
    protected $target;
    
    public function __construct($target){
        $this->target = $target;
    }

    function flatten($name = null){
        $rc = new \ReflectionClass($this->target);
        $out = '';

        // Namespace
        $namespace = $rc->getNamespaceName();
        if ($namespace){
            $out .= "namespace {$namespace};\n";
        }

        // Class start
        if (!isset($name)){
            $name = $rc->getShortName();
        }
        $out .= "class {$name} ";

        // Parent class
        $pc = $rc->getParentClass();
        if ($pc){
            $out .= "extends \\{$pc->getName()} ";
        }

        // Interfaces
        $interfaces = $rc->getInterfaces();
        if (sizeOf($interfaces) > 0){
            $out .= "implements ";
            $out .= implode(',', array_map(function($interface){
                return '\\'.$interface->getName();
            }, $interfaces));
            $out .= ' ';
        }

        $out .= "{\n\n";

        // Properties
        $defaults = $rc->getDefaultProperties();
        foreach ($rc->getProperties() as $property){
            $out .= '    '.$this->getPropertySource($property, $defaults).PHP_EOL;
        }
        $out .= PHP_EOL;

        // Methods
        foreach ($rc->getMethods() as $method){
            $out .= $this->getMethodSource($method).PHP_EOL;
        }

        // Class end
        $out .= "}";

        return $out;
    }

    protected function getMethodSource(\ReflectionMethod $method){
        $fileSource = file($method->getFilename()); 
        $len = $method->getEndLine() - $method->getStartLine() + 1;
        $lines = array_slice($fileSource, $method->getStartLine()-1, $len);

        // Needs a bit of work
        //array_unshift($lines, $method->getDocComment().PHP_EOL);

        // Annotate where methods came from
        array_unshift($lines, "    // From class {$method->getDeclaringClass()->getName()}\n");

        return implode('', $lines);
    }

    protected function getPropertySource(\ReflectionProperty $p, array $defaults){
        $o = '';
        if ($p->isPrivate()){
            $o .= 'private ';
        } else if ($p->isProtected()){
            $o .= 'protected ';
        } else {
            $o .= 'public ';
        }

        if ($p->isStatic()){
            $o .= 'static ';
        }

        $o .= '$'.$p->getName();

        if (isset($defaults[$p->getName()])){
            $val = $defaults[$p->getName()];
            $o .= ' = '.var_export($val, true);
        }

        $o .= ';';

        return $o;
    }

}

