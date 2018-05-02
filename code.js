$(function() {

    //VARIABLES

    // The article matching the name in the search results box which the user clicks on
    var requiredArticle;

    //Array of information with which to populate the Info Display Panel
    var articleArray;

    //Array of keys in information array
    var articleArrayKeys;

    // Determined by the table which supplies the information on the subject chosen by the user, and therefore the type of subject
    var subjectType;

    // Search results to put in the div below the search form
    var searchResults = [];

    // Commit search result to an array of divs
    var viewedArticles = [];

    //Hide Browse InfoDisplayTab and Search Results Box
    $('#infoDisplayTabs').hide();
    $('#searchResultsBox').hide();

    //NB: YOU MAY HAVE TO TAKE THE FOLLOWING LINES OUT OF THE DOCUMENT-READY FUNCTION

    //Search Form & Button

    // Search form with Autocomplete Function

    //AJAX call to getautocompletelibrary.php to draw down array of names to use for autocomplete function and commit search results of chosen item to search results box
    $.ajax({
        url: "getautocompletelibrary.php",
        dataType: "json",
        success: function(data) {
            $("#search").autocomplete({
                source: data,
            });
            $("#submitbutton").on('click', function(event) {
                event.preventDefault();
                $('#searchResultsBox').show();
                $("#searchResults").show();
                var searchText = document.getElementById("search").value;
                if (searchResults.indexOf(searchText) == -1) {
                    searchResults.push(searchText);
                };
                console.log(searchResults);
                //Fill Search Results Box and link each search result to Info Display Modal.
                $("#searchResults").html('');
                for (j = 0; j < searchResults.length; j++) {
                        $("#searchResults").prepend("<div class='searchResultsItem'><h4><a class='searchResultsItemLink' data-toggle='modal' data-target='#modal' onclick='loadArticle();'>" + searchResults[j] + "</a></h4></div>");
                };
            });
        },
    });

    var additionalReadingItem;
    if (additionalReadingItem) {
        addNewSearchResults(additionalReadingItem);
    };

}); //END OF DOCUMENT READY FUNCTION - DO NOT MOVE

// GLOBAL FUNCTIONS

function loadArticle() {
    $('.searchResultsItem').off().click(function() {
        requiredArticle = event.target.innerText;
        requiredArticle = requiredArticle.replace("'", "\'");
        console.log(requiredArticle);
        $.ajax({
            url: 'search.php',
            method: 'POST',
            data: { 'name': requiredArticle },
            error: function() { console.log("AJAX call failed") },
            success: function(data) {
                //Clear search results box of all items apart from the one(s) clicked on and viewed in the modal during this reload of the page (i.e. keep a record of all the articles seen) 
                searchResultsBoxTitle.innerText = 'You have viewed or requested article(s) on: -';
                $("#searchResults").empty();
                $("#searchResults").prepend("<div class='searchResultsItem'><h4><a class='searchResultsItemLink' data-toggle='modal' data-target='#modal' onclick='loadArticle();'>" + requiredArticle + "</a></h4></div>");
                //What comes back is a JSON string
                console.log(data);
                //JSON string needs to be converted to a JavaScriptarray
                articleArray = $.parseJSON(data);
                //Display Article in Info Display box
                articleArrayKeys = Object.keys(articleArray);
                subjectType = articleArrayKeys[0];
                // console.log(articleArray);
                // console.log(subjectType);
                // Empty content from previous any search
                $('#modalTitle').html("");
                $('#personName').html("");
                $('#personLifeDetails').html("");
                $('#personSymbols').html("");
                $('#personStillsGallery').html("");
                $('#personBiog').html("");
                $('#videoVault').html("");
                // Show person layout, beginning with what goes in the top tab
                $('#personLayout').show();
                $('#modalTitle').html(articleArray['display_name'].toUpperCase());
                console.log(articleArray['personCountryName']);
                // Display person's country flag, display name and any honours
                $('#personName').html("<span id='personFlag'><img src='images/" + articleArray['personCountryFlag'] + "'><span style='display:none'>" + articleArray['personCountryName'] + "</span></span>" + articleArray['name_prefix'] + "<span id='displayName'>" + articleArray['display_name'] + "</span> <span id='honours'>" + articleArray['honours'] + " </span>");
                // If the person has a nickname, put this first in the life details sub-heading
                if (articleArray['nickname']) {
                    $('#personLifeDetails').html("<div id='nickname'>" + articleArray['nickname'] + "</div>");
                };
                // Display life details DOB/POB underneath country flag, display name and honours
                $('#personLifeDetails').append("<div class='birthDeath'>Born: " + moment(articleArray['date_of_birth']).format('Do MMMM YYYY') + ", " + articleArray['place_of_birth'] + ".</div>");
                //If the person has died,  place DOD/POD after DOB/POB details and place bracketed years after player's display name
                if (articleArray['date_of_death']) {
                    $('#personLifeDetails').append("<div class='birthDeath'>Died: " + moment(articleArray['date_of_death']).format('Do MMMM YYYY') + ", " + articleArray['place_of_death'] + ".</div>");
                    $('#personName').append("<span id='birthDeathBrackets'>(" + moment(articleArray['date_of_birth']).format('YYYY') + "-" + moment(articleArray['date_of_death']).format('YYYY') + ")</span>");
                };

                // Check which boolean categories are showing 1 for "true" and attach the relevant icon: -
                var booleanCategoriesArray = [articleArray['player_bool'], articleArray['exec_bool'], articleArray['journo_bool'], articleArray['ref_bool'], articleArray['broadcaster_bool'], articleArray['wpc_bool'], articleArray['ukc_bool'], articleArray['ranking_bool'], articleArray['master_bool'], articleArray['natchamp_bool'], articleArray['wac_bool'], articleArray['147_bool']];
                var playerIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Player" data-placement="bottom" src="images/player.png"> ';
                var execIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Executive or Promoter" data-placement="bottom" src="images/executive.png"> ';
                var journoIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Journalist" data-placement="bottom" src="images/journalist.png"> ';
                var refereeIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Referee" data-placement="bottom" src="images/referee.png"> ';
                var broadcasterIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Broadcaster" data-placement="bottom" src="images/broadcaster.png"> ';
                var wpcIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="World Professional Champion" data-placement="bottom" src="images/wpc.png"> ';
                var ukcIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="UK Champion" data-placement="bottom" src="images/lion.png"> ';
                var rankingIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Won Ranking Tournament" data-placement="bottom" src="images/ranking.png"> ';
                var mastersIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Won the Masters" data-placement="bottom" src="images/masters.png"> ';
                var natchampIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="National Champion" data-placement="bottom" src="images/natchamp.png"> ';
                var wacIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="World Amateur Champion" data-placement="bottom" src="images/wac.png"> ';
                var maximumIconHTML = '<img class="booleanIcon" data-toggle="tooltip" title="Made a 147 in competition" data-placement="bottom" src="images/147.png"> ';
                var booleanIconsArray = [playerIconHTML, execIconHTML, journoIconHTML, refereeIconHTML, broadcasterIconHTML, wpcIconHTML, ukcIconHTML, rankingIconHTML, mastersIconHTML, natchampIconHTML, wacIconHTML, maximumIconHTML];
                var personSymbolsHTML = '';
                for (i = 0; i < booleanCategoriesArray.length; i++) {
                    if (booleanCategoriesArray[i] == 1) {
                        personSymbolsHTML = personSymbolsHTML + booleanIconsArray[i];
                    }
                };
                $('#personSymbols').html(personSymbolsHTML);

                // Fill the person's stills gallery
                var personPictureURLKeyMain = 'personPictureURL';
                var personPictureCaptionKeyMain = 'personPictureCaption';
                var personPictureCreditKeyMain = 'personPictureCredit';
                var numberofPicturesforPerson = testDataforMediaURL(data, personPictureURLKeyMain);
                // console.log(numberofPicturesforPerson);
                if (articleArray['personPictureURL1']) {
                    for (i = 1; i < (numberofPicturesforPerson + 1); i++) {
                        personPictureURLKey = personPictureURLKeyMain + i;
                        personPictureCaptionKey = personPictureCaptionKeyMain + i;
                        personPictureCreditKey = personPictureCreditKeyMain + i;
                        $('#personStillsGallery').addClass("col-md-2");
                        $('#personBiog').addClass("col-md-6");
                        $('#personStillsGallery').append("<img src='" + articleArray[personPictureURLKey] + "'><br/><span class='mediaCaptionAndCredit'>" + articleArray[personPictureCaptionKey] + " Picture courtesy of " + articleArray[personPictureCreditKey] + "</span>");
                    };
                } else {
                    $('#personStillsGallery').removeClass("col-md-2").addClass("col-md-0");
                    $('#personBiog').removeClass("col-md-6").addClass("col-md-8");
                };

                // Fill the person's biography text section
                var majorTournamentWins = articleArray['majorTournamentWins'];
                var personBiogHTML = articleArray['biography'];
                // console.log(personBiogHTML);
                if (majorTournamentWins) {
                    $('#personBiog').append(personBiogHTML + "<p><h3><strong>Major tournament victories: -</strong></h3></p>" + majorTournamentWins);
                } else {
                    $('#personBiog').append(personBiogHTML);
                };

                // Fill the person's video vault
                var personVideoURLKeyMain = 'personVideoURL';
                var personVideoCaptionKeyMain = 'personVideoCaption';
                var personVideoCreditKeyMain = 'personVideoCredit';
                var numberofVideosforPerson = testDataforMediaURL(data, personVideoURLKeyMain);
                // console.log(numberofVideosforPerson);
                if (articleArray['personVideoURL1']) {
                    for (i = 1; i < (numberofVideosforPerson + 1); i++) {
                        personVideoURLKey = personVideoURLKeyMain + i;
                        personVideoCaptionKey = personVideoCaptionKeyMain + i;
                        personVideoCreditKey = personVideoCreditKeyMain + i;
                        $('#videoVault').append("<div class='row' style='display: flex; align-items: center;'><div style='float: left;'><iframe width='200' height='150' src='" + articleArray[personVideoURLKey] + "' frameborder='0' gesture='media' allow='encrypted-media' allowfullscreen></iframe></div><div class='mediaCaptionAndCredit' style='white-space: normal; '>" + articleArray[personVideoCaptionKey] + " Video courtesy of " + articleArray[personVideoCreditKey] + "</div></div>");
                        // console.log(articleArray[personVideoURLKey]);
                    };
                    // console.log(numberofVideosforPerson);
                };
                requiredArticle = null;
            }
        });
    });
}

function testDataforMediaURL(string, substring) {
    var n = 0;
    var position = 0;
    while (true) {
        position = string.indexOf(substring, position);
        if (position != -1) {
            n++;
            position += substring.length;
        } else {
            break;
        }
    };
    return (n);
};

function addNewSearchResults(additionalReadingItem) {
    additionalReadingItem = event.target.innerText;
    console.log(additionalReadingItem);

    function appendToNewSearchResults() {
        $("#searchResults").prepend(
            "<div class='searchResultsItem'><h4><a class='searchResultsItemLink' data-toggle='modal' data-target='#modal' onclick='loadArticle();'>" + additionalReadingItem + "</a></h4></div>");
    };
    appendToNewSearchResults();
}