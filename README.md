Moduł płatności Dotpay dla Magento 2.1
===================
Niniejszy moduł płatności jest przygotowany dla **Magento w wersji 2.1 i wyższych**.
* Magento 2.0: [KLIKNIJ](https://github.com/dotpay/Magento2)
* Magento 1.x: [KLIKNIJ](https://github.com/dotpay/Magento-1.x)

Instrukcja instalacji
-------------
1. Proszę przejść do katalogu głównego instalacji sklepu Magento
2. Należy się upewnić, czy w pliku *composer.json* Magento są dodane repozytoria Dotpay. Jeśli nie, trzeba wywołać następujące komendy:
```
composer config repositories.dotpaySDK vcs https://github.com/dotpay/PHP-SDK

composer config repositories.magento2-payment vcs https://github.com/dotpay/magento2-payment
```
3. Instalacja modułu następuje przy pomocy narzędzia *composer*. W katalogu głównym Magento należy wywołać następujące komendy:
```
composer require dotpay/magento2-payment

php bin/magento module:enable Dotpay_Payment

php bin/magento setup:upgrade
```
4. Jeśli potrzeba kompilacji wstrzykiwanych zależności oraz plików statycznych, należy wywołać poniższe komendy:
```
php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy
```

Odinstalowanie
-------------
1. Proszę wykonać poniższą komendę w głownym katalogu Magento:
```
php bin/magento module:uninstall Dotpay_Payment -r -c
```

---------------------------------------

Dotpay payment module for Magento 2.1
===================
This module is prepared for **Magento version 2.1 and later**.
* Magento 2.0: [CLICK](https://github.com/dotpay/Magento2)
* Magento 1.x: [CLICK](https://github.com/dotpay/Magento-1.x)

Installation
-------------
1. Go to the main directory of your Magento installation
2. Make sure if the file *composer.json* of Magento contains repositories of Dotpay. If not, then execute following commands:
```
composer config repositories.dotpaySDK vcs https://github.com/dotpay/PHP-SDK

composer config repositories.magento2-payment vcs https://github.com/dotpay/magento2-payment
```
3. Innstallation of the payment module is realized by using the *composer*tool. Execute the following commands in the main directory of your Magento installation:
```
composer require dotpay/magento2-payment

php bin/magento module:enable Dotpay_Payment

php bin/magento setup:upgrade
```
4. If you need to compile Dependency Injection and static files, execute following commands:
```
php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy
```

Uninstall
-------------
1. Execute the following command in the main directory of your Magento installation:
```
php bin/magento module:uninstall Dotpay_Payment -r -c
```
