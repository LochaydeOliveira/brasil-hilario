<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/db.php';
require_once 'includes/VisualConfigManager.php';

$visualConfig = new VisualConfigManager($pdo);

echo "Gerando CSS dinâmico...\n";

$css_salvo = $visualConfig->saveCSS();

if ($css_salvo) {
    echo "✅ CSS dinâmico gerado com sucesso!\n";
    echo "📁 Arquivo salvo em: assets/css/dynamic.css\n";
} else {
    echo "❌ Erro ao gerar CSS dinâmico\n";
}

echo "\nCorreções aplicadas:\n";
echo "✅ Erro de digitação corrigido: var(--font-primaary) → var(--font-primary)\n";
echo "✅ Fonte adicionada para .sidebar-widget h3\n";
echo "✅ Todas as fontes da sidebar agora usam variáveis CSS\n";
echo "✅ ESPECIFICIDADE MÁXIMA adicionada com 'body' + '!important'\n";
echo "✅ Regras CSS com prioridade máxima para configurações do admin\n";
?> 