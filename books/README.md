# Books API

The books API is a small API I wrote to interact with the [Micro.blog Books API](https://help.micro.blog/t/json-api-books/545) without [leaking my app token](https://github.com/RobinBoers/geheimesite.nl/blob/27e5bfbd9945e1ef1e486e43050b2fa18d547e82/js/bookshelves.js#L63).

This works in combination with a hugo shortcode to display bookshelves from Micro.blog on my website.

The token itself is stored in `token.php` which looks like this:

```php
<?php

$token = "3938D29DCAE2E6A7B2E0"
```

_This is an revoked token, don't bother trying to use it :joy:_
