<?php
// Simular as configurações que viriam do banco de dados
$font_configs = [
    'titulo_conteudo' => [
        'fonte' => 'Arial, Helvetica, sans-serif',
        'peso' => '700'
    ],
    'titulo_conteudo_h1' => [
        'desktop' => '36px',
        'mobile' => '30px'
    ],
    'titulo_conteudo_h2' => [
        'desktop' => '30px',
        'mobile' => '26px'
    ],
    'titulo_conteudo_h3' => [
        'desktop' => '26px',
        'mobile' => '22px'
    ]
];

// Simular conteúdo HTML
$content = '<div class="post-content">
    <h1>Título Principal</h1>
    <p>Parágrafo de teste.</p>
    <h2>Subtítulo</h2>
    <p>Outro parágrafo.</p>
    <h3>Sub-subtítulo</h3>
    <p>Mais um parágrafo.</p>
</div>';

// Função que estamos testando (versão corrigida)
function applyContentTitleStyles($content, $font_configs) {
    // Verificar se há configurações para títulos de conteúdo
    $has_configs = false;
    
    // Verificar se há configurações básicas (fonte e peso)
    if (isset($font_configs['titulo_conteudo']['fonte']) || 
        isset($font_configs['titulo_conteudo']['peso'])) {
        $has_configs = true;
    }
    
    // Verificar se há configurações de tamanho
    if (isset($font_configs['titulo_conteudo_h1']['desktop']) || 
        isset($font_configs['titulo_conteudo_h1']['mobile']) ||
        isset($font_configs['titulo_conteudo_h2']['desktop']) || 
        isset($font_configs['titulo_conteudo_h2']['mobile']) ||
        isset($font_configs['titulo_conteudo_h3']['desktop']) || 
        isset($font_configs['titulo_conteudo_h3']['mobile'])) {
        $has_configs = true;
    }
    
    if (!$has_configs) {
        return $content;
    }
    
    // Gerar CSS para títulos de conteúdo
    $css = '<style>';
    $css .= '/* CSS gerado dinamicamente para títulos de conteúdo */';
    
    // Estilos básicos para todos os títulos (desktop)
    $css .= 'body .post-content h1, body .post-content h2, body .post-content h3, body .post-content h4, body .post-content h5, body .post-content h6 {';
    
    if (isset($font_configs['titulo_conteudo']['fonte'])) {
        $css .= "font-family: {$font_configs['titulo_conteudo']['fonte']} !important; ";
    }
    if (isset($font_configs['titulo_conteudo']['peso'])) {
        $css .= "font-weight: {$font_configs['titulo_conteudo']['peso']} !important; ";
    }
    
    $css .= '}';
    
    // Tamanhos específicos para cada nível de título (desktop)
    if (isset($font_configs['titulo_conteudo_h1']['desktop'])) {
        $css .= "body .post-content h1 { font-size: {$font_configs['titulo_conteudo_h1']['desktop']} !important; }";
    }
    if (isset($font_configs['titulo_conteudo_h2']['desktop'])) {
        $css .= "body .post-content h2 { font-size: {$font_configs['titulo_conteudo_h2']['desktop']} !important; }";
    }
    if (isset($font_configs['titulo_conteudo_h3']['desktop'])) {
        $css .= "body .post-content h3 { font-size: {$font_configs['titulo_conteudo_h3']['desktop']} !important; }";
    }
    
    // CSS responsivo para mobile - com maior especificidade
    $css .= '@media (max-width: 768px) {';
    $css .= '/* Estilos mobile para títulos de conteúdo */';
    
    // Aplicar fonte e peso também no mobile
    if (isset($font_configs['titulo_conteudo']['fonte'])) {
        $css .= "body .post-content h1, body .post-content h2, body .post-content h3, body .post-content h4, body .post-content h5, body .post-content h6 { font-family: {$font_configs['titulo_conteudo']['fonte']} !important; }";
    }
    if (isset($font_configs['titulo_conteudo']['peso'])) {
        $css .= "body .post-content h1, body .post-content h2, body .post-content h3, body .post-content h4, body .post-content h5, body .post-content h6 { font-weight: {$font_configs['titulo_conteudo']['peso']} !important; }";
    }
    
    // Tamanhos específicos para mobile - com maior especificidade
    if (isset($font_configs['titulo_conteudo_h1']['mobile'])) {
        $css .= "body .post-content h1 { font-size: {$font_configs['titulo_conteudo_h1']['mobile']} !important; }";
    }
    if (isset($font_configs['titulo_conteudo_h2']['mobile'])) {
        $css .= "body .post-content h2 { font-size: {$font_configs['titulo_conteudo_h2']['mobile']} !important; }";
    }
    if (isset($font_configs['titulo_conteudo_h3']['mobile'])) {
        $css .= "body .post-content h3 { font-size: {$font_configs['titulo_conteudo_h3']['mobile']} !important; }";
    }
    
    $css .= '}';
    
    $css .= '</style>';
    $css .= '<!-- CSS para títulos de conteúdo aplicado dinamicamente -->';
    
    // Inserir CSS no início do conteúdo
    return $css . $content;
}

// Testar a função
echo "<h2>Teste da Função applyContentTitleStyles (Versão Corrigida)</h2>";

echo "<h3>Configurações de teste:</h3>";
echo "<pre>";
print_r($font_configs);
echo "</pre>";

echo "<h3>Conteúdo original:</h3>";
echo htmlspecialchars($content);

echo "<h3>Conteúdo com estilos aplicados:</h3>";
$resultado = applyContentTitleStyles($content, $font_configs);
echo $resultado;

echo "<h3>CSS gerado:</h3>";
$css_gerado = applyContentTitleStyles('', $font_configs);
echo "<pre>" . htmlspecialchars($css_gerado) . "</pre>";

echo "<h3>Teste de responsividade:</h3>";
echo "<p>Para testar a responsividade, redimensione a janela do navegador para menos de 768px de largura.</p>";
echo "<p>Os títulos devem mudar de tamanho automaticamente.</p>";
?> 