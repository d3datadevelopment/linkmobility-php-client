[![deutsche Version](https://logos.oxidmodule.com/de2_xs.svg)](README.md)
[![english version](https://logos.oxidmodule.com/en2_xs.svg)](README.en.md)

# LINK Mobility PHP API Client

[LINK Mobility](https://www.linkmobility.de/) provides a service for sending mobile messages (SMS, Whatsapp, RCS, Chatbot, ...).

The API client enables the simple integration of the LINK Mobility service into PHP-based projects. Requests to send messages can be sent, the status and the response are evaluated.

## Features

The interface currently supports the sending of SMS (text and binary) based on the [Message API in version 1.0.0](https://docs.linkmobility.de/sms-api/rest-api). The API is prepared for the integration of other message formats.

## Getting Started

```
composer require d3/linkmobility-php-client
```

```
$client = new Client('personal accesstoken');
$client->setLogger($logger);  // optional
$request = new D3\LinkmobilityClient\SMS\RequestFactory($message, $client)->getSmsRequest())
    ->addRecipient(new D3\LinkmobilityClient\ValueObject\Recipient('recipient number', 'DE'));
$response = $client->request($request)
```

## Changelog

See [CHANGELOG](CHANGELOG.md) for further informations.

## Contributing

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue. Don't forget to give the project a star! Thanks again!

- Fork the Project
- Create your Feature Branch (git checkout -b feature/AmazingFeature)
- Commit your Changes (git commit -m 'Add some AmazingFeature')
- Push to the Branch (git push origin feature/AmazingFeature)
- Open a Pull Request

## Support

If you have any questions about the *messaging service* and its *contracts*, please contact the [LINK Mobility Team](https://www.linkmobility.de/kontakt).

For *technical inquiries* you will find the contact options in the [composer.json](composer.json).

## License
(status: 2022-07-13)

Distributed under the GPLv3 license.

```
Copyright (c) D3 Data Development (Inh. Thomas Dartsch)

This software is distributed under the GNU GENERAL PUBLIC LICENSE version 3.
```

For full copyright and licensing information, please see the [LICENSE](LICENSE.md) file distributed with this source code.