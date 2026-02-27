#!/bin/sh
# Docker環境を変更した場合などに利用する
# 通常の起動、停止はdocker-compose start, docker-compose stop

# コンテナとネットワークを停止して削除
docker-compose down
# キャッシュを利用せずDockerをビルド
docker-compose build --no-cache
# Docker起動(Compose ファイルで定義されていないサービス用のコンテナを削除)
docker-compose up -d --remove-orphans
docker-compose ps

# Application関連
