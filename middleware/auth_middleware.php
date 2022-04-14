<?php

    if(!isset($_SESSION['allowed'])){
        header("Location: ". LOGIN_PAGE . "");
    }