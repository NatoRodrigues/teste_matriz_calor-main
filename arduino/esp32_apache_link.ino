#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_AMG88xx.h>

const char* ssid = "jrservice.net(sandra)9806-4567";  // Nome da rede Wi-Fi
const char* password = "15022000";
const char* endpoint = "http://10.0.0.103/teste_matriz_calor-main/backend/backend.php"; // Caminho para o script PHP no servidor

#define GRID_ROWS 8
#define GRID_COLS 8

Adafruit_AMG88xx amg;

float grid[GRID_ROWS][GRID_COLS];

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Conectando ao WiFi...");
  }
  Serial.println("Conectado ao WiFi.");

  // Inicializar o sensor AMG8833
  bool sensorReady = amg.begin();
  if (!sensorReady) {
    Serial.println("Falha ao iniciar o sensor AMG8833. Verifique as conexões.");
    while (1);
  }
}

void loop() {
  // Ler dados do sensor AMG8833 e preencher a matriz grid[][] com os valores
  float pixels[AMG88xx_PIXEL_ARRAY_SIZE];
  amg.readPixels(pixels);

  // Construir uma string com os dados separados por vírgula
  String dataString = "";
  for (int i = 0; i < GRID_ROWS; i++) {
    for (int j = 0; j < GRID_COLS; j++) {
      grid[i][j] = pixels[j + i * GRID_COLS]; // Atualizar os valores da matriz
      Serial.print(grid[i][j]); // Imprimir o valor
      Serial.print("\t"); // Adicionar uma tabulação para melhorar a formatação
      dataString += String(grid[i][j]);
      if (j < GRID_COLS - 1) {
        dataString += ",";
      }
    }
    Serial.println(); // Nova linha para a próxima linha da matriz
    if (i < GRID_ROWS - 1) {
      dataString += "|";
    }
  }
  Serial.println(); // Nova linha para separar os dados da matriz na saída serial

  // Enviar dados via GET
  String url = String(endpoint) + "?data=" + dataString;
  HTTPClient http;
  http.begin(url);
  int httpResponseCode = http.GET();
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println(httpResponseCode);
    Serial.println(response);
  } else {
    Serial.println("Erro na solicitação HTTP.");
  }
  http.end();

  // Aguardar um intervalo de tempo antes de enviar os dados novamente
  delay(10000); // Exemplo de intervalo de envio: 5 segundos
}
