# Imageable Laravel 

Imageable Laravel is a Laravel library for dealing with images.

## Installation

Use composer to require package to Your project.

```bash
composer require gause/imageable-laravel
```

## Imageable Facade Usage

```php
    public function store(\Illuminate\Http\Request $request) 
    {
        $image = Imageable::createImage(
            $request->image_file,
            $request->name,
            $request->short_description,
            $request->description
        );
    }
```

## ImageableRequest Usage

```php
<?php

namespace App\Requests;

use Gause\ImageableLaravel\Requests\ImageableRequest;

class ExampleRequest extends ImageableRequest
{
    /**
     *  Authorize requests
     *
     *  return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     *  Defines rules for validating request
     *
     *  return array
     */
    public function rules(): array
    {
        return [
        ];
    }
}

```

```php
    public function store(\Illuminate\Http\Request $request) 
    {
        $image = $request->createImage();
    }
```

Create image method automatically uses all attributes passed with prefix 'image'.
Prefix can be specified as method argument: 

```php 
$request->createImage('myOwnPrefix'); 
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./License.md)