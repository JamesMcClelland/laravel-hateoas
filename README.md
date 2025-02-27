# Laravel HATEOAS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gdebrauwer/laravel-hateoas.svg?style=flat-square)](https://packagist.org/packages/gdebrauwer/laravel-hateoas)
[![Build Status](https://img.shields.io/travis/gdebrauwer/laravel-hateoas/master.svg?style=flat-square)](https://travis-ci.org/gdebrauwer/laravel-hateoas)
[![Quality Score](https://img.shields.io/scrutinizer/g/gdebrauwer/laravel-hateoas.svg?style=flat-square)](https://scrutinizer-ci.com/g/gdebrauwer/laravel-hateoas)
[![Total Downloads](https://img.shields.io/packagist/dt/gdebrauwer/laravel-hateoas.svg?style=flat-square)](https://packagist.org/packages/gdebrauwer/laravel-hateoas)

[HATEOAS](https://en.wikipedia.org/wiki/HATEOAS) allows you to expose the authorization logic of your REST API.
This package makes it easy to add HATEOAS links to your Laravel API resources.

Each resource has its HATEOAS links, and only the accessible links per resource are returned.
If a link is not available on a resource, then the clients of your API can disable functionality linked to that HATEOAS link.

By default an array of links, in the following format, will be added to the JSON of a Laravel API resource:

```json
{
    "data": [
        {
            "id": 1,
            "text": "Hello world!",
            "_links": [
                {
                    "rel": "self",
                    "type": "GET",
                    "href": "http://localhost/message/1"
                },
                {
                    "rel": "delete",
                    "type": "DELETE",
                    "href": "http://localhost/message/1"
                }
            ]
        }
    ]
}
```


## Installation

You can install the package via composer:

```bash
composer require gdebrauwer/laravel-hateoas
```

## Usage

You can create a new HATEOAS class for a model using the following artisan command:

```bash
php artisan make:hateoas MessageHateoas --model=Message
```

In the created class you can define public methods that will be used to generate the links. A method should either return a link or `null`.

```php
class MessageHateoas
{
    use CreatesLinks;

    /**
     * Get the HATEOAS link to view the message.
     *
     * @param \App\Message $message
     *
     * @return null|\GDebrauwer\Hateoas\Link
     */
    public function self(Message $message)
    {
        if (! auth()->user()->can('view', $message)) {
            return;
        }

        return $this->link('message.show', ['message' => $message]);
    }

    /**
     * Get the HATEOAS link to delete the message.
     *
     * @param \App\Message $message
     *
     * @return null|\GDebrauwer\Hateoas\Link
     */
    public function delete(Message $message)
    {
        if (! auth()->user()->can('delete', $message)) {
            return $this->link('message.archive', ['message' => $message]);
        }

        return $this->link('message.destroy', ['message' => $message]);
    }
}
```

To add the links to an API resource, you have to add the `HasLinks` trait and use the `$this->links()` method. The HATEOAS class will be automatically discovered.

```php
class MessageResource extends JsonResource
{
    use HasLinks;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            '_links' => $this->links(),
        ];
    }
}
```

## Customization

#### Formatting

You can customize the JSON links formatting by binding your custom formatter to the service container. The provided formatter class should implement the `Formatter` interface:

```php
use GDebrauwer\Hateoas\Formatters\Formatter;
use GDebrauwer\Hateoas\Formatters\DefaultFormatter;

$this->app->bind(Formatter::class, DefaultFormatter::class);
```

If the code to format the links is pretty small or you don't want to create a separate formatter class for it, you can provide the custom formatting using a callback:

```php
use GDebrauwer\Hateoas\Hateoas;
use GDebrauwer\Hateoas\LinkCollection;

Hateoas::formatLinksUsing(function (LinkCollection $links) {
    // return array based on links
});
```

#### HATEOAS class discovery

By default, the HATEOAS classes of models will be auto-discovered. Specifically, the HATEOAS classes must be in a Hateoas directory below the directory that contains the models.
If you would like to provide your own HATEOAS class discovery logic, you can register a custom callback:

```php
use GDebrauwer\Hateoas\Hateoas;

Hateoas::guessHateoasClassNameUsing(function (string $class) {
    // return a HATEOAS class name
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Günther Debrauwer](https://github.com/gdebrauwer)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
