#!/bin/bash

# Verifica se foi fornecida uma mensagem
if [ -z "$1" ]; then
    echo "Erro: Por favor, forneça uma mensagem para o commit"
    echo "Uso: ./git-push.sh \"sua mensagem de commit\""
    exit 1
fi

# Adiciona todas as alterações
git add .

# Faz o commit com a mensagem fornecida
git commit -m "$1"

# Faz o push para a branch main
git push origin main

echo -e "\n\033[32mOperação concluída com sucesso!\033[0m" 