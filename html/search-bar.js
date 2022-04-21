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

                // make ajax request to get data from search.php
                $.ajax({
                    async: true,
                    url: "../search.php?search="+ $(".search-input").val(),
                    timeout: 5000,
                    success: function(data) {
                        $('#search-result').removeClass('loading-effect')
                        document.getElementById('search-result').style.overflow = 'scroll';
                        document.getElementById("search-result").innerHTML = "<ul style='list-style: none;'>" + data + "</ul>";
                    }
                });
            });