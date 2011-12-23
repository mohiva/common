<?php

namespace com\mohiva\test\resources\common\lang\reflection;

$var = 1;
function () use ($var) {};

use com\mohiva\common\lang\ReflectionClass, com\mohiva\common\lang\ReflectionProperty;
use com\mohiva\common\lang\ReflectionMethod;

$var = 1;
function () use ($var) {};

class NamespaceWithClosureDeclaration {}

// Should not be recognized
use ArrayIterator;
