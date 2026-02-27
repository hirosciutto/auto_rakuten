#!/bin/bash

echo "SSH トンネルを開始します... (127.0.0.1:23309 → mysql3113.db.sakura.ne.jp:3306)"

ssh -4 -N -L 127.0.0.1:23309:mysql3113.db.sakura.ne.jp:3306 \
    coslog@coslog.sakura.ne.jp \
    -i ./coslog_ecdsa.pem \
    -o ExitOnForwardFailure=yes \
    -o ServerAliveInterval=60 \
    -o StrictHostKeyChecking=accept-new &

SSH_PID=$!
sleep 2

if kill -0 "$SSH_PID" 2>/dev/null; then
    echo "✅ トンネル接続成功 (PID: $SSH_PID)"
    echo "   mysql -h 127.0.0.1 -P 23309 で接続できます"
    echo "   終了するには Ctrl+C を押してください"
    trap "kill $SSH_PID 2>/dev/null; echo ''; echo 'トンネルを終了しました。'" EXIT
    wait "$SSH_PID"
else
    echo "❌ トンネル接続に失敗しました"
    exit 1
fi

