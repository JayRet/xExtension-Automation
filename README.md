Automation Extension
====================
This extension allows you to send a custom GET/POST request each time a new entry is added. You can also specify the feeds on which this should happen.

QuickStart Guide with Docker
============================
1. start docker with a volume to point to an empty directory where you want to store the extension(s) in.
```sh
docker run \
    # ... some params \
    -v path/to/dir:/var/www/FreshRSS/extensions \
    freshrss/freshrss
```

2. after starting docker, git pull this repo in the directory.

3. enable the extension in FreshRss.

4. automate everything!!!!

Inserting Entry Data in Request
===============================
You can insert entry variables in the request value (not the key) by using `${variable}`.

The available variables are: id, guid, title, author, authors, content, link, date, machineReadableDate, lastSeen, dateAdded, isRead, isFavorite, isUpdated, feedId, tags and hash. Note that the content and tags are returned as strings.

