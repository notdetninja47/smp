<?php
require_once ("model/notes.php");
require_once ("functions.php");
?>
<!DOCTYPE html>
<html lang="en" ng-app="noteApp">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>Your notes</title>
        <!-- CSS  -->
        <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>
    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="js/materialize.js"></script>
    <script src="js/init.js"></script>
    <script>
        $(document).ready(function() {
            $('.modal-trigger').leanModal();
        });
        function opedEditModal(noteId){
            $('#editingModal').openModal();
            var title = document.getElementById("title-" + noteId).innerHTML;
            var text = document.getElementById("text-" + noteId).innerHTML;

            $('#note-id').val(noteId);
            $('#note-title').val(title);
            $('#note-text').val(text);
            $('#note-text').trigger('autoresize');

        }
        function openNewNoteModal(){
            $('#newNoteModal').openModal();
            var text = document.getElementById("text-" + noteId).innerHTML;
            $('#new-note-user-id').val($_SESSION["UserId"]);
        }
    </script>

     <div class="navbar-fixed">
        <nav class="amber accent-4" role="navigation">
            <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo center"><?php echo (substr($_SESSION["UserEmail"],0,strpos($_SESSION["UserEmail"],"@"))."(ID = ${_SESSION["UserId"]})");?></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="javascript:openNewNoteModal()" class="white-text">New note</a></li>
                    <li><a href="actions/logout.php">Log out</a></li>
                </ul>
                <ul id="nav-mobile" class="side-nav">
                    <li><a href="javascript:openNewNoteModal()" class="white-text">New note</a></li>
                    <li><a href="actions/logout.php">Log out</a></li>
                </ul>
                <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
            </div>
        </nav>

        </div>

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
                        <div class="card $color teal">
                            <div class="card-content white-text">
                                <span id="title-${note["id"]}" class="card-title">${note["title"]}</span>
                                <p>${note["date"]}</p>
                                <p id="text-${note["id"]}" >${note["text"]}</p>
                            </div>
                            <div class="card-action">
                                <a href="javascript:opedEditModal(${note['id']})" class="white-text">Edit</a>
                                <a href="#" class="white-text">Share</a>
                                <a href="#" class="white-text">Archive</a>
                            </div>
                        </div>
                    </div>

EOL;
                    }
                ?>
                    <div id="editingModal" class="modal bottom-sheet">
                        <form method="post" id="${note['id']}" action="actions/change-note.php">
                        <div class="modal-content">
                            <h4>Editing note</h4>
                                <div class="input-field">
                                    <i class="material-icons prefix">mode_edit</i>
                                    <input placeholder="Title" id="note-title" name="note-title" type="text" class="validate">
                                </div>
                                <div class="input-field">
                                    <textarea id="note-text" name="note-text" class="materialize-textarea large" placeholder="Note text"></textarea>
                                </div>
                                <input type="hidden" id="note-id" name="note-id">
                        </div>
                        <div class="modal-footer">
                            <input class="modal-action modal-close waves-effect waves-green btn-flat" type="submit" value="Confirm">
                            <a href="#!" class=" modal-action modal-close waves-effect waves-red btn-flat right">Cancel</a>
                        </div>
                        </form>

                    </div>
<!-- ///////////////////////////////////////////////////////////////-->
                    <div id="newNoteModal" class="modal bottom-sheet">
                        <form method="post" id="new-note-form" action="actions/create-note.php">
                            <div class="modal-content">
                                <h4>Creating new note</h4>
                                <div class="input-field">
                                    <i class="material-icons prefix">mode_edit</i>
                                    <input placeholder="Title" id="new-note-title" name="note-title" type="text" class="validate">
                                </div>
                                <div class="input-field">
                                    <textarea id="new-note-text" name="note-text" class="materialize-textarea large" placeholder="Note text"></textarea>
                                </div>
                                <input type="hidden" id="new-note-user-id" name="user-id" value=<?php echo $_SESSION["UserId"]; ?>>
                            </div>
                            <div class="modal-footer">
                                <input class="modal-action modal-close waves-effect waves-green btn-flat" type="submit" value="Create">
                                <a href="#!" class=" modal-action modal-close waves-effect waves-red btn-flat right">Cancel</a>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>



    </body>
</html>