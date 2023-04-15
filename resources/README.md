# Resources API

The resources API simply returns static JSON files with data used in multiple projects, such as [my personal site](https://geheimesite.nl) and my [self-hosted microblog](https://micro.geheimesite.nl).

It has the following endpoints:

- `/resources/profile`: Returns data about me in the [`microformats`](https://microformats.org) format.

- `/resources/videos`: Returns a list of videos that I liked, sorted by topic in the [media format](media.md)

- `/resources/books/currently-reading`: Returns the books that I'm currently reading in the [book format](book.md).

- `/resources/books/finished`: Returns the books that I finished reading, sorted by year.

- `/resources/books/want-to-read`: Returns the books I want to read.

- `/resources/feeds/subscriptions.opml`: Returns an OPML file containing all the feeds I follow in my RSS reader.

- `/resources/feeds/videos.opml`: Returns an OPML file containing RSS feeds for all my YouTube subscriptions.
