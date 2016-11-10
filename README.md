# rbac-manage
通用权限管理系统

## Setup ##

 Add a `composer.json` file to your project:

```javascript
{
  "require": {
      "php-amqplib/php-amqplib": "2.6.*"
  }
}
```

Then provided you have [composer](http://getcomposer.org) installed, you can run the following command:

```bash
$ composer.phar install
```

That will fetch the library and its dependencies inside your vendor folder. Then you can add the following to your
.php files in order to use the library

```php
require_once __DIR__.'/vendor/autoload.php';
```
