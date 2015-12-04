# Laravel-epay API

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

Laravel wrapper for the Epay.bg API.
Working with laravel 5.1

## Install

Via Composer

``` bash
$ composer require dgeorgiev/epay

```

* Add the service provider to your $providers array in config/app.php file like:

```
Dgeorgiev\Epay\EpayServiceProvider::class
```

* Add the alias to your $aliases array in config/app.php file like:

```
Epay' => Dgeorgiev\Epay\Facades\Epay::class
```

* Run the following command to publish configuration:
```
php artisan vendor:publish
```


## Usage

``` php

    $invoice     = sprintf("%.0f", rand(1, 50) * 105);
    $amount      = '22,80';
    $expiration  = '01.08.2020';
    $description = 'Test';

    Epay::setData(
        $invoice,
        $amount,
        $expiration,
        $description
    );

```

### Notification receive route (POST)

``` PHP
    Route::post('receive', function(){

        $receiver = Epay::receiveNotification(Request::all());

        /**
        * Update order or status of payment
        *
        *    array (
        *      'invoice' => '1890',
        *      'status' => 'PAID',
        *      'pay_date' => '20151204143730',
        *      'stan' => '036257',
        *      'bcode' => '036257',
        *    ),
        *
        **/
        foreach($receiver['items'] as $item){
            Log::info($item);
            Log::info($item['status']);
            Log::info($item['invoice']);
        }

        return $receiver['response'];

    });
```


### Form in view
```
    <form action="{{ Epay::getSubmitUrl() }}" method="post">
        {!! Epay::generateHiddenInputs() !!}

        // your code here

        <button type=submit>Изпрати</button>
    </form>
```

## Support
This package only supports Laravel 5 & Laravel 5.1 at the moment.

* In case of any issues, kindly create one on the Issues section.
* If you would like to contribute:
  * Fork this repository.
  * Implement your features.
  * Generate pull request.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email me@dgeorgiev.biz instead of using the issue tracker.

## Credits

- [epay.bg demo packages][https://demo.epay.bg/]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-packagist]: https://packagist.org/packages/league/:package_name
[link-author]: https://github.com/dgeorgiev
