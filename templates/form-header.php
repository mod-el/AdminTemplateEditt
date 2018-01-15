<?php
if(!isset($_GET['print'])){
    ?>
    <div id="history-box" style="right: -15%">
        <div class="text-center pad10v">
            [<a href="#" onclick="historyStepBack(); return false"> indietro </a>]
            [<a href="#" onclick="historyStepForward(); return false"> avanti </a>]
            [<a href="#" onclick="switchHistoryBox(); return false"> chiudi </a>]
        </div>
        <div id="links-history"></div>
    </div>

    <div class="text-right">
        [<a href="#" onclick="switchHistoryBox(); return false"> storico </a>]
    </div>
    <?php
}
?>

<form action="" method="post" id="adminForm" name="adminForm" onsubmit="save(); return false" data-filled="0">
	<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
	<input style="display:none" type="text" name="fakeusernameremembered" />
	<input style="display:none" type="password" name="fakepasswordremembered" />

    <input type="hidden" name="_model_version" value="1" />
