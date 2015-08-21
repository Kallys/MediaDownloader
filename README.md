# Youtube-dl WebUI

![Main](https://github.com/p1rox/Youtube-dl-WebUI/raw/master/img/main.png)
![List](https://github.com/p1rox/Youtube-dl-WebUI/raw/master/img/list.png)

## Description
Youtube-dl WebUI is a small web interface for youtube-dl. It allows you to host your own video downloader. 
After the download you can stream your videos from your web browser or save it on your computer directly from the list page.
It supports simultaneous downloads in background.

### You can now extract the audio of a video and download multiple videos at the same time !

## Requirements
- A web server (Apache or nginx)
- PHP latest version should be fine.
- Python 2.7 for Youtube-dl
- [Youtube-dl](https://github.com/rg3/youtube-dl)
- avconv or other is required for audio extraction (from youtube-dl doc) :
`-x, --extract-audio convert video files to audio-only files (requires ffmpeg or avconv and ffprobe or avprobe)`

## How to install ?
1. Clone this repo in your web folder (ex: /var/www).
2. Edit config.php as you want it to work.
3. Create the "videos" folder. 
4. Check permissions.
5. Load index.php to check that everything works.

## Set a password
1. Open config/config.php
2. Set security to true
3. Find a password, hash it with md5 and replace the value of password.

Example (chosen password is root):

`$ echo -n root|md5sum
63a9f0ea7bb98050796b649e85481845
`

## CSS Theme
[Lumen](https://bootswatch.com/lumen/)

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
