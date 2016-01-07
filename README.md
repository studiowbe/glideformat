# GlideFormat
Use the excellent [glide api](http://glide.thephpleague.com/) with named presets

## Some basic examples

```php
$server = Studiow\GlideFormat\GlideFormatServer::createServer([
            "source" =>  '/path/to/uploads',
            "cache" => '/path/to/cache',
        ]);

$server->addPreset("thumbnail", ["w" => 100, "h" => 150, "fit" => "crop"]);
```

And when using a router, you could something like this:

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// create $server like above

$router = new \League\Route\RouteCollection();

$router->get("/img/{format}/{filename}", function(Request $request, Response $response, array $args) use ($server) {
     $server->outputImage($args['filename'], $args['format']);
});
```
