#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <PubSubClient.h>
#include <HTTPClient.h>
#include "time.h"

#define SS_PIN 5
#define RST_PIN 4
MFRC522 rfid(SS_PIN, RST_PIN);

LiquidCrystal_I2C lcd(0x27, 16, 2);

#define BUZZER_PIN 2

const char* ssid = "ZTE_2.4G_KesHDt";
const char* password = "rangkiang";

const char* mqtt_server = "192.168.1.32";
const char* serverUrl = "http://192.168.1.32/iot/update.php";

WiFiClient espClient;
PubSubClient client(espClient);

const char* ntpServer = "pool.ntp.org";
const long gmtOffset_sec = 7 * 3600;

String getTime() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) return "No Time";

  char buffer[30];
  strftime(buffer, sizeof(buffer), "%Y-%m-%d %H:%M:%S", &timeinfo);
  return String(buffer);
}

void reconnectWiFi() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi putus, reconnect...");

    WiFi.begin(ssid, password);

    unsigned long start = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - start < 10000) {
      delay(500);
      Serial.print(".");
    }

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("\nWiFi tersambung lagi");
      Serial.println(WiFi.localIP());
    } else {
      Serial.println("\nGagal reconnect WiFi");
    }
  }
}

void reconnectMQTT() {
  if (!client.connected()) {
    Serial.print("Connecting MQTT...");

    if (client.connect("ESP32Client")) {
      Serial.println("connected");
    } else {
      Serial.print("failed, rc=");
      Serial.println(client.state());
    }
  }
}

void setup() {
  Serial.begin(115200);

  SPI.begin();
  rfid.PCD_Init();

  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();

  pinMode(BUZZER_PIN, OUTPUT);

  WiFi.begin(ssid, password);
  Serial.print("Connecting WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi Connected!");
  Serial.println(WiFi.localIP());

  client.setServer(mqtt_server, 1883);

  configTime(gmtOffset_sec, 0, ntpServer);

  lcd.setCursor(0, 0);
  lcd.print("RFID READY");
  lcd.setCursor(0, 1);
  lcd.print("Tempel kartu");

  Serial.println("=== RFID READY ===");
}

void loop() {
  reconnectWiFi();
  reconnectMQTT();
  client.loop();

  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;

  digitalWrite(BUZZER_PIN, HIGH);
  delay(200);
  digitalWrite(BUZZER_PIN, LOW);

  String waktu = getTime();

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Scan Berhasil");

  Serial.print("UID: ");

  String uidStr = "";

  for (byte i = 0; i < rfid.uid.size; i++) {
    Serial.print(rfid.uid.uidByte[i], HEX);
    Serial.print(" ");
    uidStr += String(rfid.uid.uidByte[i], HEX);
  }

  Serial.println();
  Serial.println("Waktu: " + waktu);

  String data = "{\"uid\":\"" + uidStr + "\",\"time\":\"" + waktu + "\"}";

  if (client.connected()) {
    client.publish("sensor/rfid", data.c_str());
    Serial.println("Data dikirim ke MQTT:");
    Serial.println(data);
  } else {
    Serial.println("MQTT belum connect");
  }

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");

    int responseCode = http.POST(data);

    Serial.print("HTTP Response: ");
    Serial.println(responseCode);

    http.end();
  }

  lcd.setCursor(0, 1);
  lcd.print("OK");

  delay(2000);

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Tempel kartu");
}