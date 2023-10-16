<!DOCTYPE html>
<html lang="fr">
<head>

    <!-- Basic Page Needs
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta charset="utf-8">
    <title>Préparation atelier</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Mobile Specific Metas
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FONT
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

    <!-- SCRIPTS
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <script
            src="https://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>

    <script type='text/javascript'>
        $(document).ready(function () {
            $("#export-button").click(
                function() {
                    var articleList = $('#articlesList tr');
                    if (articleList.length == 0) {
                        alert("Veuillez sélectionner au moins une erreur");
                        return false;
                    }

                    var data = [];
                    articleList.each(function() {
                        var row = [];
                        $(this).find('td').each(function() {
                            row.push($(this).text());
                        });
                        data.push(row);
                    });

                    var req = new XMLHttpRequest();
                    req.open("POST", "exportpdf.php", true);
                    req.responseType = "blob";
                    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    req.onreadystatechange = function ()
                    {
                        if (req.readyState === 4 && req.status === 200)
                        {
                            var blob=req.response;
                            var filename = "test.pdf";
                            var link = document.createElement('a');
                            link.setAttribute("type", "hidden"); // make it hidden if needed
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "erreurs.pdf";
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                            var file = new File([blob], filename, { type: 'application/force-download' });
                            //window.open(URL.createObjectURL(file));
                        }
                    };
                    req.send("data=" + JSON.stringify(data));
                }
            );

            $(".addKeyword").click(
                function () {
                    $("#loader").show();
                    var keywordKey = $(this).attr("data-key");
                    var keywordOk = $(this).find(".label_ok").html();
                    var keywordKo = $(this).find(".label_ko").html();


                    $.getJSON('https://fr.wikipedia.org/w/api.php?action=query&list=search&srsearch=%22' + encodeURIComponent(keywordKo) + '%22&srnamespace=0&format=json&srlimit=50&callback=?', function (data) {
                        var count = 0;
                        for (var id in data["query"]["search"]) {
                            count++;
                            var title = data["query"]["search"][id]["title"];
                            var snippet = data["query"]["search"][id]["snippet"];
                            var size = data["query"]["search"][id]["size"];

                            // Does snippet contains the string "searchmatch"
                            var regex = /searchmatch/g;
                            var match = regex.exec(snippet);
                            if (match != null) {
                                var resultLine = "<tr><td><a href=\"http://fr.wikipedia.org/wiki/" + encodeURIComponent(title) + "\">" + title + "</a><br/><small> " + size + " caractères</small></td><td>" + keywordKo + "</td><td>" + keywordOk + "</td><td>" + snippet + "</td><td><a class='btnSuppr' href='#'>&#10006;</a></td></tr>";
                                $("#articlesList").append(resultLine);
                                $(".btnSuppr").on("click", function () {
                                        $(this).parent().parent().remove();
                                        updateNbExports();
                                        return false;
                                    }
                                );
                            }
                        }
                        updateNbExports();
                        $("#loader").hide();
                    });
                }
            );
        });

        function updateNbExports() {
            $("#nbExports").html(" (" + $('#articlesList tr').length + ")")
        }
    </script>

    <!-- CSS
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/skeleton.css">

    <style>
        .button {
            margin-right: 10px;
        }

        .hidden {
            display: none;
        }

        .searchmatch {
            font-weight: bold;
            font-style: italic;
        }

        .btnSuppr {
            color: red;
            font-size: 2em;
        }

        #loader {
            position: relative;
            width: 64px;
            height: 64px;
            display: none;
        }

        #loader div {
            position: absolute;
            top: 27px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #245;
            animation-timing-function: cubic-bezier(0, 1, 1, 0);
        }

        #loader div:nth-child(1) {
            left: 6px;
            animation: lds-ellipsis1 0.6s infinite;
        }

        #loader div:nth-child(2) {
            left: 6px;
            animation: lds-ellipsis2 0.6s infinite;
        }

        #loader div:nth-child(3) {
            left: 26px;
            animation: lds-ellipsis2 0.6s infinite;
        }

        #loader div:nth-child(4) {
            left: 45px;
            animation: lds-ellipsis3 0.6s infinite;
        }

        @keyframes lds-ellipsis1 {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes lds-ellipsis3 {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(0);
            }
        }

        @keyframes lds-ellipsis2 {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(19px, 0);
            }
        }
    </style>

    <!-- Favicon
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body>

<?php
include "utils.php";
$keywords = loadKeywords();
?>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
    <div class="row">
        <div class="">
            <h1>Génération de liste pour atelier</h1>
            <table class="u-full-width">
                <thead>
                <tr>
                    <th>Article</th>
                    <th>Erreur</th>
                    <th>Correction</th>
                    <th>Extrait</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody id='articlesList'>
                </tbody>
            </table>

            <p></p>
            <div id="loader">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <br/>
            <?php
            foreach ($keywords as $indice => $values) {
                print "<span class='button addKeyword' data-key='" . $indice . "'><span class='hidden label_ko'>" . $values["ko"] . "</span><span class='hidden label_ok'>" . $values["ok"] . "</span>" . $values["ko"] . " &rarr; " . $values["ok"] . "</span>";
            }
            ?>

            <div id="export-button" class="u-full-width button button-primary">Exporter<span id='nbExports'></span>
            </div>
        </div>
    </div>
</div>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
