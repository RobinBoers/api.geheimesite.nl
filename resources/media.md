# Video format

The JSON returned by the `/resources/videos` endpoint is structured using the media format, which described below.

The API always returns a `group`.

## A single item

```json
{
    title: "SOME_TITLE",
    creator: "SOME_AUTHOR",
    url: "URL_TO_THE_MEDIA"
}
```

## A group of items

```json
{
    title: "SOME_TITLE",
    items: [
        "LIST_OF_ITEMS"
    ]
}
```
