<html>
    <head>
        <title>FWD-Dynatest</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <!-- Js library for map handling https://switch2osm.org/using-tiles/getting-started-with-leaflet/ -->
        <!-- Bootsrap stuff -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>    </head>
    <body style="width:100%; height: 100%;" class="bg-dark">
        <nav class="navbar navbar-expand-lg bg-secondary">
            <div class="container-fluid">
                <span class="navbar-brand">FWD-Dynatest</span>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="http://sump-osad.pl/osad_FWD-Dynatest/compare.php" class="nav-link">Porównywanie</a>
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
        <main class="container-fluid text-white" style="width:100%; height: 90%;" >
            <div class="row" style="width:100%; height: 100%;">
                <div class="col-2" style="height: 100%;">
                    <div style="width:100%; min-height:20%">
                        <div>
                            <label for="sessionSelect" class="fw-bold">Wybierz kampanię pomiarową:</label>
                            <select id="sessionSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') sessionSelected(this, [document.getElementById('sekcjaSelect')])">
                                <option value="nul" selected="selected"></option>
                                <?php 
                                    include 'functions.php';
                                    getSectionsAsOptions();
                                ?>
                            </select>
                        </div>
                        <div style="display:none">
                            <label for="sekcjaSelect" class="fw-bold">Wybierz sekcje pomiarową z wybranej kampanii:</label>
                            <select id="sekcjaSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') selectChanged(this);">
                                <option value="nul" selected="selected"></option>
                            </select>
                        </div>
                        <div class="hidden">
                            <label>Liczba zrzutów brana pod uwagę: <i data-bs-toggle="tooltip" data-bs-title="Zmień ilość serii zrzutów branych pod uwagę przy wizualizacji danych aby wykluczyć błędy statystyczne." class="bi bi-patch-question-fill"></i></label>
                            <div class="input-group mb-3" style="margin:auto; width:80%">
                                <button class="btn nohover btn-outline-secondary" id="allBtn" type="button" data-bs-toggle="button" onclick="mode(0)">Wszystkie</button>
                                <button class="btn nohover btn-outline-secondary active" id="70Btn" type="button" data-bs-toggle="button" aria-pressed="true" onclick="mode(1)">70%</button>
                                <button class="btn nohover btn-outline-secondary" id="1Btn" type="button" data-bs-toggle="button" onclick="mode(2)">Ostatni</button>
                            </div>
                        </div>
                    </div>
                    <div class="card text-dark hidden" style="height:70%">
                        <nav>
                            <div class="nav nav-tabs card-header" id="nav-tab" role="tablist" style="padding-bottom: 0;">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Kryteria</button>
                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Kilometraż <i data-bs-toggle="tooltip" data-bs-title="Kliknięcie na element listy stacji zrzutów powoduje wyświetlenie danych tej stacji." class="bi bi-patch-question-fill"></i></button>
                            </div>
                        </nav>
                        <div class="card-body" style="overflow-y:auto;">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    <table class="tableBordered text-center" style="width:100%;">
                                        <tr>
                                            <td>Legenda Wykresu:</td>
                                        </tr>
                                        <tr><td style="background-color:#00ff00;">Nie wymaga remontu</td></tr>
                                        <tr><td style="background-color:#006600;">Faza początkowa degradacji</td></tr>
                                        <tr><td style="background-color:#ff9900;">Stan ostrzegawczy</td></tr>
                                        <tr><td style="background-color:#cc3300;">Stan zły</td></tr>
                                        <tr><td style="background-color:#ff0000;">Konieczny remont / Przebudowa</td></tr>
                                    </table>
                                    <hr class="hr" style="margin: 0.5rem 0;" />
                                    <table class="tableBordered text-center" style="width:100%;">
                                        <tr>
                                            <td>SCI:</td>
                                        </tr>
                                        <tr><td style="background-color:#00ff00;">SCI < 120</td></tr>
                                        <tr><td style="background-color:#006600;">121 < SCI < 160</td></tr>
                                        <tr><td style="background-color:#ff9900;">161 < SCI < 200</td></tr>
                                        <tr><td style="background-color:#cc3300;">201 < SCI < 240</td></tr>
                                        <tr><td style="background-color:#ff0000;">241 < SCI</td></tr>
                                        <tr>
                                            <td>BDI:</td>
                                        </tr>
                                        <tr><td style="background-color:#00ff00;">BDI < 90</td></tr>
                                        <tr><td style="background-color:#006600;">91 < BDI < 120</td></tr>
                                        <tr><td style="background-color:#ff9900;">121 < BDI < 150</td></tr>
                                        <tr><td style="background-color:#cc3300;">151 < BDI < 180</td></tr>
                                        <tr><td style="background-color:#ff0000;">181 < BDI</td></tr>
                                        <tr>
                                            <td>BCI:</td>
                                        </tr>
                                        <tr><td style="background-color:#00ff00;">BCI < 45</td></tr>
                                        <tr><td style="background-color:#006600;">46 < BCI < 60</td></tr>
                                        <tr><td style="background-color:#ff9900;">61 < BCI < 75</td></tr>
                                        <tr><td style="background-color:#cc3300;">76 < BCI < 90</td></tr>
                                        <tr><td style="background-color:#ff0000;">91 < BCI</td></tr>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                    <ol id="pointList" style="margin-left: 10px;">
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-5 hidden" style="height:100%;">
                    <div class="card text-dark" style="height:43%;margin-bottom:2%;">
                        <div class="card-header">Wykresy stanu technicznego stref przekroju poprzecznego nawierzchni:</div>
                        <div class="card-body">
                            Strefa 1: Warstwy bitumiczne
                            <div class="chartContainer" id="SCI" style="height:9%">
                                
                            </div>
                            Strefa 2: Warstwy podbudowy
                            <div class="chartContainer" id="BDI" style="height:17%">
                                
                            </div>
                            Strefa 3: Podłoże nawierzchni
                            <div class="chartContainer" id="BCI" style="height:25%">
                                
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-outline-secondary" onclick="move(-1)"><i class="bi bi-arrow-left"></i></button><button class="btn btn-outline-secondary" onclick="move(1)"><i class="bi bi-arrow-right"></i></button>
                            Zaznaczenie <span id="selInfo"></span> = <span id="distInfo" style="color:blue">0</span> km <i data-bs-toggle="tooltip" data-bs-title="Aby zaznaczyć kliknij i przytrzymaj na obszarze wykresu aby rozpocząć zaznaczanie, a następnie puść w miejscu na wykresie gdzie chcesz aby zaznaczenie się skończyło." class="bi bi-patch-question-fill"></i>
                        </div>
                    </div>
                    <div class="card text-dark" style="width:100%; max-height:23%; margin-bottom:2%">
                        <div class="card-header">Dane miejsca pomiaru:</div>
                        <div class="card-body text-dark">
                            <table style="width:100%;display:none" class="tableBordered">
                                <thead id="infoTableHeader">
                                    <tr>
                                        <th>Koordynaty (WGS84)</th>
                                        <th>Kilometraż</th>
                                        <th>T MMA</th>
                                        <th>T Powierzchni</th>
                                        <th>T Powietrza</th>
                                        <th>SCI</th>
                                        <th>BDI</th>
                                        <th>BCI</th>
                                    </tr>
                                    <tr>
                                        <td id="coords"></td>
                                        <td id="station"></td>
                                        <td id="asphalt"></td>
                                        <td id="surface"></td>
                                        <td id="air"></td>
                                        <td id="sciDisplay"></td>
                                        <td id="bdiDisplay"></td>
                                        <td id="bciDisplay"></td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card text-dark" style="width:100%; max-height:30%;">
                        <div class="card-header">Wyniki pomiaru po normalizacji (T = 20°C, <span id="surface2"></span>F = 50kN):</div>
                        <div class="card-body text-dark">
                            <table style="width:100%;display:none" class="tableBordered">
                                <thead>
                                    <tr id="miniHead">
                                        <th>Naprężenia [kPa]</th>
                                    </tr>
                                </thead>
                                <tbody id="infoTable">

                                </tbody>
                            </table>
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
    <script src="static/script.js"></script>
</html>