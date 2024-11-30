<?php

namespace SendSMS;

/**
 * MobiShastra API2 Wrapper Class
 *
 * This class is used to interface with the MobiShastra API2 to send messages, manage contacts, retrieve messages from
 * inboxes, track message delivery statuses, access history reports
 *
 * @package    MobiShastra
 * @subpackage API
 * @author     Shek Muktar
 * @version    v0.1
 * @const      REQUEST_URL       URL to make the request to
 * @const      REQUEST_TIMEOUT   Timeout in seconds for the HTTP request
 * @const      REQUEST_HANDLER   Handler to use when making the HTTP request (for future use)
 */
class MobiShastra
{
	//"https://mshastra.com/sendurl.aspx?user={$user}&pwd={$password}&senderid={$test_sender_id}&mobileno={$recipient_mobile_number}&msgtext={$message}&priority=High&CountryCode=ALL"
	const REQUEST_URL = 'https://mshastra.com/';
	const REQUEST_TIMEOUT = 60;
	const REQUEST_HANDLER = 'curl';

	private $username;
	private $password;
	private $sender_id;

	private $errorReporting = false;

	public $errors = [];

	/**
	 * Instantiate the object
	 * @param $username
	 * @param $hash
	 */
	public function __construct(string $username, string $password, string $sender_id)
	{
		$this->username = $username;
		$this->password = $password;
		$this->sender_id = $sender_id;
	}

	/**
	 * Send an SMS to one or more comma separated numbers
	 * @param       $numbers
	 * @param       $message
	 * @param       $sender
	 * @param null  $sched
	 * @param false $test
	 * @param null  $receiptURL
	 * @param numm  $custom
	 * @param false $optouts
	 * @param false $simpleReplyService
	 * @return array|mixed
	 * @throws Exception
	 */
	public function sendSms($numbers, $message, $sender, $sched = null, $test = false, $receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false)
	{
		if (!is_array($numbers) && !is_string($numbers))
			throw new \Exception('Invalid $numbers format. Must be an array of numbers or single number');
		if (empty($message))
			throw new \Exception('Empty message');
		if (empty($sender))
			throw new \Exception('Empty sender name');
		if (!is_null($sched) && !is_numeric($sched))
			throw new \Exception('Invalid date format. Use numeric epoch format');

		$params = array(
			'user'       	=> $this->username,
			'pwd'       	=> $this->password,
			'senderid'      => $sender,
			'mobileno'      => implode(',', $numbers),
			'msgtext'       => $message,
			'priority'      => 'High',
			'CountryCode'   => 'High',
		);

		$url = self::REQUEST_URL . "sendurl.aspx";

		return $this->_sendRequest($url, $params);
	}

	/**
	 * Private function to construct and send the request and handle the response
	 * @param       $command
	 * @param array $params
	 * @return array|mixed
	 * @throws Exception
	 * @todo Add additional request handlers - eg fopen, file_get_contacts
	 */
	private function _sendRequest($command, $params = array())
	{
		// Create request string
		$params['username'] = $this->username;

		$this->lastRequest = $params;

		if (self::REQUEST_HANDLER == 'curl')
			$rawResponse = $this->_sendRequestCurl($command, $params);
		else throw new \Exception('Invalid request handler.');

		// @TODO Parse result
		$result = $rawResponse;

		return $result;
	}

	/**
	 * Curl request handler
	 * @param $api_url
	 * @param $params
	 * @return mixed
	 * @throws Exception
	 */
	private function _sendRequestCurl($url, $params)
	{
		$url .= '?'. http_build_query($params);
		// Initialize handle
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => self::REQUEST_TIMEOUT
		));

		$rawResponse = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);

		if ($rawResponse === false) {
			throw new \Exception('Failed to connect to the MobiShastra service: ' . $error);
		} elseif ($httpCode != 200) {
			throw new \Exception('Bad response from the MobiShastra service: HTTP code ' . $httpCode);
		}

		return $rawResponse;
	}

	/**
	 * fopen() request handler
	 * @param $command
	 * @param $params
	 * @throws Exception
	 */
	private function _sendRequestFopen($command, $params)
	{
		throw new \Exception('Unsupported transfer method');
	}

	/**
	 * Get last request's parameters
	 * @return array
	 */
	public function getLastRequest()
	{
		return $this->lastRequest;
	}

	/**
	 * Get Credit Balances
	 * @return array
	 */
	public function getBalance()
	{
		$result = $this->_sendRequest('balance');
		return array('sms' => $result->balance->sms);
	}
}
