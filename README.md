```
$client = new Client('accesstoken');
$request = (new \D3\LinkmobilityClient\SMS\Request('me', 'message'))
    ->addRecipient(new Recipient('recipient'));
$client->request($request)
```