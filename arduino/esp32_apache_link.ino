#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_AMG88xx.h>

const char* ssid = "wifi_name";  // Nome da rede Wi-Fi
const char* password = "wifi_password";
const char* endpoint = "server_endpoint"; // Caminho para o servidor

#define GRID_ROWS 8
#define GRID_COLS 8
#define TEMPERATURE_THRESHOLD 30 // Limiar de temperatura em °C
#define PIXEL_THRESHOLD 8 // Limiar de pixels acima do limiar de temperatura

Adafruit_AMG88xx amg;

float grid[GRID_ROWS][GRID_COLS];
unsigned long lastBuzzerTime = 0; // Variável para armazenar o momento em que o buzzer foi desativado pela última vez
const unsigned long buzzerInterval = 1000; // Intervalo desejado entre acionamentos do buzzer (em milissegundos)

#define BUZZER_FREQUENCY 200 // Frequência do buzzer em Hz
#define BUZZER_PIN 5 // Pino do buzzer
#define LED_R_PIN 27  // Pino do LED RGB - Vermelho
#define LED_G_PIN 16  // Pino do LED RGB - Verde
#define LED_B_PIN 14  // Pino do LED RGB - Azul

String authKey = ""; // Sua chave de autenticação gerada dinamicamente

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  pinMode(LED_R_PIN, OUTPUT);
  pinMode(LED_G_PIN, OUTPUT);
  pinMode(LED_B_PIN, OUTPUT);

  // Inicializar o LEDC para controlar o PWM dos LEDs
  ledcSetup(0, 5000, 8); // Canal 0, frequência 5 kHz, resolução de 8 bits
  ledcAttachPin(LED_R_PIN, 0); // Anexar o canal 0 ao pino do LED vermelho
  ledcAttachPin(LED_G_PIN, 1); // Anexar o canal 1 ao pino do LED verde
  ledcAttachPin(LED_B_PIN, 2); // Anexar o canal 2 ao pino do LED azul

  // Aqui você pode definir a cor inicial do LED RGB como branco
  analogWrite(LED_R_PIN, 255);
  analogWrite(LED_G_PIN, 255);
  analogWrite(LED_B_PIN, 255);

  while (WiFi.status() != WL_CONNECTED) {
    // Aqui você pode fazer o LED RGB piscar em azul durante o processo de conexão WiFi
    analogWrite(LED_R_PIN, 0);
    analogWrite(LED_G_PIN, 0);
    analogWrite(LED_B_PIN, 255);
    delay(100);
    analogWrite(LED_R_PIN, 255);
    analogWrite(LED_G_PIN, 255);
    analogWrite(LED_B_PIN, 255);
    delay(100);
    Serial.println("Conectando ao WiFi...");
  }
  Serial.println("Conectado ao WiFi.");

  // Gerar a chave de autenticação
  authKey = generateAuthKey(16); // Gera uma chave de 16 caracteres

  // Inicializar o sensor AMG8833
  bool sensorReady = amg.begin();
  if (!sensorReady) {
    Serial.println("Falha ao iniciar o sensor AMG8833. Verifique as conexões.");
    while (1);
  }

  pinMode(BUZZER_PIN, OUTPUT); // Configurar o pino do buzzer como saída
}
bool buzzerActive = false; // Variável para indicar se o buzzer está ativado

String generateAuthKey(int length) {
  String chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  String key = "";
  for (int i = 0; i < length; i++) {
    int index = random(0, chars.length());
    key += chars.charAt(index);
  }
  return key;
}

void loop() {
  // Ler dados do sensor AMG8833 e preencher a matriz grid[][] com os valores
  float pixels[AMG88xx_PIXEL_ARRAY_SIZE];
  amg.readPixels(pixels);

  // Construir uma string com os dados separados por vírgula
  String pixelsString = "";
  int highTemperaturePixels = 0; // Contador de pixels com temperatura acima do limiar
  for (int i = 0; i < GRID_ROWS; i++) {
    for (int j = 0; j < GRID_COLS; j++) {
      grid[i][j] = pixels[j + i * GRID_COLS]; // Atualizar os valores da matriz
      pixelsString += String(grid[i][j]);
      if (j < GRID_COLS - 1) {
        pixelsString += ",";
      }

      // Verificar se o pixel está acima do limiar de temperatura
      if (grid[i][j] > TEMPERATURE_THRESHOLD) {
        highTemperaturePixels++;
      }
    }
    if (i < GRID_ROWS - 1) {
      pixelsString += "|";
    }
  }

  // Ler a temperatura do termistor interno do AMG8833
  float thermistorTemp = amg.readThermistor();

  // Obter o tempo atual
  unsigned long currentTime = millis();

  // Ativar o buzzer se pelo menos 8 pixels estiverem acima de 30 graus e já passou o intervalo desejado
  if (highTemperaturePixels >= PIXEL_THRESHOLD && currentTime - lastBuzzerTime >= buzzerInterval && !buzzerActive) {
    Serial.println("Ativando o buzzer");
    tone(BUZZER_PIN, BUZZER_FREQUENCY);
    digitalWrite(BUZZER_PIN, HIGH); // Ativar o buzzer
    buzzerActive = true; // Indicar que o buzzer está ativado
    lastBuzzerTime = currentTime; // Atualizar o tempo do último acionamento do buzzer
    analogWrite(LED_R_PIN, 255);  // Vermelho
    analogWrite(LED_G_PIN, 0);    // Verde
    analogWrite(LED_B_PIN, 0);    // Azul
  } else if (buzzerActive && currentTime - lastBuzzerTime >= buzzerInterval) {
    Serial.println("Desativando o buzzer");
    noTone(BUZZER_PIN); // Desativar o buzzer
    digitalWrite(BUZZER_PIN, LOW); // Desativar o buzzer
    buzzerActive = false; // Indicar que o buzzer está desativado
  }

  // Se não houver pixels suficientes acima de 30 graus, definir a cor do LED como verde
  if (!buzzerActive) {
    analogWrite(LED_R_PIN, 0);    // Vermelho
    analogWrite(LED_G_PIN, 255);  // Verde
    analogWrite(LED_B_PIN, 0);    // Azul
  }

  // Enviar dados via POST
  String postData = "pixels=" + pixelsString + "&thermistor_temp=" + String(thermistorTemp);
  HTTPClient http;
  http.begin(endpoint);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.addHeader("Authorization", "Bearer " + authKey); // Adicionar o header de autenticação
  int httpResponseCode = http.POST(postData);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println(httpResponseCode);
    Serial.println(response);
    // Verificar se a resposta contém erro de autenticação
    if (response.indexOf("error") >= 0) {
      Serial.println("Erro na autenticação. Verifique a chave de autenticação.");
    }
  } else {
    Serial.println("Erro na solicitação HTTP.");
  }
  http.end();
  delay(100);  // Aguardar um intervalo de tempo antes de enviar os dados novamente
}
