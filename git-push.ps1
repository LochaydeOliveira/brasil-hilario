# Script para automatizar commit e push
param(
    [Parameter(Mandatory=$true)]
    [string]$mensagem
)

# Adiciona todas as alterações
git add .

# Faz o commit com a mensagem fornecida
git commit -m $mensagem

# Faz o push para a branch main
git push origin main

Write-Host "`nOperação concluída com sucesso!" -ForegroundColor Green 