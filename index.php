<!DOCTYPE html>
<?php require_once("config.php"); ?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Youtube-dl WebUI</title>
        <link rel="stylesheet" href="css/bootstrap.css" media="screen">
        <link rel="stylesheet" href="css/bootswatch.min.css">
    </head>
    <body>
        <div class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $mainPage; ?>">Youtube-dl WebUI</a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?php echo $mainPage; ?>">Download</a></li>
                    <li><a href="<?php echo $listPage; ?>">List of videos</a></li>
                </ul>
            </div>
        </div>
        <div class="container">
            <h1>Download</h1>
<?php
    if(isset($_GET['url']) && !empty($_GET['url']))
    {
        $url = $_GET['url'];
        $cmd = 'youtube-dl -o ' . escapeshellarg('./'.$folder.'%(title)s-%(uploader)s.%(ext)s') . ' ' . escapeshellarg($url) . ' 2>&1';
        exec($cmd, $output, $ret);
        if($ret == 0)
        {
            echo '<div class="alert alert-success">
                    <strong>Download succeed !</strong> <a href="'.$listPage.'" class="alert-link">Link to the video</a>.
                </div>';
        }
        else{
            echo '<div class="alert alert-dismissable alert-danger">
                    <strong>Oh snap!</strong> Something went wrong. Error code : <br>';
            foreach($output as $out)
            {
                echo $out . '<br>'; 
            }
            echo '</div>';
        }
    }
    else{?>
            <form class="form-horizontal" action="<?php echo $mainPage; ?>">
                <fieldset>
                    <div class="form-group">
                        <div class="col-lg-10">
                            <input class="form-control" id="url" name="url" placeholder="Link" type="text">
                        </div>
                        <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary">Download</button>
                        </div>
                    </div>
                    
                </fieldset>
            </form>
            <br>

            <?php destFolderExists($folder);?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Info</h3></div>
                        <div class="panel-body">
                            <p>Free space : <?php if(file_exists($folder)){ freeSpace(disk_free_space("./".$folder));} else {echo "Folder not found";} ?></b></p>
                            <p>Download folder : <?php echo $folder ;?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Help</h3></div>
                        <div class="panel-body">
                            <p><b>How does it work ?</b></p>
                            <p>Simply paste your video link in the field and click "Download"</p>
                            <p><b>With which sites does it works ?</b></p>
                            <p><a href="http://rg3.github.io/youtube-dl/supportedsites.html">Here</a> is the list of the supported sites</p>
                            <p><b>How can I download the video on my computer ?</b></p>
                            <p>Go to "List of videos", choose one, right click on the link and do "Save target as ..." </p>
                        </div>
                    </div>
                </div>
            </div>
<?php
    }
?>
        </div><!-- End container -->
        <footer>
            <div class="well text-center">
                <p><a href="https://github.com/p1rox/Youtube-dl-WebUI" target="_blank">Fork me on Github</a></p>
                <p>Created by <a href="https//twitter.com/p1rox" target="_blank">@p1rox</a> - Web Site : <a href="http://p1rox.fr" target="_blank">p1rox.fr</a></p>
            </div>
        </footer>
    </body>
</html>

<?php

function freeSpace($Bytes)
{
    $Type = array("", "Ko", "Mo", "Go", "To");
    $Index = 0;
    while($Bytes >= 1024)
    {
        $Bytes /= 1024;
        $Index++;
    }
    return(round($Bytes) . " " . $Type[$Index]);
}

function destFolderExists($destFolder)
{
    if(!file_exists($destFolder))
    {
        echo '<div class="alert alert-danger">
                <strong>Error : </strong> Destination folder doesn\'t exist or is not found here. 
            </div>';
    }
}

?>
