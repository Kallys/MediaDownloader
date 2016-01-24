# Media Downloader

## Description
Media Downloader is a light web interface for [Youtube-dl](https://github.com/rg3/youtube-dl).
By hosting your own media downloader, you, your friends and your familly are allowed to use youtube-dl to download music or video on the web without installing anything nor using a terminal.

A lot of sites are supported (700+), including YouTube, dailymotion, Bandcamp, Facebook, SoundCloud, GoogleDrive, Imgur, Instagram, Vimeo, Vevo, twitch, Twitter... (a full list is available [here](http://rg3.github.io/youtube-dl/supportedsites.html)).

After the download you can stream your videos from your web browser or save it on your computer directly from the list page.
It supports simultaneous downloads in background.

## News
- You can now choose which quality and format to download. 

## Requirements
- A web server (Apache or nginx)
- PHP >= 5.4
- Python 2.7 for Youtube-dl
- [Youtube-dl](https://github.com/rg3/youtube-dl)
- avconv or ffmpeg may be required for "Best Ever" quality (since it will merge both audio and video best available quality)

## How to install?
1. Clone this repo in your web folder (ex: /var/www).
2. Edit config.php as you want it to work.
3. Create the "downloads" folder. 
4. Check permissions.
5. Load index.php to check that everything works.

## Set a password
1. Open config/config.php
2. Set security to true
3. Find a password, hash it with md5 and replace the value of password.

Example (chosen password is root):

```
echo -n root|md5sum| sed 's/ .*//'
# Returns the hash 63a9f0ea7bb98050796b649e85481845
```

## CSS Theme
[Lumen](https://bootswatch.com/lumen/)

## Screenshots
![Main](https://github.com/Kallys/MediaDownloader/raw/master/img/main.png)
![List](https://github.com/Kallys/MediaDownloader/raw/master/img/list.png)

## Credits
Thanks for p1rox's nice project "[Youtube-dl WebUI](https://github.com/p1rox/Youtube-dl-WebUI)" from which Media Downloader is forked.

Feel free to fork and contribute if you like this project!