<?php

namespace FMM\OryxBundle\Client;

use Guzzle\Service\Client as Client;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Parser\ParserRegistry;
use Guzzle\Plugin\CurlAuth\CurlAuthPlugin;
use FMM\OryxBundle\Resources\templates\OryxUriTemplate;

class OryxClient extends Client
{
	public static function factory($config=array())
	{
		ParserRegistry::getInstance()->registerParser(
			'uri_template', new OryxUriTemplate(ParserRegistry::getInstance()->getParser('uri_template'))
		);

		$default = array(
			'base_url' 	=> 'http://{hostname}:{port}',
			'hostname' => 'localhost',
			'port'		=> '8091',
			'username' => null,
			'password' => null
		);
		$required = array('base_url', 'hostname', 'port');
		$config = Collection::fromConfig($config, $default, $required);

		$client = new self($config->get('base_url'), $config);
		$client->setDescription(ServiceDescription::factory(__DIR__.DIRECTORY_SEPARATOR
									.'..'.DIRECTORY_SEPARATOR
									.'Resources'.DIRECTORY_SEPARATOR
									.'config'.DIRECTORY_SEPARATOR
									.'oryx_service.json')
		);

		$client->setDefaultHeaders(array(
			'Accept' => 'application/json',
		));

		$authPlugin = new CurlAuthPlugin($config['username'], $config['password']);
		$client->addSubscriber($authPlugin);

		return $client;
	}

	public static function filterIngestData(array $data)
	{
		$result = '';

		foreach ($data as $line) {
			$result .= $line['userID'].','.$line['itemID'].(isset($line['value']) ? ','.$line['value'] : '').PHP_EOL;
		}

		return $result;
	}
}