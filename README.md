# OTP Service

Use the OTP Service for instant SMS deliveries such as passwords, confirmation codes, etc. SMS messages are delivered within 3 minutes and cannot be scheduled.

## Contents
- [Contact & Support](#contact--support)
- [Documentation](#documentation)
- [Supported Laravel Versions](#supported-laravel-versions)
- [Supported Lumen Versions](#supported-lumen-versions)
- [Supported Symfony Versions](#supported-symfony-versions)
- [Supported PHP Versions](#supported-php-versions)
- [Installation](#installation)
- [Example](#example)

## Contact & Support

For all your questions and suggestions related to the Netgsm API Service, you can send an email to teknikdestek@netgsm.com.tr.

## Documentation

Access comprehensive documentation and sample code for different programming languages for the API Service at
 Comprehensive documentation for the API Service and sample coding in different software languages 
 Available from [https://www.netgsm.com.tr/dokuman](https://www.netgsm.com.tr/dokuman)
 
### Supported Laravel Versions

Supports Laravel versions 6.x, 7.x, 8.x, 9.x., 10.x., 11.x.

### Supported Lumen Versions

Supports Lumen versions 6.x, 7.x, 8.x, 9.x.

### Supported Symfony Versions

Supports Symfony versions 4.x, 5.x, 6.x., 7.x.

### Supported PHP Versions

Supports PHP version 7.2.5 and above.

### Installation

Install using Composer:

```bash
composer require netgsm/otp
```

In your .env file, it is mandatory to define your NETGSM subscription information:

```env
NETGSM_USERCODE=""
NETGSM_PASSWORD=""
NETGSM_HEADER=""

```

## Example
```php
$config = [
    'username' => $_ENV['NETGSM_USERCODE'],
    'password' => $_ENV['NETGSM_PASSWORD'],
    'header' => $_ENV['NETGSM_HEADER']
];

$otpService = new OTPService($config);

$data = [
    'message' => 'Your OTP is 123456',
    'no' => '905xxxxxxxxx'
];

try {
    $response = $otpService->sendOTP($data);
    print_r($response); // or dd(response);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

```

## Successful Request Example Output
```php
Array
(
    [status] => Sending successful.
    [jobId] => 1310546758
)

```

## Failed Request Example Output
```php
Array
(
    [status] => Check your sender name.
    [code] => 41
)

```
