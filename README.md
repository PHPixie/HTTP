# HTTP
PHPixie HTTP library


[![Build Status](https://travis-ci.org/PHPixie/HTTP.svg?branch=master)](https://travis-ci.org/PHPixie/HTTP)
[![Test Coverage](https://codeclimate.com/github/PHPixie/HTTP/badges/coverage.svg)](https://codeclimate.com/github/PHPixie/HTTP)
[![Code Climate](https://codeclimate.com/github/PHPixie/HTTP/badges/gpa.svg)](https://codeclimate.com/github/PHPixie/HTTP)
[![HHVM Status](https://img.shields.io/hhvm/phpixie/http.svg?style=flat-square)](http://hhvm.h4cc.de/package/phpixie/http)

[![Author](http://img.shields.io/badge/author-@dracony-blue.svg?style=flat-square)](https://twitter.com/dracony)
[![Source Code](http://img.shields.io/badge/source-phpixie/http-blue.svg?style=flat-square)](https://github.com/phpixie/http)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](https://github.com/phpixie/http/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/phpixie/http.svg?style=flat-square)](https://packagist.org/packages/phpixie/http)

This library handles HTTP protocol asbtraction and implements the PSR-7 implemenetation. In addition to implementing the standard it provides wrappers for Requests and Responses and also abstractions for Cookies and Session. Since these wrappers work with any PSR-7 implementation it will now be possible to run PHPixie in some interesting environments, like inside ReactPHP etc. You can also use these abstractions to create your own PSR-7 compatible microframework.

Here is a quick demo:

```php
//Without the PHPixie Framework
$slice = new \PHPixie\Slice();
$http = new \PHPixie\HTTP($slice);

//inside the framework
$http = $frameworkBuilder->components()->http();
```

**Requests**

```php
//Build a Request from globals ($_GET etc)
$request = $http->request();

//Or you can pass a PSR-7 ServerRequestInterface to wrap
$request = $http->request($serverRequest);

//$_GET
$query = $request->query();

//$_POST
$query = $request->data();

//Additional attributes,
//e.g. parameters from routing
$query = $request->attributes();

//$_GET['pixie']
$query->get('pixie');

//With default value
$query->get('pixie', 'Trixie');

//Throw an exception if field is not set
$query->getRequired('pixie');

//$_GET['user']['name'];
$query->get('user.name');

//Or like this
$userData = $query->slice('user');
$userData->get('name');

//In this case $userData
//is an instance of \PHPixie\Slice\Data
//totally unrelated to HTTP library
//so you can pass it around the system
//without coupling to HTTP

//Accessing $_SERVER
$request->server()->get('http_host');

//Get a header line
//If multiple values are present
//for the same header, they will be
//concatenated with ','
$request->headers()->get('host');
$request->headers()->getRequired('host');

//Get header values as array
$request->headers()->getLines('accept');

//Handling uploads
$uploadedFile = $request->uploads()->get('file');
$uploadedFile->move('/images/fairy.png');

//HTTP method
$uri = $request->method();

//Accessing Uri
$uri = $request->uri();
$path = $uri->getPath();

//Underlying ServerRequest
$serverRequest = $request->serverRequest();
```

**Response**  
Apart from provideing a Response wrapper, PHPixie HTTP also can simplify building some frequently used responses, like JSON responses with proper headers and file downloads.

```php
$responses = $http->responses();

//The simplest response
$response = $responses->string('hello world');

//JSON with headers that forbid caching
$responses->json(array('name' => 'Pixie'));

//Redirect
$responses->redirect('http://phpixie.com/');

//File streaming
$responses->streamFile('pixie.png');

//Download contetnts as a file
//useful for CSV, TXT types
$responses->download('name.txt', 'text/plain', 'Trixie');

//File download
$responses->downloadFile('pixie.png', 'image.png', 'images/fairy.png');

//Modifying the status code
$response->setStatus('404', 'Not Found');

//OR you can omit the phrase to use
//the default one for the provided code
$response->setStatus('404');

//Modifying headers
$response->headers->set('Content-Type', 'text/csv');

//Transforming into PSR-7 Response
$response->asResponseMessage();

//Outputting a response
$http->output($response);
```

**Context**  
Another important part is managing users Cookies and Session. Frequently you would access them outside of the request processing, for example in you authroization library. Also for non0standard environments, like in ReactPHP you would need special handlers for modifying the session. That’s why PHPixie separates these into a Context instance. You can store it separately, allowing users to modify cookies independently and then merge them with the Response. It’s rather easy:

```php
//Generate context for a particular request
$context = $http->context($request);

//And then it's pretty straightforward
$cookies = $context->cookies();
$session = $context->session();

$cookies->set('lang', 'en');
$session->getRequired('user_id');

//Remember to pass the context
//when outputting the Response
//or generation a PSR-7 response
$http->output($response, $context);
$response->asResponseMessage($context);
```

To obtain the HTTP Context when using the PHPixie framework, you need to get it from the framework context:

```php
$httpContext = $frameworkBuilder->context()->httpContext();
$cookies = $httpContext->cookies();
$session = $httpContext->session();
```

Also you can just use the PSR-7 implementations without PHPixie wrappers:

```php
//Get the PSR-7 factory
$psr7 = $http->messages();
$serverRequest = $psr7->sapiServerRequest();
```

As all the other PHPixie libraries it is 100% unit tested and has a high codeclimate score (actually perfect score in this case)