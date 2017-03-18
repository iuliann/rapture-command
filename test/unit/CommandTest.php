<?php

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testOption()
    {
        $cmd = new \Rapture\Command\Command(['required' => 'yes'], function ($v) { echo $v; });

        $this->assertEquals('yes', $cmd->getOption('required'));
        $this->assertTrue($cmd->hasOption('required'));
    }
}
