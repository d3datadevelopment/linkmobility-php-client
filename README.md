```
$client = new Client('accesstoken');
$client->setLogger($logger);  // optional
$request = new D3\LinkmobilityClient\SMS\RequestFactory($message, $client)->getSmsRequest())
    ->addRecipient(new D3\LinkmobilityClient\ValueObject\Recipient('recipient', 'DE'));
$client->request($request)
```