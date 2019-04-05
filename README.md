# Monero-Lazy-Monitor
Ahh e tal somos preguiçosos e queremos ter tudo num só sitio sem termos que nos chatear muito !!!
## Requirements 
- WebServer/PHP
    ```
    apt-get install apache2
    apt-get install php
    ```
- cURL 
    ```
    apt-get install php-curl
    ```
- DOMDocument
    ```
    apt-get install libxml2-dev
    apt-get install php-dom
    ```
- Paciencia (muita)

## Configuration
Edit `config.json` file: 
- *"refresh"*: refresh rate value in seconds

- Workers

| Field | Data |
| ---: | :--- |
| *"id"* | worker/rig ID (must be unique) |
| *"ip"* | worker/rig IP address |
| *"port"* | listening port |
| *"soft"* | aceptable values "xmrig" or "stak" |
| *"alert"* | Minimum aceptable thread hashrate, iqual or bellow this value, threads will apear in red color |
```
{
    "refresh": 90,
    "workers": [
        {
            "id": "RIG-ID-1",
            "ip": "10.0.0.8",
            "port": "8081",
            "soft": "xmrig",
            "alert": 0
        },
        {
            "id": "RIG-ID-2",
            "ip": "10.0.0.9",
            "port": "5002",
            "soft": "stak",
            "alert": 0
        }
    ]
}
```
## Donations 
Não preciso disto para comer, mas já bebia um cafézito!
    
   XMR: `49Rj5W3gK3gcTYYKZq2rnkAvTFfaQestpVWJgGbSXwneWCCRs6cxKAAF2YgNe4e7NJdjGbqUyqMwj38SQfp3V5XmAzrjMdu`
    
   BTC: `(brevemente) Sou tão pobre que ainda nem uma carteira vazia tenho !!`
