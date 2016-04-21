<?php
require_once ("model/notes.php");
?>
<!DOCTYPE html>
<html lang="en" ng-app="noteApp">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Note-Taking App</title>
        <!-- CSS  -->
        <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    </head>
    <body ng-controller="noteCtrl">
        <nav class="amber accent-4" role="navigation">
            <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo"><?php echo("Welcome, ".$_SESSION["UserEmail"]."!");?></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="logout.php">Log out</a></li>
                </ul>

                <ul id="nav-mobile" class="side-nav">
                    <li><a href="logout.php">Log out</a></li>
                </ul>
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
            </div>
        </nav>

        <div class="container" id="container">
            <div class="section">
                <div class="row"><!--Без этого div выстроятся в одну линию-->

                    <?php
                    $notes = Notes::getAllUserNotes($_SESSION["UserId"]);
                    foreach($notes as $note){

                        if(strlen(trim($note["title"]))<33&&strlen(trim($note["text"]))<129)
                            $m = 4;
                        else if(strlen(trim($note["title"]))<65&&strlen(trim($note["text"]))<257)
                                $m = 6;
                             else if(strlen(trim($note["title"]))<65&&strlen(trim($note["text"]))<513)
                                    $m = 8;
                                 else $m = 12;
                        $color = Notes::getNoteTypeColor($note["typeId"]);
                        echo <<<EOL
                    <div class="col s12 m$m">
                        <div class="card $color">
                            <div class="card-content white-text">
                                <span class="card-title">${note["title"]}</span>
                                <p>${note["text"]}</p>
                            </div>
                        </div>
                    </div>

EOL;

                    }
                ?>
                </div>
            </div>
        </div>


        <!--  Scripts-->
        <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script src="js/materialize.js"></script>
        <script src="js/init.js"></script>
    </body>
</html>