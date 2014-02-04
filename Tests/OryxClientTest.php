<?php

namespace FMM\FindMyMovieBundle\Tests;

use FMM\FMMOryxBundle\Client\OryxClient;
use Guzzle\Http\Message\Response;
use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\RequestInterface;

class OryxClientTest extends GuzzleTestCase
{
	private $serverUrl = "54.194.185.239";
	private $serverPort = "8091";

    /**
     * @param MockPlugin $plugin
     * @param $code
     * @param null $body
     *
     * @return OryxClient
     */
    protected function prepareClient(MockPlugin $plugin, $code, $body = null)
    {
        $client = OryxClient::factory();
        $plugin->addResponse(new Response($code, null, $body));
        $client->addSubscriber($plugin);

        return $client;
    }

    /**
     * @param MockPlugin $plugin
     *
     * @return RequestInterface
     */
    protected function getRequest(MockPlugin $plugin)
    {
        $requests = $plugin->getReceivedRequests();

        return reset($requests);
    }

    public function testHomepage()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200);

        // ACT
        $response = $client->get()->send();

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort, $this->getRequest($plugin)->getUrl());
    }

    public function testWithUsernamePassword()
    {
        // ARRANGE
        $client = OryxClient::factory(array(
            'username' => 'test',
            'password' => '1234',
        ));

        // ACT
        $request = $client->createRequest();

        // ASSERT
        $this->assertEquals('Basic '.base64_encode('test:1234'), $request->getHeader('Authorization'));
    }

    public function testWithoutUsernamePassword()
    {
        // ARRANGE
        $client = OryxClient::factory();

        // ACT
        $request = $client->createRequest();

        // ASSERT
        $this->assertEquals(null, $request->getHeader('Authorization'));
    }

    public function testWithNullUsernamePassword()
    {
        // ARRANGE
        $client = OryxClient::factory(array(
            'username' => null,
            'password' => null,
        ));

        // ACT
        $request = $client->createRequest();

        // ASSERT
        $this->assertEquals(null, $request->getHeader('Authorization'));
    }

    public function testRecommendation()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, "8587,0.9856012\n7443,0.9246327");

        // ACT
        $command = $client->getCommand('GetRecommendation', array(
        	'userID' 	=> 1,
        	'howMany' 	=> 2,
        ));
        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertEquals("8587,0.9856012\n7443,0.9246327", $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/recommend/1?howMany=2', $this->getRequest($plugin)->getUrl());
    }

    public function testRecommendationToMany()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, "862,0.9052277\n630,0.846878");

        // ACT
        $command = $client->getCommand('GetRecommendationToMany', array(
        	'userIDs' 	=> array(1, 3),
        	'howMany' 	=> 2
        ));
        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertEquals("862,0.9052277\n630,0.846878", $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/recommendToMany/1/3?howMany=2', $this->getRequest($plugin)->getUrl());
    }

    public function testRecommendationToAnonymous()
    {
	// ARRANGE
	$plugin = new MockPlugin();
	$client = $this->prepareClient($plugin, 200, "756,0.008963955\n11970,0.0084485505");

	// ACT
	$command = $client->getCommand('GetRecommendationToAnonymous', array(
		'preferences' 	=> array(10539 => 0.7, 11113 => 0.1),
		'howMany' 	=> 2,
	));
	/** @var $response Response */
	$response = $client->execute($command);

	// ASSERT
	$this->assertEquals("756,0.008963955\n11970,0.0084485505", $response->getBody(true));
	$this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/recommendToAnonymous/10539=0.7/11113=0.1?howMany=2', $this->getRequest($plugin)->getUrl());
    }

    public function testEstimationForAnonymous()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, '0.65');

        // ACT
        $command = $client->getCommand('GetEstimationForAnonymous',
            array(
                'itemID'      		=> 510,
                'preferences' 	=> array(10539 => 0.7, 11113 => 0.1),
            )
        );
        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertEquals(0.0016920734, $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/estimateForAnonymous/510/10539=0.7/11113=0.1', $this->getRequest($plugin)->getUrl());
    }

    public function testReady()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200);

        // ACT
        $command = $client->getCommand('Ready');

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/ready', $this->getRequest($plugin)->getUrl());
    }

    public function testRefresh()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200);

        // ACT
        $command = $client->getCommand('Refresh');

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/refresh', $this->getRequest($plugin)->getUrl());
    }

    public function testBecause()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, "11970,0.81400377\n10530,0.73516923");

        // ACT
        $command = $client->getCommand('GetBecause', array('userID' => 6, 'itemID' => 10539));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals("11970,0.81400377\n10530,0.73516923", $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/because/6/10539', $this->getRequest($plugin)->getUrl());
    }

    public function testEstimate()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, <<<BODY
0.22691375
0.120162085

BODY);

        // ACT
        $command = $client->getCommand('GetEstimation', array('userID' => 9, 'itemIDs' => array(10674, 11224)));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertRegExp('/^([\d\.]+[^\d\.]+){2}$/', $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/estimate/9/10674/11224', $this->getRequest($plugin)->getUrl());
    }

    public function testSimilarity()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, "10545,0.88881636\n10599,0.8729536");

        // ACT
        $command = $client->getCommand('GetSimilarity', array(
        	'itemIDs' 	=> array(10674, 11224),
        	'howMany' 	=> 2,
        ));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals("10545,0.88881636\n10599,0.8729536", $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/similarity/10674/11224?howMany=2', $this->getRequest($plugin)->getUrl());
    }

    public function testSimilarityToItem()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, <<<BODY
0.6347412
0.47760922

BODY
        );

        // ACT
        $command = $client->getCommand('GetSimilarityToItem', array(
            'toItemID' => 424,
            'itemIDs' => array(9377, 10510),
        ));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertRegExp('/^([\d\.]+[^\d\.]+){2}$/', $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/similarityToItem/424/9377/10510', $this->getRequest($plugin)->getUrl());
    }

    public function testMostPopularItems()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200, "14,432.0\n1891,355.0");

        // ACT
        $command = $client->getCommand('GetMostPopularItems', array('howMany'	=> 2));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals("14,432.0\n1891,355.0", $response->getBody(true));
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/mostPopularItems?howMany=2', $this->getRequest($plugin)->getUrl());
    }

    public function testIngest()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200);

        // ACT
        $command = $client->getCommand('Ingest', array('data' => array(
            array("userID" => 1, "itemID" => 11224, "value" => 0.234),
        )));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/ingest', $this->getRequest($plugin)->getUrl());
        $this->assertEquals(<<<BODY
1,11224,0.234

BODY
            , (string)$this->getRequest($plugin)->getBody());
    }

    public function testPostPref()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200);

        // ACT
        $command = $client->getCommand('SetPref', array("userID" => 1, "itemID" => 11224, "value" => (string)0.234));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/pref/1/11224', $this->getRequest($plugin)->getUrl());
        $this->assertEquals('POST', $this->getRequest($plugin)->getMethod());
        $this->assertEquals('0.234', (string)$this->getRequest($plugin)->getBody());
    }

    public function testRemovePref()
    {
        // ARRANGE
        $plugin = new MockPlugin();
        $client = $this->prepareClient($plugin, 200);

        // ACT
        $command = $client->getCommand('RemovePref', array("userID" => 1, "itemID" => 11224));

        /** @var $response Response */
        $response = $client->execute($command);

        // ASSERT
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('http://'.$this->serverUrl.':'.$this->serverPort.'/pref/#/11224', $this->getRequest($plugin)->getUrl());
        $this->assertEquals('DELETE', $this->getRequest($plugin)->getMethod());
    }
}