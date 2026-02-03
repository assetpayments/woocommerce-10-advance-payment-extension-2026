## Расширение оплаты картой для Wordpress 6+ WooCommerce 10+ (классическая и Blocks корзина)

### Установка
* Загрузите файл модуля woocommerce_10_advance_blocks_1.zip через Plugins -> Add New -> Upload Plugin
* Активируйте расширение AssetPayments в WooCommerce -> Settings -> Payment methods
* Задайте в настройки расширения:
  * Turn On/Off - Активация платежного метода
  * Checkout type - выбор типа корзины (классический и block-based)
  * Public key - AssetPayments публичный ключ
  * Secret key - AssetPayments секретный ключ
  * Processing ID - AssetPayments ИД процессинга
  * Template ID - AssetPayments ИД шаблона
  * Skip Checkout page - пропуск тестовой страницы AssetPayments
  * Payment method title - Наименование метода в корзине
  * Payment method description - Описание метода в корзине
  * Advance amount or % - Сумма авансового платежа в числах и %
  * Advance product title - наименование товара в корзине при авансовом платеже
  * Lang - язык платежной страницы
  * Current language function - функция управления языком
  * Alternative callback URL - вкл/выкл функции альтернативного webhook url
  * Сallback URL - альтернативный webhook url
  * Successful payment status - статус успешной оплаты
  * Declined payment status - статус ошибочной оплаты
  * Refunded payment status - статус возврата

### Примечания
Разработано и протестировано с Wordpress 6+ WooCommerce 10+

### Проблемы при установке
Если при установке модуля показывается сообщение "AssetPayments не поддерживает валюты Вашего магазина." - измените настройки валюты в WooCommerce -> Settings -> Main -> Currency settings -> Currency на EUR, USD, UAH, или добавьте свой код валюты в файле WC_Gateway_kmnd_Assetpayments 


