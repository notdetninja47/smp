<?php
require_once("../model/notes.php");
require_once("../model/users.php");
require_once("../functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <title>Your notes</title>
    <!-- CSS  -->
    <link href="../css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="../css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="../js/materialize.js"></script>
<script src="../js/init.js"></script>
<script>
    $(document).ready(function () {
        $('.modal-trigger').leanModal();
    });
    function opedEditModal(noteId) {
        var title = document.getElementById("title-" + noteId).innerHTML;
        var text = document.getElementById("text-" + noteId).innerHTML;

        $('#note-id').val(noteId);
        $('#note-title').val(title);
        $('#note-text').val(text);
        $('#note-text').trigger('autoresize');

        $('#editingModal').openModal();

    }
    function openNewNoteModal() {
        $('#newNoteModal').openModal();
    }
    function openShareModal(noteId) {
        $('#share-note-id').val(noteId);
        $('#shareNoteModal').openModal();
    }
    function openColorModal(noteId) {
        $('#pick-color-note-id').val(noteId);
        $('#pickColorModal').openModal();
    }
    function openDateModal() {
        $('#pickDateModal').openModal();
    }

</script>

<div class="navbar-fixed">
    <nav class="amber accent-4" role="navigation">
        <div class="nav-wrapper container"><a id="logo-container" href="#"
                                              class="brand-logo center"><?php echo(substr($_SESSION["UserEmail"], 0, strpos($_SESSION["UserEmail"], "@")) . "(ID = ${_SESSION["UserId"]})"); ?></a>
            <ul class="left hide-on-med-and-down">
                <li><a href="archive-notes-page.php" class="white-text">Archive</a></li>
                <li><a href="shared-notes-page.php" class="white-text">Inbox</a></li>
            </ul>
            <ul class="right hide-on-med-and-down">
                <li><a href="javascript:openDateModal()"><i class="material-icons">today</i></a></li>
                <li><a href="../actions/logout.php">Log out</a></li>
            </ul>
            <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="side-nav">
                <li><a href="archive-notes-page.php">Archive</a></li>
                <li><a href="shared-notes-page.php">Inbox</a></li>
                <li><a href="../actions/logout.php">Log out</a></li>
            </ul>
            <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
        </div>
    </nav>

</div>
<div class="container" id="container">
    <?php
        if(isset($_POST["date"]) && $_POST["date"]!="")
            echo '<a href="notes-page.php" class="btn-large red">Show all</a>';
    ?>

    <div class="section">
        <div class="row">
            <?php
            $allNotes = Notes::getAllUserNotes($_SESSION["UserId"]);

            if(isset($_POST["date"]) && $_POST["date"]!="")
                $notes = Notes::filterNotesByDate($allNotes, $_POST["date"]);
            else $notes = $allNotes;

            $rowLeft = 12;
            echo '<div class="row">';
            foreach ($notes as $note) {
                if ($note["deleted"] == 0) {
                    if (strlen(trim($note["title"])) < 33 && strlen(trim($note["text"])) < 129)
                        $m = 4;
                    else if (strlen(trim($note["title"])) < 65 && strlen(trim($note["text"])) < 257)
                        $m = 6;
                    else if (strlen(trim($note["title"])) < 65 && strlen(trim($note["text"])) < 513)
                        $m = 8;
                    else $m = 12;
                    $color = Notes::getNoteTypeColor($note["typeId"]);

                    $rowLeft -= $m;
                    if ($rowLeft < 0) {
                        $rowLeft = 12 - $m;
                        echo '</div><div class="row">';
                    }
                    echo <<<EOL
                    <div class="col s12 m$m"">

                        <div class="card $color">
                            <div class="card-content white-text">
                                                  <a href="javascript:opedEditModal(${note['id']})" class="white-text">
                                <span id="title-${note["id"]}" class="card-title" >${note["title"]}</span>
                                <p>${note["date"]}</p>
                                <p id="text-${note["id"]}" >${note["text"]}</p>
                                                            </a>
EOL;
                    $recipients = Notes::getRecipientsIds($note["id"]);
                    if (count($recipients) > 0) {
                        echo '<br><br><p>Shared with';
                        foreach ($recipients as $recipient) {
                            $recipient_email = Users::getUserEmail($recipient["recipientId"]);
                            echo <<<EOL
                        <span class="chip" style="margin:1px">
                            $recipient_email
                            <a href="../actions/dismiss-note-share.php?note-id=${note["id"]}&recipient-id=${recipient["recipientId"]}">
                                <i class="material-icons">close</i>
                            </a>
                        </span>
EOL;
                        }
                        echo '</p>';
                    }
                    echo <<<EOL
                            </div>
                            <div class="card-action">
                            <div class="fixed-action-btn horizontal" style="position: absolute; right:5px;">
                                <a class="btn-flat btn-floating btn-large small $color">
                                  <i class="large material-icons">reorder</i>
                                </a>
                                <ul >
                                  <li><a href="javascript:opedEditModal(${note['id']})" class="btn-floating red"><i class="material-icons">mode_edit</i></a></li>
                                  <li><a href="javascript:openShareModal(${note['id']})" class="btn-floating yellow darken-1"><i class="material-icons">supervisor_account</i></a></li>
                                  <li><a href="../actions/delete-note.php?note-id=${note['id']}" class="btn-floating green"><i class="material-icons">delete</i></a></li>
                                  <li><a href="javascript:openColorModal(${note['id']})" class="btn-floating blue"><i class="material-icons">invert_colors</i></a></li>
                                </ul>
                              </div>
                            </div>
                        </div>
                    </div>
EOL;
                }
            }
            echo '</div>
              <div class="fixed-action-btn horizontal" style="bottom: 45px; right: 24px;">
                <a href="javascript:openNewNoteModal()" class="btn-floating btn-large red">
                  <i class="large material-icons">add</i>
                </a>
              </div>';
            ?>
            <div id="editingModal" class="modal bottom-sheet">
                <form method="post" id="${note['id']}" action="../actions/change-note.php">
                    <div class="modal-content">
                        <h4>Editing note</h4>
                        <div class="input-field">
                            <i class="material-icons prefix">mode_edit</i>
                            <input placeholder="Title" id="note-title" name="note-title" type="text" length="256"
                                   class="validate">
                        </div>
                        <div class="input-field">
                            <textarea id="note-text" name="note-text" length="4096" class="materialize-textarea large"
                                      placeholder="Note text"></textarea>
                        </div>
                        <input type="hidden" id="note-id" name="note-id">
                    </div>
                    <div class="modal-footer">
                        <input class="modal-action modal-close waves-effect waves-green btn-flat" type="submit"
                               value="Confirm">
                        <a href="#!" class=" modal-action modal-close waves-effect waves-red btn-flat right">Cancel</a>
                    </div>
                </form>
            </div>
            <div id="newNoteModal" class="modal bottom-sheet">
                <form method="post" id="new-note-form" action="../actions/create-note.php">
                    <div class="modal-content">
                        <h4>Creating new note</h4>
                        <div class="input-field">
                            <i class="material-icons prefix">mode_edit</i>
                            <input placeholder="Title" id="new-note-title" name="note-title" type="text"
                                   class="validate" length="256">
                        </div>
                        <div class="input-field">
                            <textarea id="new-note-text" name="note-text" class="materialize-textarea large"
                                      placeholder="Note text" length="4096"></textarea>
                        </div>
                        <input type="hidden" id="new-note-user-id" name="user-id"
                               value=<?php echo $_SESSION["UserId"]; ?>>
                    </div>
                    <div class="modal-footer">
                        <input class="modal-action modal-close waves-effect waves-green btn-flat" type="submit"
                               value="Create">
                        <a href="#!" class=" modal-action modal-close waves-effect waves-red btn-flat right">Cancel</a>
                    </div>
                </form>
            </div>
            <div id="shareNoteModal" class="modal bottom-sheet">
                <form method="post" id="new-note-form" action="../actions/share-note.php">
                    <div class="modal-content">
                        <h4>Select person to share your note with</h4>
                        <div class="input-field">
                            <i class="material-icons prefix">mode_edit</i>
                            <input placeholder="ID of person" id="recipientId" name="recipient-id" type="text"
                                   class="validate">
                        </div>
                        <input type="hidden" id="share-note-id" name="note-id">
                    </div>
                    <div class="modal-footer">
                        <input class="modal-action modal-close waves-effect waves-green btn-flat" type="submit"
                               value="Share">
                        <a href="#!" class=" modal-action modal-close waves-effect waves-red btn-flat right">Cancel</a>
                    </div>
                </form>
            </div>
            <div id="pickColorModal" class="modal bottom-sheet">
                <form action="../actions/change-color.php" method="post">
                    <div class="modal-content">
                        <h4>Pick new color</h4>
                        <?php
                        echo <<<EOL
                            <input type="hidden" id="pick-color-note-id" name="note-id">
EOL;
                        $colors = Notes::getAllNoteTypes();
                        foreach ($colors as $color) {
                            echo <<<EOL
                                <input type="submit" class="card btn-large circle ${color["name"]}" style="text-indent:-9999px;" name="color" value="${color["id"]}"></button>
EOL;
                        }
                        ?>

                    </div>
                </form>
            </div>
            <div id="pickDateModal" class="modal" style="width: 90%; height:100%">
                <form action="notes-page.php" method="post">
                    <br>
                    <div class="modal-content">
                        <h4>Pick date</h4>
                        <input class="datepicker" name="date" required/>
                        <input type="submit" class="btn-large red btn-flat white-text submit" value="OK"/>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $('.datepicker').pickadate({
        height: '100%',
        labelMonthNext: 'Next month',
        labelMonthPrev: 'Previous month',
        labelMonthSelect: 'Select a month',
        labelYearSelect: 'Select a year',
        monthsFull: [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
        monthsShort: [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],
        weekdaysFull: [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ],
        weekdaysShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ],
        weekdaysLetter: [ 'S', 'M', 'T', 'W', 'T', 'F', 'S' ],
        today: 'Today',
        clear: '',
        close: 'Confirm'
    });
</script>

</body>
</html>