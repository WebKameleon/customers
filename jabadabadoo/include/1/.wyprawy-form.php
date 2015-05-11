<?php

    if (isset($_GET['wyprawy-gen']) && $_GET['wyprawy-gen']) include __DIR__.'/system/wyprawy-gen.php';

    include __DIR__.'/system/wyprawy-form.html';
    echo '<hr size="1" style="margin:0"/>';
    echo '<a href="'.$self_link.$next_sign.'wyprawy-gen=1">Wygeneruj bazę</a>';