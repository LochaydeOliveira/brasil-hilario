<?php
require_once 'config/config.php';
require_once 'includes/db.php';

echo "<h2>Executando Nova Configuração de Fontes</h2>";

try {
    // Configurações para adicionar
    $configuracoes = [
        // Fonte geral do site
        ['fontes', 'site', 'fonte_geral', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'site', 'usar_fonte_geral', '1', 'boolean'],
        ['fontes', 'site', 'personalizar_fontes', '0', 'boolean'],
        
        // Configurações individuais de fontes
        ['fontes', 'titulos', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'paragrafos', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'navegacao', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'sidebar', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'cards', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'botoes', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'meta_textos', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        
        // Configurações de peso das fontes
        ['fontes', 'site', 'peso_titulos', '700', 'texto'],
        ['fontes', 'site', 'peso_paragrafos', '400', 'texto'],
        ['fontes', 'site', 'peso_navegacao', '500', 'texto'],
        ['fontes', 'site', 'peso_sidebar', '400', 'texto'],
        ['fontes', 'site', 'peso_cards', '400', 'texto'],
        ['fontes', 'site', 'peso_botoes', '500', 'texto'],
        ['fontes', 'site', 'peso_meta', '400', 'texto'],
        
        // Configurações de tamanho responsivo
        ['fontes', 'titulos', 'tamanho_desktop', '28px', 'texto'],
        ['fontes', 'titulos', 'tamanho_mobile', '24px', 'texto'],
        ['fontes', 'subtitulos', 'tamanho_desktop', '20px', 'texto'],
        ['fontes', 'subtitulos', 'tamanho_mobile', '18px', 'texto'],
        ['fontes', 'paragrafos', 'tamanho_desktop', '16px', 'texto'],
        ['fontes', 'paragrafos', 'tamanho_mobile', '14px', 'texto'],
        ['fontes', 'navegacao', 'tamanho_desktop', '14px', 'texto'],
        ['fontes', 'navegacao', 'tamanho_mobile', '12px', 'texto'],
        ['fontes', 'sidebar', 'tamanho_desktop', '14px', 'texto'],
        ['fontes', 'sidebar', 'tamanho_mobile', '12px', 'texto'],
        ['fontes', 'cards', 'tamanho_desktop', '14px', 'texto'],
        ['fontes', 'cards', 'tamanho_mobile', '12px', 'texto'],
        ['fontes', 'botoes', 'tamanho_desktop', '14px', 'texto'],
        ['fontes', 'botoes', 'tamanho_mobile', '12px', 'texto'],
        ['fontes', 'meta_textos', 'tamanho_desktop', '12px', 'texto'],
        ['fontes', 'meta_textos', 'tamanho_mobile', '10px', 'texto'],
        
        // Configurações para seções específicas do blog
        ['fontes', 'leia_tambem', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'leia_tambem', 'peso_titulo', '600', 'texto'],
        ['fontes', 'leia_tambem', 'tamanho_titulo_desktop', '22px', 'texto'],
        ['fontes', 'leia_tambem', 'tamanho_titulo_mobile', '20px', 'texto'],
        ['fontes', 'leia_tambem', 'peso_texto', '400', 'texto'],
        ['fontes', 'leia_tambem', 'tamanho_texto_desktop', '14px', 'texto'],
        ['fontes', 'leia_tambem', 'tamanho_texto_mobile', '12px', 'texto'],
        
        ['fontes', 'ultimas_portal', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif', 'fonte'],
        ['fontes', 'ultimas_portal', 'peso_titulo', '600', 'texto'],
        ['fontes', 'ultimas_portal', 'tamanho_titulo_desktop', '22px', 'texto'],
        ['fontes', 'ultimas_portal', 'tamanho_titulo_mobile', '20px', 'texto'],
        ['fontes', 'ultimas_portal', 'peso_texto', '400', 'texto'],
        ['fontes', 'ultimas_portal', 'tamanho_texto_desktop', '14px', 'texto'],
        ['fontes', 'ultimas_portal', 'tamanho_texto_mobile', '12px', 'texto']
    ];
    
    $adicionadas = 0;
    $erros = [];
    
    foreach ($configuracoes as $config) {
        [$categoria, $elemento, $propriedade, $valor, $tipo] = $config;
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO configuracoes_visuais (categoria, elemento, propriedade, valor, tipo, ativo) 
                VALUES (?, ?, ?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()
            ");
            
            $resultado = $stmt->execute([$categoria, $elemento, $propriedade, $valor, $tipo]);
            
            if ($resultado) {
                $adicionadas++;
                echo "✅ {$categoria}.{$elemento}.{$propriedade} = {$valor}<br>";
            } else {
                $erros[] = "❌ Falha ao adicionar {$categoria}.{$elemento}.{$propriedade}";
                echo "❌ Falha ao adicionar {$categoria}.{$elemento}.{$propriedade}<br>";
            }
        } catch (Exception $e) {
            $erros[] = "❌ Erro: {$categoria}.{$elemento}.{$propriedade} - " . $e->getMessage();
            echo "❌ Erro: {$categoria}.{$elemento}.{$propriedade} - " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<br><h3>Resumo:</h3>";
    echo "✅ Configurações adicionadas: {$adicionadas}<br>";
    echo "❌ Erros: " . count($erros) . "<br>";
    
    if (!empty($erros)) {
        echo "<br><h4>Detalhes dos erros:</h4>";
        foreach ($erros as $erro) {
            echo "{$erro}<br>";
        }
    }
    
    // Regenerar CSS
    require_once '../includes/VisualConfigManager.php';
    $visualConfig = new VisualConfigManager($pdo);
    $css_salvo = $visualConfig->saveCSS();
    
    if ($css_salvo) {
        echo "<br>✅ CSS regenerado com sucesso!<br>";
    } else {
        echo "<br>❌ Erro ao regenerar CSS<br>";
    }
    
    echo "<br><a href='configuracoes-visuais.php' class='btn btn-primary'>Ir para Configurações Visuais</a>";
    
} catch (Exception $e) {
    echo "<br>❌ Erro geral: " . $e->getMessage();
}
?> 