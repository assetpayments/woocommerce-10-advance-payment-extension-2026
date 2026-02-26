## Розширення оплати карткою для Wordpress 6+ WooCommerce 10+ (класичний та Blocks кошик)

### Встановлення
* Завантажте файл модуля `woocommerce_10_advance_blocks_1.zip` через Plugins -> Add New -> Upload Plugin
* Активуйте розширення AssetPayments у WooCommerce -> Settings -> Payment methods
* Вкажіть у налаштуваннях розширення:
  * **Turn On/Off** — Активація платіжного методу
  * **Checkout type** — вибір типу кошика (класичний або block-based)
  * **Public key** — публічний ключ AssetPayments
  * **Secret key** — секретний ключ AssetPayments
  * **Processing ID** — ІД процесингу AssetPayments
  * **Template ID** — ІД шаблону AssetPayments
  * **Skip Checkout page** — пропуск тестової сторінки AssetPayments
  * **Payment method title** — назва методу в кошику
  * **Payment method description** — опис методу в кошику
  * **Advance amount or %** — сума авансового платежу в числах або %
  * **Advance product title** — назва товару в кошику при авансовому платежі
  * **Lang** — мова платіжної сторінки
  * **Current language function** — функція керування мовою
  * **Alternative callback URL** — увімк./вимк. функції альтернативного webhook url
  * **Сallback URL** — альтернативний webhook url
  * **Successful payment status** — статус успішної оплати
  * **Declined payment status** — статус помилкової оплати
  * **Refunded payment status** — статус повернення

### Примітки
Розроблено та протестовано з Wordpress 6+ WooCommerce 10+

### Проблеми при встановленні
Якщо при встановленні модуля з'являється повідомлення "AssetPayments не підтримує валюти Вашого магазину." — змініть налаштування валюти у WooCommerce -> Settings -> Main -> Currency settings -> Currency на EUR, USD, UAH, або додайте свій код валюти у файлі `WC_Gateway_kmnd_Assetpayments`.



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


