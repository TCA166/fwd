<html>
    <head>
        <title>FWD-Dynatest</title>
        <meta charset="UTF-8">
        <script src="static/compare.js"></script>
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <!-- Js library for map handling https://switch2osm.org/using-tiles/getting-started-with-leaflet/ -->
        <!--tree view stuff -->
        <script src="static/jquery-3.6.3.min.js"></script>
        <script src="static/bootstrap-treeview.js"></script>
        <link rel="stylesheet" href="static/bootstrap-treeview.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
        <!-- Bootsrap stuff -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    </head>
    <body style="width:100%; height: 100%;" class="bg-dark">
        <nav class="navbar navbar-expand-lg bg-secondary">
            <div class="container-fluid">
                <a href="http://sump-osad.pl/osad_FWD-Dynatest/" class="navbar-brand">FWD-Dynatest</a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <span class="nav-link disabled">Porównywanie</span>
                    </li>
                    <li class="nav-item">
                        <a href="http://sump-osad.pl/osad_FWD-Dynatest/manage.php" class="nav-link">Zarządzaj</a>
                    </li>
                    <li class="nav-item">
                        <a href="http://sump-osad.pl/" class="nav-link">Powrót</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="container-fluid text-white" style="width:100%; height: 80%;" >
            <div class="row" style="width:100%; height: 100%;">
                <div class="col-2" style="height: 100%;">
                    <div style="width:100%; min-height:40%">
                        <label for="sekcjaSelect" class="fw-bold">Wybierz pas którego sekcje chcesz porównać:</label>
                        <div id="tree"></div>
                        <div class="hidden">
                            <label for="topSelect" class="fw-bold">Wybierz sekcje pomiarową pasa:</label>
                            <select id="topSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') selectChanged(this);">
                                <option value="nul" selected="selected"></option>
                            </select>
                            <label for="bottomSelect" class="fw-bold">Wybierz sekcje pomiarową do porównywania:</label>
                            <select id="bottomSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') selectChanged(this);">
                                <option value="nul" selected="selected"></option>
                            </select>
                        </div>
                    </div>
                    <div class="card text-dark hidden">                        
                    </div>
                </div>
                <div class="col-5 hidden" style="height:100%;">
                    <div class="card text-dark" style="height:43%;margin-bottom:2%;">
                        <div class="card-header"></div>
                        <div class="card-body">

                        </div>
                    </div>
                    <div class="card text-dark" style="width:100%; max-height:23%; margin-bottom:2%">
                        <div class="card-header"></div>
                        <div class="card-body text-dark">

                        </div>
                    </div>
                    <div class="card text-dark" style="width:100%; max-height:30%;">
                        <div class="card-header"></div>
                        <div class="card-body text-dark">

                        </div>
                    </div>
                </div>
                <div class="col-5 hidden" style="height: 100%;" id="mapContainer">
                    <div id="map" style="width:100%; height: 100%;">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=16.835002899169925%2C52.338171059294034%2C17.06159591674805%2C52.43246689687904&amp;layer=mapnik" style="border: 1px solid black; display: block;"></iframe>
                    </div>
                </div>
            </div>
        </main>
        <footer>

        </footer>
    </body>
    <script src="static/functions.js"></script>
    <script>
        treeData = JSON.parse('<?php include 'createTree.php'; echo createTree(1)?>');

        $('#tree').treeview({
            data: treeData, 
            onNodeSelected: function(event, data) {
                switch(data["type"]){
                    case 0:
                        resetPage();
                        break;
                    case 1:
                        resetPage();
                        treeChanged(data);
                        break;
                }
            },
            collapseIcon:"bi bi-chevron-double-down",
            expandIcon:"bi bi-chevron-double-right",
            levels:2,
            selectedIcon:""
        });
    </script>
</html>