[![deutsche Version](https://logos.oxidmodule.com/de2_xs.svg)](README.md)
[![english version](https://logos.oxidmodule.com/en2_xs.svg)](README.en.md)

# LINK Mobility PHP API Client

[LINK Mobility](https://www.linkmobility.de/) stellt einen Service zum Versenden von mobilen Nachrichten (SMS, Whatsapp, RCS, Chatbot, ...) zur Verfügung.

Der API Client ermöglicht die einfache Einbindung des LINK Mobility Dienstes in PHP-basierende Projekte. Es können Anfragen zum Nachrichtenversand geschickt werden, der Status und die Antwort werden ausgewertet.

## Features

Die Schnittstelle unterstützt derzeit den Versand von SMS (Text und Binär) auf Basis der [Message API in Version 1.0.0](https://docs.linkmobility.de/sms-api/rest-api). Für die Integration weiterer Nachrichtenformate ist die API vorbereitet.

## Erste Schritte

```
composer require d3/linkmobility-php-client
```

```
$client = new \D3\LinkmobilityClient\Client('personal accesstoken');
\D3\LinkmobilityClient\LoggerHandler::getInstance()->setLogger($logger);  // optional
$request = new D3\LinkmobilityClient\SMS\RequestFactory($message, $client)->getSmsRequest())
    ->addRecipient(new D3\LinkmobilityClient\ValueObject\Recipient('recipient number', 'DE'));
$response = $client->request($request)
```

## Changelog

Siehe [CHANGELOG](CHANGELOG.md) für weitere Informationen.

## Beitragen

Wenn Sie eine Verbesserungsvorschlag haben, legen Sie einen Fork des Respoitories an und erstellen Sie einen Pull Request. Alternativ können Sie einfach ein Issue erstellen. Fügen Sie das Projekt zu Ihren Favoriten hinzu. Vielen Dank.

- Erstellen Sie einen Fork des Projekts
- Erstellen Sie einen Feature Branch (git checkout -b feature/AmazingFeature)
- Fügen Sie Ihre Änderungen hinzu (git commit -m 'Add some AmazingFeature')
- Übertragen Sie den Branch (git push origin feature/AmazingFeature)
- Öffnen Sie einen Pull Request

## Support

Bei Fragen zum *Messaging Service* und dessen *Verträgen* kontaktieren Sie bitte das [LINK Mobility Team](https://www.linkmobility.de/kontakt).

Zu *technischen Anfragen* finden Sie die Kontaktmöglichkeiten in der [composer.json](composer.json).

## Lizenz
(Stand: 13.07.2022)

Vertrieben unter der GPLv3 Lizenz.

```
Copyright (c) D3 Data Development (Inh. Thomas Dartsch)

Diese Software wird unter der GNU GENERAL PUBLIC LICENSE Version 3 vertrieben.
```

Die vollständigen Copyright- und Lizenzinformationen entnehmen Sie bitte der [LICENSE](LICENSE.md)-Datei, die mit diesem Quellcode verteilt wurde.