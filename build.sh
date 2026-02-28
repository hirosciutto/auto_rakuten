#!/bin/bash

scp -r -P 22 -i ./coslog_ecdsa.pem ./public/build coslog@coslog.sakura.ne.jp:/home/coslog/www/auto_rakuten/public/
