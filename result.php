<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Pesquisa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script>
        window.onload = function() {
            if (performance.navigation.type === 1) {
                window.location.href = 'index.php';
            }
        };
       
        
        document.addEventListener('DOMContentLoaded', () => {
          const form = document.getElementById('animarForm');
          const elements = document.querySelectorAll('.container-all');
          let isSubmitting = false;

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;
        elements.forEach((element) => {
            element.classList.add('fade-out');
        });
        form.classList.add('fade-out');
        setTimeout(() => {
            form.submit();
            setTimeout(() => {
                elements.forEach((element) => {
                    element.classList.remove('fade-out');
                    element.classList.add('fade-in');
                });
                form.classList.remove('fade-out');
                form.classList.add('fade-in');
                
            }, 1000); 

        }, 2000); 
    });
});

    </script>

</head>
<?php

session_start();

if (empty($_POST['InserirCidade'])) {
    header("Location: index.php");
    exit();
}

$_SESSION['InserirCidade'] = htmlspecialchars(trim($_POST['InserirCidade']));
$commandoCidade = $_SESSION['InserirCidade'];
$cidade = implode("-", explode(" ", $commandoCidade));

$url = "http://api.openweathermap.org/geo/1.0/direct?q={$cidade}&limit=1&appid=e6d16822ab5de7387bac591781ccc79b";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);
curl_close($curl);

$dadosCidade = json_decode($response, true);

if (empty($dadosCidade) || empty($dadosCidade[0])) {
    $nomeCidade = 'Cidade não encontrada';
    $qualidadeAr = 'Não disponível';
    $recomendacao = 'Nenhuma informação disponível.';
    $carbono = 'Não disponível';
    $oxidoNitrico = 'Não disponível';
    $dioxidoNitroenio = 'Não disponível';
    $ozonio = 'Não disponível';
    $enxofre = 'Não disponível';
    $particulaFina = 'Não disponível';
    $particulaGrossa = 'Não disponível';
    $amonia = 'Não disponível';
} else {
    $localCidade = $dadosCidade[0];
    $nomeCidade = $localCidade['name'];
    $latitude = $localCidade['lat'];
    $longitude = $localCidade['lon'];

    $url = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$latitude}&lon={$longitude}&appid=e6d16822ab5de7387bac591781ccc79b";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $dadosPoluição = json_decode($response, true);

    $color = '';
    if (empty($dadosPoluição) || empty($dadosPoluição['list'][0])) {
        $qualidadeAr = 'Não disponível';
        $recomendacao = 'Nenhuma informação disponível.';
    } else {
        $qualidadeAqi = $dadosPoluição['list'][0]['main']['aqi'] ?? 'Não disponível';

        switch ($qualidadeAqi) {
            case 1:
                $qualidadeAr = 'Boa';
                $color = '9fff00';
                break;
            case 2:
                $qualidadeAr = 'Adequada';
                $color = '96e11e';
                break;
            case 3:
                $qualidadeAr = 'Moderada';
                $color = '8dc33c';
                break;
            case 4:
                $qualidadeAr = 'Ruim';
                $color = '84a55a';
                break;
            case 5:
                $qualidadeAr = 'Muito ruim';
                $color = '7b8778';
                break;
            default:
                $qualidadeAr = 'Não disponível';
                break;
        }

        $count = 0;

        switch ($qualidadeAr) {
            case 'Boa':
                $recomendacao = 'A qualidade do ar está boa, aproveite para fazer atividades ao ar livre.';
                break;
            case 'Adequada':
                $recomendacao = 'A qualidade do ar é adequada, pessoas sensíveis devem monitorar possíveis sintomas.';
                break;
            case 'Moderada':
                $recomendacao = 'A qualidade do ar é moderada, pessoas com condições respiratórias devem evitar esforços intensos.';
                break;
            case 'Ruim':
                $recomendacao = 'A qualidade do ar está ruim, é aconselhável limitar atividades físicas ao ar livre.';
                break;
            case 'Muito ruim':
                $recomendacao = 'A qualidade do ar é muito ruim, fique em ambientes fechados e evite sair.';
                break;
            default:
                $recomendacao = 'Nenhuma informação disponível.';
                break;
        }

        $componentesAr = $dadosPoluição['list'][0]['components'];
        $carbono = $componentesAr['co'];
        $oxidoNitrico = $componentesAr['no'];
        $dioxidoNitroenio = $componentesAr['no2'];
        $ozonio = $componentesAr['o3'];
        $enxofre = $componentesAr['so2'];
        $particulaFina = $componentesAr['pm2_5'];
        $particulaGrossa = $componentesAr['pm10'];
        $amonia = $componentesAr['nh3'];
    }
}
?>
<style>
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

.fade-out {
    animation: fadeOut 2s forwards;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fade-in {
    animation: fadeIn 1s forwards; 
}
</style>
<body>
    <main id="containerMain" class="container-all">
        <div class="container-info">
            <header class="search-bar">
                <form id="animarForm" method="post" action="result.php">
                    <input
                    id="animar"
                    type="text"
                    name="InserirCidade"
                    placeholder="Pesquisar cidade..."
                    required>
                </form>
            </header>
            <section class="container">
                <section class="grid">
                    <div class="content">
                        <post>
                            <nav class="titulo-qualidade">Qualidade do Ar em <?php echo $nomeCidade; ?></nav>
                        </post>
                        <post>
                            <nav class="qualidade" style="background-color: #<?= $color ?>"><?php echo $qualidadeAr; ?></nav>
                            <nav class="titulo-recomendacao">Recomendação:</nav>
                            <nav class="recomendacao">
                                <label><?php echo $recomendacao; ?></label>
                            </nav>
                        </post> 
                    </div>
                    <footer>
                        <post>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#7b8778" class="bi bi-1-square-fill" viewBox="0 0 16 16">
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm7.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z"/>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#84a55a" class="bi bi-2-square-fill" viewBox="0 0 16 16">
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm4.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306"/>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#8dc33c" class="bi bi-3-square-fill" viewBox="0 0 16 16">
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318"/>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#96e11e" class="bi bi-4-square-fill" viewBox="0 0 16 16">
                            <path d="M6.225 9.281v.053H8.85V5.063h-.065c-.867 1.33-1.787 2.806-2.56 4.218"/>
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.519 5.057q.33-.527.657-1.055h1.933v5.332h1.008v1.107H10.11V12H8.85v-1.559H4.978V9.322c.77-1.427 1.656-2.847 2.542-4.265Z"/>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#9fff00" class="bi bi-5-square-fill" viewBox="0 0 16 16">
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5.994 12.158c-1.57 0-2.654-.902-2.719-2.115h1.237c.14.72.832 1.031 1.529 1.031.791 0 1.57-.597 1.57-1.681 0-.967-.732-1.57-1.582-1.57-.767 0-1.242.45-1.435.808H5.445L5.791 4h4.705v1.103H6.875l-.193 2.343h.064c.17-.258.715-.68 1.611-.68 1.383 0 2.561.944 2.561 2.585 0 1.687-1.184 2.806-2.924 2.806Z"/>
                        </svg>
                    </post>
                    </footer>
                </section>
                <section class="list-components">
                    <table class="table-components">
                        <thead class="thead-table-components">
                            <tr>
                                <th>Componente</th>
                                <th>Valor (µg/m³)</th>
                            </tr>
                        </thead>
                        <tbody class="tbody-table-components">
                            <tr>
                                <td>Monóxido de Carbono (CO)</td>
                                <td><?php echo $carbono; ?></td>
                            </tr>
                            <tr>
                                <td>Óxido Nítrico (NO)</td>
                                <td><?php echo $oxidoNitrico; ?></td>
                            </tr>
                            <tr>
                                <td>Dióxido de Nitrogênio (NO₂)</td>
                                <td><?php echo $dioxidoNitroenio; ?></td>
                            </tr>
                            <tr>
                                <td>Ozônio (O₃)</td>
                                <td><?php echo $ozonio; ?></td>
                            </tr>
                            <tr>
                                <td>Dióxido de Enxofre (SO₂)</td>
                                <td><?php echo $enxofre; ?></td>
                            </tr>
                            <tr>
                                <td>Material Particulado Fino (PM2.5)</td>
                                <td><?php echo $particulaFina; ?></td>
                            </tr>
                            <tr>
                                <td>Material Particulado Grosso (PM10)</td>
                                <td><?php echo $particulaGrossa; ?></td>
                            </tr>
                            <tr>
                                <td>Amônia (NH₃)</td>
                                <td><?php echo $amonia; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </section>
        </div>
        <div class="mapa-container"></div>
    </main>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        <?php if ($latitude != null && $longitude != null) : ?>
                const latitude = <?php echo json_encode($latitude); ?>;
                const longitude = <?php echo json_encode($longitude); ?>;
                const zoom = 13;
                const mapa = L.map(document.querySelector('.mapa-container'), {
                    center: [latitude, longitude],
                    zoom: zoom,
                    dragging: false,
                    scrollWheelZoom: false,
                    doubleClickZoom: false,
                    boxZoom: false,
                    keyboard: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(mapa);

                L.marker([latitude, longitude]).addTo(mapa)
                    .bindPopup("Localização selecionada.")
                    .openPopup();
            <?php else: ?>
                document.getElementById('mapa-container').style.display = 'none';
            <?php endif; ?>
    </script>
</body>

</html>