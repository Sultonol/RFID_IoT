import paho.mqtt.client as mqtt
import json
import mysql.connector

# ================= DATABASE =================
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="iot_rfid"
)

cursor = db.cursor(buffered=True)

# ================= MQTT =================
broker = "192.168.1.10"
topic = "sensor/rfid"

def on_connect(client, userdata, flags, rc):
    print("Connected to MQTT")
    client.subscribe(topic)

def on_message(client, userdata, msg):
    print("Data diterima:", msg.payload.decode())

    data = json.loads(msg.payload.decode())

    uid = data["uid"]
    waktu = data["time"]

    # 🔥 CARI NAMA DARI TABEL USERS
    cursor.execute("SELECT nama FROM mahasiswa WHERE uid=%s", (uid,))
    result = cursor.fetchone()

    if result:
        nama = result[0]
    else:
        nama = "Tidak Dikenal"

    # 🔥 SIMPAN KE mqtt_logs
    cursor.execute("INSERT INTO mqtt_logs (uid, nama, waktu) VALUES (%s, %s, %s)", (uid, nama, waktu))
    db.commit()

    print(f"UID: {uid} → Nama: {nama}")

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

client.connect(broker, 1883, 60)
client.loop_forever()