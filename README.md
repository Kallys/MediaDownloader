# Media Downloader

## Description
Media Downloader is a light web interface for [Youtube-dl](https://github.com/rg3/youtube-dl).
By hosting your own media downloader, you, your friends and your familly are allowed to use youtube-dl to download music or video on the web without installing anything nor using a terminal.

A lot of sites are supported (700+), including YouTube, dailymotion, Bandcamp, Facebook, SoundCloud, GoogleDrive, Imgur, Instagram, Vimeo, Vevo, twitch, Twitter... (a full list is available [here](http://rg3.github.io/youtube-dl/supportedsites.html)).

After the download you can stream your videos from your web browser or save it on your computer directly from the list page.
Media Downloader supports simultaneous downloads in background and now allows you to queuing them!

## News
- Queuing downloads
- New administration page
- New design

## Requirements
- A web server
- Composer
- PHP >= 7
- Python 2.7 for Youtube-dl
- [Youtube-dl](https://github.com/rg3/youtube-dl)

Optional:
- avconv or ffmpeg (required for "Best Ever" quality since it will merge both audio and video best available quality)

## How to install?
1. Clone this repo in your web folder (ex: /var/www).
2. Run composer install inside the cloned folder
3. Check permissions 
  - everything must be readable for www user
  - temp/, resources/databases, resources/logs/, resources/sessions/, public/downloads/ must be readable for www user
4. Double check permissions!
5. Adapt public/.htaccess to your configuration (subfolder or subdomain)
6. Do your web server configuration
7. Go to your page using a web browser and follow web installation !

## CSS Theme
[Lumen](https://bootswatch.com/lumen/)

## Screenshots
### Home page
![Home page screenshot](https://github.com/Kallys/MediaDownloader/raw/dev/public/img/home.jpg)
### Add links page
![Links page screenshot](https://github.com/Kallys/MediaDownloader/raw/dev/public/img/links.jpg)
### Media list page
![List page screenshot](https://github.com/Kallys/MediaDownloader/raw/dev/public/img/list.jpg)
### Admin page
![Admin page screenshot](https://github.com/Kallys/MediaDownloader/raw/dev/public/img/admin.jpg)

Feel free to fork and contribute if you like this project!
