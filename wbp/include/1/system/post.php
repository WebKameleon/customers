<?php

    if (isset($KAMELEON_MODE) && $KAMELEON_MODE && Bootstrap::$main->session('editmode')>1) echo '</div>';
