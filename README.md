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
        $request->image_file
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

Create image method automatically uses all attributes passed with prefix 'image':
    image -> image file
    image_name -> name of image in db
    image_short_description -> short description of image
    image_description -> description of image
    

Prefix can be specified as method argument: 

```php 
$request->createImage('my_own_prefix'); 
```

Than arrguments should be passed to endpoint like this:

    my_own_prefix -> image file
    my_own_prefix_name -> name of image in db
    my_own_prefix_short_description -> short description of image
    my_own_prefix_description -> description of image

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./License.md)
