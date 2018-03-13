<?php
    $link = mysqli_connect(//DETAILS REDACTED);
    if(mysqli_connect_error()){
        die ('ERROR: unable to connect to database: ' . mysqli_connect_error());
    }
?>