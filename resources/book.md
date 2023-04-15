# Book format

The JSON returned by the `/resources/books/{*}` endpoints is structured using the book format, which described below.

The API always returns either a `collection` or `timeline`.

## A single book

```json
{
    id: "SOME_UUID_(optional)",
    title: "SOME_TITLE",
    author: "SOME_AUTHOR,
    cover: "PATH_TO_COVER",
    url: "URL_TO_BOOK_IN_LIBRARY_OR_WEBSHOP"
}
```

## A collection of books

```json
{
    title: "SOME_TITLE",
    type: "collection",
    items: [
        "LIST_OF_BOOKS"
    ]
}
```

## A timeline

A timeline is a list of collections, which are usually years.

```json
{
    title: "SOME_TITLE_(optional)",
    "type": "timeline",
    "items": [
        "LIST_OF_COLLECTIONS"
    ]
}
```
