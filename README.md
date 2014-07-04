# Youtube-dl WebUI

![Main](https://github.com/p1rox/Youtube-dl-WebUI/raw/master/img/main.png)
![List](https://github.com/p1rox/Youtube-dl-WebUI/raw/master/img/list.png)

## Description
Youtube-dl WebUI is a small web interface for youtube-dl. It allows you to host your own video downloader. After the download you can stream your videos from your web browser or save it on your computer directly from the list page.

## Requirements
- A web server (Apache or nginx)
- PHP latest version should be fine.
- [Youtube-dl](https://github.com/rg3/youtube-dl)

## How to install ?
1. Clone this repo in your web folder (ex: /var/www).
2. Edit config.php as you want it to work.
3. Create the video folder. 
4. Check permissions.
5. Access to your page (ex: index.php) to check that everything works.

## Set a password
1. Open config.php
2. Set security to 1
3. Find a password and hash it with md5 (you can do this with the md5.php page)

## CSS Theme
[Flatly](http://bootswatch.com/flatly/)

## License

Copyright (c) 2014 Armand VIGNAT

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
