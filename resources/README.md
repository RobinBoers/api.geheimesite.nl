# Resources API

The resources API simply returns static JSON files with data used in multiple projects, such as [my personal site](https://geheimesite.nl) and my [self-hosted microblog](https://micro.geheimesite.nl).

## Usage

The API has the following endpoints:

### Profile data

```api
GET /resources/profile
```

Returns data about me in the [`microformats`](https://microformats.org) format.

### Video list

```api
GET /resources/videos
```

Returns a list of videos that I liked, sorted by topic in the [media format](media.md)

### Books

```api
GET /resources/books/currently-reading
```

Returns the books that I'm currently reading in the [book format](book.md).

```api
GET /resources/books/finished
```

Returns the books that I finished reading, sorted by year.

```api
GET /resources/books/want-to-read
```

Returns the books I want to read.

### Subscription lists

```api
GET /resources/feeds/subscriptions.opml
```

Returns an OPML file containing all the feeds I follow in my RSS reader.

```api
GET /resources/feeds/videos.opml
```

Returns an OPML file containing RSS feeds for all my YouTube subscriptions.
