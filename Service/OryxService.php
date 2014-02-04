<?php

namespace FMM\OryxBundle\Service;

use FMM\OryxBundle\Client\OryxClient ;

/**
 * OryxService lets you use the Oryx REST api
 */
class OryxService
{
	/**
	 * @var OryxClient
	 */
	protected $client;

	/**
	 * @param string $host 		The hostname
	 * @param int 	    $prot 		The port
	 * @param string $username 	The username
	 * @param string $password 	The password
	 */
	function __construct($host, $port, $username = null, $password = null)
	{
		$this->client = OryxClient::factory(array(
			'hostname' => $host,
			'port' 		=> $port,
			'username'	=> $username,
			'password' => $password
		));
	}

	/**
	 * Get recommendation(s) for a known user
	 *
	 * @param $userID 	The user id
	 * @param $count  	The number of results to retrieve
	 *
	 * @return array
	 */
	public function getRecommendation($userID, $count = null)
	{
		$command = $this->client->getCommand('GetRecommendation', array(
			'userID' 	=> $userID,
			'howMany' 	=> $count,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Get recommendation(s) for multiple known users
	 *
	 * @param $userIDs 	An array of user ids
	 * @param $count  	The number of results to retrieve
	 *
	 * @return array
	 */
	public function getRecommendationToMany(array $userIDs, $count = null)
	{
		$command = $this->client->getCommand('GetRecommendationToMany', array(
			'userIDs' 	=> $userIDs,
			'howMany' 	=> $count,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Get recommendation(s) for an anonymous user using an array of Item/Strength pairs that define the user preferences
	 *
	 * @param $preferences 	An array defining the preferences of the unknown user
	 * @param $count  		The number of results to retrieve
	 *
	 * @return array
	 */
	public function getRecommendationToAnonymous(array $preferences = array(), $count = null)
	{
		$command = $this->client->getCommand('GetRecommendationToAnonymous', array(
			'scoreParams' 	=> $preferences,
			'howMany' 		=> $count,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Get ids from items that are similar to the given ones
	 *
	 * @param $itemIDs 	An array of item ids
	 * @param $count  	The number of results to retrieve
	 *
	 * @return array
	 */
	public function getSimilarItems(array $itemIDs, $count = null)
	{
		$command = $this->client->getCommand('GetSimilarity', array(
			'itemIDs' 	=> $itemIDs,
			'howMany' 	=> $count,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Get the similarity of each item from the set to the given item
	 *
	 * @param $toItemID 	The item id to calculate similarity to
	 * @param $itemIDs  	The items to calculate similarity from
	 *
	 * @return array
	 */
	public function getSimilarityToItems($toItemID, array $itemIDs)
	{
		$command = $this->client->getCommand('GetSimilarityToItem', array(
			'toItemID' 	=> $toItemID,
			'itemIDs' 	=> $itemIDs,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Get an estimated preference between a user and some items
	 *
	 * @param $userID 	The user id
	 * @param $itemIDs  	The item ids
	 *
	 * @return array
	 */
	public function getEstimations($userID, array $itemIDs)
	{
		$command = $this->client->getCommand('GetEstimation', array(
			'userID' 	=> $userID,
			'itemIDs' 	=> $itemIDs,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Get an estimated preference for an anonymous user using an array of Item/Strength pairs that define the user preferences
	 *
	 * @param $toItemID 		The item to estimate preference for
	 * @param $preferences  	An array defining the preferences of the unknown user
	 *
	 * @return array
	 */
	public function getEstimationForAnonymous($toItemID, array $preferences)
	{
		$command = $this->client->getCommand('GetRecommendationToAnonymous', array(
			'toItemID' 		=> $toItemID,
			'scoreParams' 	=> $preferences,
		));

		return $this->client->execute($command)->getBody(true);
	}

	 /**
       * Lists the items that were most influential in recommending a given item to a given user
       *
       * @param int   $userID The user id
       * @param int   $itemID The item id
       *
       * @return array
       */
	public function getBecause($userID, $itemID, $count = null)
	{
		$command = $this->client->getCommand('GetBecause', array(
			'userID' 	=> $userID,
			'itemID' 	=> $itemID,
			'howMany'	=> $count,
		));

		return $this->client->execute($command)->getBody(true);
	}

    	/**
     	 * Gets most similar items
     	 *
     	 * @param int $count The number of result to retrieve
       *
       * @return array
       */
	public function getMostPopularItems($count = null)
	{
		$command = $this->client->getCommand('GetMostPopularItems', array(
			'howMany'	=> $count,
		));

		return $this->client->execute($command)->getBody(true);
	}

	/**
	 * Sets a preference between a user and an item
	 *
	 * @param int   $userID The user id
	 * @param int   $itemID The item id
	 * @param float $value  The strength of the association
	 *
	 * @return bool
	 */
	public function setPreference($userID, $itemID, $value = null)
	{
		$command = $this->client->getCommand('SetPref', array(
		    	'userID'		=> $userID,
		    	'itemID'		=> $itemID,
		    	'value'  	=> $value !== null ? (string)$value : null,
		));

		return $this->client->execute($command)->isSuccessful();
	}

	/**
	 * Sets a batch preference between users and items
	 *
	 * @param array $prefs An array of arrays with keys 'userID', 'itemID' and 'value'
	 *
	 * @return bool
	 */
	public function setPreferences(array $prefs)
	{
		$command = $this->client->getCommand('Ingest', array(
		    	'data' => $preferences,
		));

		return $this->client->execute($command)->isSuccessful();
	}

    /**
     * Removes a preference between a user and an item
     *
     * @param int   $userID The user id
     * @param int   $itemID The item id
     *
     * @return bool
     */
    public function removePreference($userID, $itemID)
    {
		$command = $this->client->getCommand('RemovePref', array(
		    	'userID' => $userID,
		    	'itemID' => $itemID,
		));

		return $this->client->execute($command)->isSuccessful();
	}

	/**
	 * Asks if Oryx is ready to answer requests.
	 *
	 * @return bool
	 */
	public function isReady()
	{
		$command = $this->client->getCommand('Ready');

		return $this->client->execute($command)->isSuccessful();
	}

	/**
	 * Returns "OK" or "Unavailable" status depending on whether the recommender is ready.
	 *
	 * @return bool
	 */
	public function refresh()
	{
		$command = $this->client->getCommand('Refresh');

		return $this->client->execute($command)->isSuccessful();
	}

	/**
	 * @return OryxClient
	 */
	public function getClient()
	{
		return $this->client;
	}
}