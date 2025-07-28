<?php
require_once 'config/config.php';
require_once 'includes/db.php';
require_once 'includes/VisualConfigManager.php';

echo "🔄 Forçando regeneração do CSS com fontes e paginação...\n\n";

$visualManager = new VisualConfigManager($pdo);

// Configurações padrão
$configuracoes_padrao = [
    // Paginação
    ['paginacao', 'cor_fundo', '#ffffff'],
    ['paginacao', 'cor_texto', '#007bff'],
    ['paginacao', 'cor_link', '#007bff'],
    ['paginacao', 'cor_ativa', '#007bff'],
    
    // Fontes
    ['site', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif'],
    ['titulo', 'fonte', 'Merriweather, serif'],
    ['paragrafo', 'fonte', 'Inter, sans-serif'],
    
    // Tamanhos
    ['titulo', 'tamanho', '28px'],
    ['paragrafo', 'tamanho', '16px'],
    
    // Botões
    ['botao', 'cor_primario', '#007bff'],
    ['botao', 'cor_secundario', '#6c757d'],
    ['botao', 'cor_sucesso', '#28a745'],
    
    // Cards
    ['card', 'cor_fundo', '#ffffff'],
    ['card', 'cor_borda', '#dee2e6'],
    ['card', 'cor_texto', '#212529'],
];

echo "📝 Aplicando configurações padrão:\n";
foreach ($configuracoes_padrao as $config) {
    $elemento = $config[0];
    $propriedade = $config[1];
    $valor = $config[2];
    
    if (strpos($propriedade, 'cor_') === 0) {
        $resultado = $visualManager->setCor($elemento, $propriedade, $valor);
        echo $resultado ? "✅" : "❌";
        echo " {$elemento}.{$propriedade} = {$valor}\n";
    } else {
        $resultado = $visualManager->setFonte($elemento, $propriedade, $valor);
        echo $resultado ? "✅" : "❌";
        echo " {$elemento}.{$propriedade} = {$valor}\n";
    }
}

echo "\n🎨 Gerando CSS...\n";
$css_salvo = $visualManager->saveCSS();

if ($css_salvo) {
    echo "✅ CSS gerado com sucesso!\n";
    echo "📁 Arquivo: assets/css/dynamic.css\n";
    
    // Mostrar o CSS gerado
    echo "\n📄 CSS gerado:\n";
    $css = $visualManager->generateCSS();
    echo $css;
} else {
    echo "❌ Erro ao gerar CSS\n";
}

echo "\n🎯 Agora acesse:\n";
echo "http://localhost/brasil-hilario/admin/configuracoes-visuais.php\n";
echo "As fontes e paginação devem estar funcionando!\n";
?> 