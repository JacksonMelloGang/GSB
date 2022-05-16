$(".search-input").on("input", function search() {
    // empty result
    document.getElementById("search-result").innerHTML = "";

    // when search input is empty, hide result
    if ($(".search-input").val() == "") {
        $('#search-result').toggle(false);
        return;
    };

    // otherwise show it and add loading effect & disable scrolling
    $('#search-result').toggle(true);
    $('#search-result').addClass('loading-effect');
    document.getElementById('search-result').style.overflow = 'hidden';
    console.log(window.location.host);
    // make ajax request to get data from search.php
    $.ajax({
        async: true,
        url: window.location.protocol + "//" + window.location.host + "/search.php?search=" + $(".search-input").val(),
        timeout: 5000,
        success: function (data) {
            $('#search-result').removeClass('loading-effect')
            document.getElementById('search-result').style.overflow = 'scroll';
            document.getElementById("search-result").innerHTML = "<ul style='list-style: none;'>" + data + "</ul>";
        }
    });
});

// hide #search-result when clicking outside of it
$(document).mouseup(function (e) {
    var container = $("#search-result");

    // if the target of the click isn't the container
    if (!container.is(e.target)) {
        container.hide();
    }
});