<?php
    ob_start();
    include ('connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Vintage Snooker</title>
    <link rel="icon" href="vintagesnookericon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="vintagesnookericon.ico" type="image/x-icon" />
    <!--STYLING-->

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed&amp;subset=latin-ext" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Site's own styling -->
    <link href="styling.css" rel="stylesheet">

    <!-- LIBRARY SCRIPTS -->

    <!-- jQuery required for Accordions -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/le-frog/jquery-ui.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- Moment.js required for date formatting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.2.1/moment.min.js"></script>

</head>

<body>

    <!-- NAVIGATION BAR -->

    <nav role="navigation" class="navbar navbar-custom navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand">Vintage Snooker</a>
                <button type="button" class="navbar-toggle" data-target="#navbarCollapse" data-toggle="collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse" id="navbarCollapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#mainHeading">Home</a></li>
                    <!-- <li><a href="#">Articles</a></li>
                    <li><a href="#">About this site</a></li>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Contact us</a></li> -->
                </ul>
            </div>
        </div>
    </nav>
    <!-- End of Navigation Bar -->

    <!-- HEADING -->

    <div class="container-fluid" id="headingContainer">
        <div id="mainHeading">
            <h1>Vintage Snooker</h1>
            <h2>THE STARS PAST AND PRESENT</h2>
        </div>
    </div>
    <!-- End of Heading -->

    <!-- SEARCH FORM -->
    <div id="searchformdiv">
        <form class="form-inline" method="post" id="searchform">
            <div class="form-group">
                <label for="search" class="sr-only">Search the database</label>
                <input type="text" placeholder="Start typing the name of a player" name="search" id="search">
            </div>
            <button class="btn btn-success btn-md" id='submitbutton'>Submit</button>
        </form>
    </div>
    <!-- End of Search Form -->

    <!-- SEARCH RESULTS BOX -->

    <div class='container-fluid'>
        <div class="col-md-offset-3 col-md-6" id="searchResultsBox">
            <h3 id='searchResultsBoxTitle'>Search Results: -</h3>
            <div id="searchResults">
                <!-- HTML from search results is fed here by the code.js file from data supplied by the AJAX call to search.php -->
            </div>
        </div>
    </div>
    <!-- End of Search Results Box -->

    <!-- INFORMATION MODAL -->

    <div class="modal fade bd-example-modal-lg" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- Put tab reference of chosen subject in between <h5> tags here **CODE COMPLETE**-->
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Person Modal content goes in here -->
                        <div id='personLayout' class='personLayout'>
                            <div id='personHeader' class='row'>
                                <div id='personVitalInfo' class='col-md-6'>
                                <!-- Person's Display Name and any honours go in between <div> tags here **CODE COMPLETE** -->
                                    <div id='personName'></div>
                                    <!-- Person's date and place of birth/death go in between <div> tags here along with nickname **CODE COMPLETE** -->
                                    <div id='personLifeDetails'></div>
                                </div>
                                <!-- Person's Symbols from Boolean fields go in between <div> tags here **CODE COMPLETE** -->
                                <div id='personSymbols' class='col-md-6'></div>
                            </div>   
                            <div id='personBody' class='row'>
                                <!-- Person's pictures go in between <div> tags here. NB: requires separate search on Database **CODE COMPLETE** -->
                                <div id='personStillsGallery' class='col-md-2' style='max-height: 375px; overflow-y: scroll;'></div>
                                <!-- Person's biography goes in between <div> tags here **CODE COMPLETE** -->
                                <div id='personBiog' class='col-md-6'></div>
                                <!-- Person's videos go in between <div> tags here. NB: requires separate search on Database **CODE COMPLETE** -->
                                <div id='videoVault' class='col-md-4' style='max-height: 375px; overflow-y: scroll;'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="container">
            <p>Videos are shown for illustrative and educational purposes only. No copyright infringement is intended.</p>
        </div>
    </div>
    <!-- End of Footer -->

    <!-- EVENT SCRIPTS -->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Site's own JavaScript file -->
    <script src='code.js'></script>

</body>

</html>

<?php ob_flush(); ?>