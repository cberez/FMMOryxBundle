<?php

namespace FMM\Oryx\Tests;

use FMM\Oryx\Client\OryxClient;
use Guzzle\Http\Message\Response;
use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use PHPUnit_Framework_TestCase;
use FMM\Oryx\Service\OryxService;

class OryxServiceTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        // ARRANGE
        $oryx = new OryxService('host', 1234, 'testname', 'pa$$word');

        // ACT
        $client = $oryx->getClient();

        // ASSERT
        $this->assertEquals('host', $client->getConfig('hostname'));
        $this->assertEquals(1234, $client->getConfig('port'));
        $this->assertEquals('testname', $client->getConfig('username'));
        $this->assertEquals('pa$$word', $client->getConfig('password'));
    }
}
