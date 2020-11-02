# SendSMS
SMS Sending php library for using Textlocal SMS provider

```
use SendSMS\Textlocal;

$mode = false; // true = test, false = live
$receivers = ['919348980000']; // 91 prefix is important for country code
$textlocal = new TextLocal(false, false, $apikey);
try {
	$response = $textlocal->sendSms($receivers, $message, $sender_name, null, $mode);
} catch (\Exception $e) {
	throw new \Exception("Sorry, Failed to send message: " . $e->getMessage());
}
```
