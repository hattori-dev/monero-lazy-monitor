# Monero-Lazy-Monitor

So we're lazy and we want to have everything in one place without to much hassle !!!

## Requirements 

- WebServer (we will be using Apache here), PHP (and some php libs), Curl, Patience

## Configuration

On Linux(debian based):

First install dependencies

  sudo update
  sudo apt install apache2 php php-curl libxml2-dev php-dom

Clone the repository

  git clone https://github.com/hattori-dev/monero-lazy-monitor/
  cd monero-lazy-monitor

Edit workers.txt and generate config.json

  nano workers.txt 
  php update.php

Copy files to Apache web server dir

  cp * /var/www/html

Edit `config.json` file: 

  cd /var/www/html
  nano config.json

- *"refresh"*: refresh rate value in seconds

- Workers

| Field | Data |
| ---: | :--- |
| *"id"* | worker/rig ID (must be unique) |
| *"ip"* | worker/rig IP address |
| *"port"* | listening port |
| *"soft"* | aceptable values "xmrig" or "stak" |
| *"alert"* | Minimum aceptable thread hashrate, iqual or bellow this value, threads will apear in red color |

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

## Donations 

I don't need this to eat, but I could use some coffee
    
   XMR: `49Rj5W3gK3gcTYYKZq2rnkAvTFfaQestpVWJgGbSXwneWCCRs6cxKAAF2YgNe4e7NJdjGbqUyqMwj38SQfp3V5XmAzrjMdu`
    
   BTC: `(Soon) I'm so poor I don't even have an empty wallet !!`
