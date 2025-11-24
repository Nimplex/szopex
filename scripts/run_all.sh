#!/usr/bin/env bash

if ! command -v tmux &> /dev/null; then
    echo "Error: tmux is not installed."
    exit 1
fi

dir=$(dirname "$0")

"$dir/run_postgres.sh"

session_name="dev_ngrok"

tmux new-session -d -s "$session_name" "$dir/dev.sh"
tmux split-window -h -t "$session_name:0" "$dir/ngrok.sh"
tmux select-layout -t "$session_name" tiled
tmux attach -t "$session_name"

