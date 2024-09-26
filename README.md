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

3. enable the extension in FreshRSS.

4. automate everything!!!!

Inserting Entry Data in Request
===============================
You can insert entry variables in the request value (not the key) by using `${variable}`.

The available variables are:
| param | code example | what it is |
| --- | --- | --- |
| id | `${id}` | this is the entry ID |
| guid | `${guid}` | this is the entry GUID |
| title | `${title}` | this is the entry title |
| author | `${author}` | this is the entry author |
| authors | `${authors}` | these are the entry authors |
| content | `${content}` | this is the content of the entry as a string |
| link | `${link}` | this is the entry link |
| date | `${date}` | this is the date the entry was written |
| machineReadableDate | `${machineReadableDate}` | this is the same as the date but in machine readable format |
| lastSeen | `${lastSeen}` | this is the date the entry was last seen |
| dateAdded | `${dateAdded}` | this is the date the entry was added to the feed |
| isRead | `${isRead}` | this is the boolean value that says if the entry was read |
| isFavorite | `${isFavorite}` | this is the boolean value that says if the entry was favorited  |
| isUpdated | `${isUpdatedd}` | this is the boolean value that says if the entry was updated  |
| feedId | `${feedId}` | this is the id of the feed the entry is in |
| tags | `${tags}` | these are the entry tags as a string |
| hash | `${hash}` | this is the entry hash |
