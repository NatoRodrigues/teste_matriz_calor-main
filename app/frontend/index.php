<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Calor Infravermelho</title>
    <!-- Inclua a biblioteca Plotly.js -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <!-- Inclua a biblioteca Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Inclua o Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <!-- Adicione o arquivo JavaScript -->
    <script src="./JS/fade-in.js"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <!-- Adicione o favicon -->
    <link rel="icon" href="assets/images/favicon/noun-infrared-852813.png" type="image/x-icon">
    <style>
        .chart-container {
            width: 100%;
            max-width: 600px; /* Defina o tamanho máximo para o gráfico */
            height: 400px; /* Ajuste a altura conforme necessário */
            margin: 0 auto; /* Centralize o contêiner */
        }
    </style>
    <style>
        /* Estilos CSS para o banner */
        .banner {
    display: none; /* Inicialmente, o banner está oculto */
}

.banner.fade-in {
    display: block; /* Quando a classe fade-in é adicionada, o banner é exibido */
    animation: fadeIn 0.5s ease-in-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
    </style>
</head>

<body style="background-color: #f3f4f6; font-family: Arial, sans-serif;">
    <!-- Navbar -->
    
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="assets/images/infrared.png" alt="Logo" class="h-8 mr-2">
                <a href="#" class="text-white font-bold text-xl font-sans">Detector infravermelho</a>
            </div>
            <button id="menuButton" class="text-white md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
            <ul id="menu" class="hidden md:flex space-x-4">
                <li><a href="index.php" class="text-white font-sans">Home</a></li>
                <li><a href="sobre.html" class="text-white font-sans">Sobre</a></li>
                <li><a href="contato.html" class="text-white font-sans">Contato</a></li>
            </ul>
        </div>
    </nav>

    <!-- Container para os gráficos -->
    <div class="container mx-auto mt-8">
        <!-- Banner -->
        <div id="banner" class="hidden bg-yellow-500 p-4 shadow-md rounded-md mb-8">
            <!-- Conteúdo do banner -->
            <h2 class="text-lg font-bold mb-2">🚨 Monitoramento em tempo real 🚨</h2>
            <p class="text-gray-700">Aproxime alguma parte do seu corpo ao sensor e verique o mapa abaixo.</p>
        </div>

        <!-- Gráfico de Mapa de Calor -->
        <div id="heatmapChart" class="chart-container"></div>
        <!-- Gráfico de Variação de Temperatura -->
        <div id="temperatureChart" class="chart-container"></div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 Mapa de Calor Infravermelho. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Script para os gráficos -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var button = document.querySelector('button');
        var menu = document.querySelector('ul');

        button.addEventListener('click', function () {
            menu.classList.toggle('hidden');
        });
    });

   // Função para extrair apenas os pixels dos dados recebidos
function extractPixels(data) {
    var pixels = [];
    for (var key in data) {
        if (key !== 'thermistor_temp') {
            pixels.push(data[key]);
        }
    }
    return pixels;
}

function updateHeatmap(data) {
    // Converter os dados recebidos em uma matriz bidimensional de 8x8
    var matrixData = [];
    var rowCount = 8; // Número de linhas na matriz
    var colCount = data.length / rowCount; // Número de colunas na matriz

    // Iterar sobre os dados para criar a matriz bidimensional
    for (var i = 0; i < rowCount; i++) {
        var row = [];
        for (var j = 0; j < colCount; j++) {
            // Calcular o índice no array unidimensional
            var index = i * colCount + j;
            // Adicionar o valor correspondente à matriz
            row.push(data[index]);
        }
        // Adicionar a linha à matriz bidimensional
        matrixData.push(row);
    }

    // Log dos pixels recebidos no console
    console.log('Pixels recebidos:', data);

    // Definir o layout e plotar o heatmap
    var layout = {
    title: 'Mapa de Calor Infravermelho',
    xaxis: {
        title: 'Linhas',
        side: 'bottom',
        tickfont: {
            size: 10 // Aumenta o tamanho da fonte dos rótulos do eixo x
        }
    },
    yaxis: {
        title: 'Colunas',
        autorange: 'reversed',
        tickfont: {
            size: 10 // Aumenta o tamanho da fonte dos rótulos do eixo y
        }
    },
    margin: {
        t: 80, // Aumenta a margem superior
        l: 80, // Aumenta a margem esquerda
        r: 80, // Aumenta a margem direita
        b: 100 // Aumenta a margem inferior
    },
    font: {
        family: 'Arial, sans-serif',
        size: 12, // Ajusta o tamanho da fonte geral
        color: '#333'
    }
};

    Plotly.newPlot('heatmapChart', [{
        z: matrixData,
        type: 'heatmap',
        colorscale: 'Jet',
        zsmooth: 'best',
        zhoverformat: '.2f',
        colorbar: {
            title: 'Temperatura (°C)',
            titleside: 'right',
            ticksuffix: '°C'
        }
    }], layout);
}
var timeData = [];
var temperatureData = [];

// Função para atualizar o gráfico de variação de temperatura com os dados recebidos
function updateTemperatureChart(thermistorTemp, timeData, temperatureData) {
    // Adicionar o novo valor de temperatura e o tempo correspondente
    var currentTime = new Date();
    timeData.push(currentTime);
    temperatureData.push(thermistorTemp);

    // Manter apenas os últimos 10 minutos de dados
    var maxTime = new Date(currentTime - 10 * 60000);
    while (timeData.length > 0 && timeData[0] < maxTime) {
        timeData.shift();
        temperatureData.shift();
    }

    // Dados para o gráfico de variação de temperatura
    var plotData = [{
        x: timeData,
        y: temperatureData,
        type: 'scatter',
        mode: 'lines',
        name: 'Temperatura do Termistor'
    }];

    // Definir o layout do gráfico de variação de temperatura
    var layout = {
        title: 'Variação de Temperatura do Termistor',
        xaxis: { title: 'Tempo' },
        yaxis: { title: 'Temperatura (°C)' },
        margin: { t: 60, l: 60, r: 60, b: 80 },
        font: { family: 'Arial, sans-serif', size: 12, color: '#333' }
    };

    // Plotar o gráfico de variação de temperatura
    Plotly.newPlot('temperatureChart', plotData, layout);
}

// Função para fazer a requisição AJAX ao servidor PHP usando Axios
function requestDataFromServer() {
    // Fazer a requisição GET para o backend
    axios.get('http://localhost/teste_matriz_calor-main/app/backend/backend.php')
        .then(response => {
            if (response.data.error) {
                console.error('Erro:', response.data.error);
            } else {
                console.log('Dados recebidos:', response.data); // Verifique os dados recebidos no console

                // Extrair apenas os pixels dos dados recebidos
                var heatmapData = extractPixels(response.data);

                // Atualizar o heatmap com os pixels extraídos
                updateHeatmap(heatmapData);

                // Obter o valor da temperatura do termistor
                var thermistor_temp = response.data.thermistor_temp;

                // Atualizar o gráfico de variação de temperatura
                updateTemperatureChart(thermistor_temp, timeData, temperatureData);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

    // Iniciar a requisição de dados quando a página carregar
    setInterval(requestDataFromServer, 500); // 5000 milissegundos = 5 segundos

    // Atualizar os gráficos periodicamente
</script>
</body>

</html>