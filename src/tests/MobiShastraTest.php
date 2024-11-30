<?php declare(strict_types=1);

use SendSMS\MobiShastra;
use PHPUnit\Framework\TestCase;

/**
 * MobiShastraTest
 */
class MobiShastraTest extends TestCase
{
	public function test_can_create_api_object(): void
	{
		$username = getenv('MOBISHASTRA_USERNAME');
		$password = getenv('MOBISHASTRA_PASSWORD');
		$senderid = getenv('MOBISHASTRA_SENDERID');
		$sms = new MobiShastra($username, $password, $senderid);

		$this->assertInstanceOf(MobiShastra::class, $sms);
	}

	public function test_can_send_sms_to_single_number()
	{
		$username = getenv('MOBISHASTRA_USERNAME');
		$password = getenv('MOBISHASTRA_PASSWORD');
		$senderid = getenv('MOBISHASTRA_SENDERID');
		$sms = new MobiShastra($username, $password, $senderid);

		$otp = random_int(1000, 9999);
		$numbers = ['9715866XXXX'];
		$message = $otp .' is your OTP for Tripo account login. Please do not share this with anyone.';

		$result = $sms->sendSms($numbers, $message, $senderid);

		$this->assertNotFalse($result);
		$this->assertStringContainsStringIgnoringCase('successful', $result);
	}
}