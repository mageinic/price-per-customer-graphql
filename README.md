# Price Per Customer GraphQL

**Price Per Customer GraphQL is a part of MageINIC Price Per Customer extension that adds GraphQL features.** This extension extends Price Per Customer definitions.

## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageinic/price-per-customer-graphql

php bin/magento maintenance:enable
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento maintenance:disable
php bin/magento cache:flush
```

**Note:**
Magento 2 Price Per Customer GraphQL requires installing [MageINIC Price Per Customer](https://github.com/mageinic/Price-Per-Customer) in your Magento installation.

**Or Install via composer [Recommend]**
```
composer require mageinic/price-per-customer
```

## 2. How to use

- To view the queries that the **MageINIC Price Per Customer GraphQL** extension supports, you can check `Price Per Customer GraphQl User Guide.pdf` Or run `PricePerCustomerGraphQl.postman_collection.json` in Postman.

## 3. Get Support

- Feel free to [contact us](https://www.mageinic.com/contact.html) if you have any further questions.
- Like this project, Give us a **Star**
