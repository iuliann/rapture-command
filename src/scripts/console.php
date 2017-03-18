#!/usr/bin/env php
<?php

/*
 * This file MUST be placed in the \AppName\Domain\Command directory
 */

$output = function($value) { echo $value . "\n"; };

$opt = getopt(null, ['cmd:', 'env:']);
if (!isset($opt['cmd'])) {
    $output('Missing command!' . "\n");
    exit(1);
}

if (!isset($opt['env'])) {
    $output('Missing environment!' . "\n");
    exit(1);
}

define('APP_ENV', $opt['env']);
require_once __DIR__ . '/../../configs/bootstrap.php';

$commandNamespace = APP_NAME . '\Domain\Command';
$commandClassName = $commandNamespace . '\\' . str_replace(':', '\\', $opt['cmd']);

list($short, $long) = $commandClassName::getOpt();
$options = getopt($short, $long);

/** @var \Rapture\Command\Command $command */
$command = new $commandClassName($options, $output);

$command->execute();
