<?php
require __DIR__.'/animal.php';
require __DIR__.'/dog.php';

function flattenClass($class){
    $rc = new ReflectionClass($class);

    // Class start
    $out = "class {$rc->getName()} ";

    // Interfaces
    $interfaces = $rc->getInterfaces();
    if (sizeOf($interfaces) > 0){
        $out .= "implements ";
        $out .= implode(',', array_map(function($interface){
            return $interface->getName();
        }, $interfaces));
        $out .= ' ';
    }

    $out .= "{\n\n";

    // Properties (TODO: Default values)
    foreach ($rc->getProperties() as $property){
        $out .= '    '.getPropertySource($property).PHP_EOL;
    }
    $out .= PHP_EOL;

    // Methods
    foreach ($rc->getMethods() as $method){
        $out .= getMethodSource($method).PHP_EOL;
    }

    // Class end
    $out .= "}";

    return $out;
}

function getMethodSource(\ReflectionMethod $method){
    $fileSource = file($method->getFilename()); 
    $len = $method->getEndLine() - $method->getStartLine() + 1;
    $lines = array_slice($fileSource, $method->getStartLine()-1, $len);

    // Needs a bit of work
    //array_unshift($lines, $method->getDocComment().PHP_EOL);

    // Annotate where methods came from
    array_unshift($lines, "    // From class {$method->getDeclaringClass()->getName()}\n");

    return implode('', $lines);
}

function getPropertySource(\ReflectionProperty $p){
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

    $o .= '$'.$p->getName().';';

    return $o;
}

echo flattenClass('Dog');
