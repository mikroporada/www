#!/bin/bash

# Configuration
SERVICE_URL="http://localhost:8000"

function show_help() {
    echo "LLM Service CLI"
    echo "Usage:"
    echo "  ./llm-cli.sh chat [message] - Send a chat message"
    echo "  ./llm-cli.sh pdf [html_file] - Generate PDF from HTML"
    echo "  ./llm-cli.sh health - Check service health"
    echo "  ./llm-cli.sh test - Run tests"
    exit 0
}

function chat() {
    local message="$1"
    if [ -z "$message" ]; then
        echo "Error: Message is required"
        exit 1
    fi

    curl -X POST \
        -H "Content-Type: application/json" \
        -d "{\"message\": \"$message\", \"session_id\": \"$(uuidgen)\"}" \
        "$SERVICE_URL/chat"
}

function generate_pdf() {
    local html_file="$1"
    if [ ! -f "$html_file" ]; then
        echo "Error: HTML file not found"
        exit 1
    fi

    curl -X POST \
        -H "Content-Type: application/json" \
        -d "$(cat $html_file | jq -Rs .)" \
        "$SERVICE_URL/generate-pdf"
}

function check_health() {
    curl -X GET "$SERVICE_URL/health"
}

function run_tests() {
    python3 tests/test_llm.py
}

# Main
if [ $# -eq 0 ]; then
    show_help
fi

COMMAND="$1"
shift

CASE "COMMAND" in
    "chat")
        chat "$@"
        ;;
    "pdf")
        generate_pdf "$@"
        ;;
    "health")
        check_health
        ;;
    "test")
        run_tests
        ;;
    *)
        show_help
        ;;
esac
