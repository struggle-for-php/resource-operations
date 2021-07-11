#!/usr/bin/env php
<?php declare(strict_types=1);
/*
 * This file is part of resource-operations.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$functions         = require __DIR__ . '/FunctionSignatureMap.php';
$resourceFunctions = [];
$resourceMethods = [];


foreach ($functions as $function => $arguments) {
    foreach ($arguments as $argument) {
        if (strpos($argument, '?') === 0) {
            $argument = substr($argument, 1);
        }

        if ($argument === 'resource') {
            $resourceFunction = explode('\'', $function)[0];
            if (false === strpos($resourceFunction, '::')) {
                $resourceFunctions[] = $resourceFunction;
            } else {
                [$class, $method] = explode('::', $function);
                $resourceMethods[] = $class . '::' . strtolower($method);
            }
        }
    }
}

$resourceFunctions = array_unique($resourceFunctions);
sort($resourceFunctions);
$resourceMethods = array_unique($resourceMethods);
sort($resourceMethods);


$buffer = <<<EOT
<?php declare(strict_types=1);
/*
 * This file is part of resource-operations.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfp\ResourceOperations;

final class ResourceOperations
{
EOT;


$buffer .= <<< EOT
    /**
     * @return string[]
     */
    public static function getFunctions(): array
    {
        return [

EOT;

foreach ($resourceFunctions as $function) {
    $buffer .= sprintf("            '%s',\n", $function);
}

$buffer .= <<< EOT
        ];
    }

EOT;

$buffer .= <<< EOT
    /**
     * @return string[]
     */
    public static function getMethods(): array
    {
        return [

EOT;

foreach ($resourceMethods as $function) {
    $buffer .= sprintf("            '%s',\n", $function);
}

$buffer .= <<< EOT
        ];
    }

EOT;



$buffer .= <<< EOT
}
EOT;

file_put_contents(__DIR__ . '/../src/ResourceOperations.php', $buffer);

