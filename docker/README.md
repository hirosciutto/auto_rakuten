# moa_docker 

moaの開発環境用のリポジトリ

## versions  
- php:8.1
- mysql:8.0

## 起動方法
1. `./start_docker.sh` を実行

### トラブルシュート
- docker-compose でupできない（Timeoutなど）  
dockerのupdateを確認する。  
最新の場合はdockerの再起動、PCの再起動を試す  
https://qiita.com/yokota02210301/items/bd0ed35fa638f24eb4b0  

- nginxのaccess.log, error.logがディレクトリと怒られる  
`touch nginx/log/access.log` , `touch nginx/log/error.log` でファイルを事前に作ると解決  
※ なぜディレクトリになるかは原因不明

