<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/db.php';
require_once '../includes/VisualConfigManager.php';
require_once 'includes/auth.php';

$visualConfig = new VisualConfigManager($pdo);
$mensagem = '';
$tipo_mensagem = 'success';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $salvas = 0;
        $debug_info = [];
        
        // Processar todas as configurações de cores
        foreach ($_POST as $chave => $valor) {
            if (strpos($chave, 'cor_') === 0 && !empty($valor)) {
                // Extrair elemento e propriedade do nome do campo
                // Exemplo: cor_header_link -> elemento: header, propriedade: cor_link
                $partes = explode('_', $chave, 3);
                if (count($partes) >= 3) {
                    $elemento = $partes[1];
                    $propriedade = 'cor_' . $partes[2];
                    
                    $resultado = $visualConfig->setCor($elemento, $propriedade, $valor);
                    if ($resultado) {
                        $salvas++;
                        $debug_info[] = "✅ {$chave} -> {$elemento}.{$propriedade} = {$valor}";
                    } else {
                        $debug_info[] = "❌ Falha ao salvar {$chave}";
                    }
                }
            }
        }
        
        // Processar todas as configurações de fontes
        foreach ($_POST as $chave => $valor) {
            if (strpos($chave, 'fonte_') === 0 && !empty($valor)) {
                // Exemplo: fonte_site -> elemento: site, propriedade: fonte
                $elemento = substr($chave, 6); // Remove 'fonte_'
                
                $resultado = $visualConfig->setFonte($elemento, 'fonte', $valor);
                if ($resultado) {
                    $salvas++;
                    $debug_info[] = "✅ {$chave} -> {$elemento}.fonte = {$valor}";
                } else {
                    $debug_info[] = "❌ Falha ao salvar {$chave}";
                }
            }
        }
        
        // Processar todos os tamanhos de fonte
        foreach ($_POST as $chave => $valor) {
            if (strpos($chave, 'tamanho_') === 0 && !empty($valor)) {
                // Exemplo: tamanho_titulo -> elemento: titulo, propriedade: tamanho
                $elemento = substr($chave, 8); // Remove 'tamanho_'
                
                $resultado = $visualConfig->setFonte($elemento, 'tamanho', $valor);
                if ($resultado) {
                    $salvas++;
                    $debug_info[] = "✅ {$chave} -> {$elemento}.tamanho = {$valor}";
                } else {
                    $debug_info[] = "❌ Falha ao salvar {$chave}";
                }
            }
        }
        
        // Gerar CSS dinâmico
        $css_salvo = $visualConfig->saveCSS();
        
        $mensagem = "Configurações visuais salvas com sucesso! ({$salvas} configurações atualizadas)";
        if (!$css_salvo) {
            $mensagem .= " ⚠️ CSS não foi atualizado";
        }
        $tipo_mensagem = 'success';
        
        // Debug temporário (remover depois)
        if (!empty($debug_info)) {
            $mensagem .= "\n\nDebug:\n" . implode("\n", $debug_info);
        }
        
    } catch (Exception $e) {
        $mensagem = 'Erro ao salvar configurações: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Obter configurações atuais
$configs = $visualConfig->getAllConfigs();

// Aplicar configurações padrão de fontes se não existirem
if (empty($configs['fontes']['site'])) {
    $visualConfig->setFonte('site', 'fonte', 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif');
    $visualConfig->setFonte('titulo', 'fonte', 'Merriweather, serif');
    $visualConfig->setFonte('paragrafo', 'fonte', 'Inter, sans-serif');
    $configs = $visualConfig->getAllConfigs(); // Recarregar configurações
    
    // Forçar regeneração do CSS
    $visualConfig->saveCSS();
}

$page_title = 'Configurações Visuais';
include 'includes/header.php';
?>

    <style>
        /* CSS copiado da página de configurações que funciona */
        .nav-tabs .nav-link {
            color: #495057!important;
            border: none;
            border-bottom: 6px solid transparent;
            padding: 0.75rem 1rem;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: #495057;
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color:#0b8103;
            background-color: transparent;
            color: #0b8103;
        }

        .nav-tabs .nav-link i {
            margin-right: 0.5rem;
        }

        .form-control-color {
            width: 6%;
            height: 40px;
            border-radius: 0;
            border: none!important;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header .card-title {
            color: #495057;
            font-weight: 600;
        }

        .card-header .card-title i {
            margin-right: 0.5rem;
            color: #0d6efd;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
    </style>
</head>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-palette"></i> Configurações Visuais
    </h1>
</div>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                    <pre style="white-space: pre-wrap; margin: 0;"><?= htmlspecialchars($mensagem) ?></pre>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Abas de Configurações -->
            <ul class="nav nav-tabs mb-4" id="visualTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="cores-tab" data-bs-toggle="tab" data-bs-target="#cores" type="button" role="tab">
                        <i class="fas fa-palette"></i> Cores
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fontes-tab" data-bs-toggle="tab" data-bs-target="#fontes" type="button" role="tab">
                        <i class="fas fa-font"></i> Fontes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview" type="button" role="tab">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                </li>
            </ul>
            
            <form method="POST">
                <input type="hidden" name="submit" value="1">
                
                <!-- Aba de Cores -->
                <div class="tab-content" id="visualTabsContent">
                    <div class="tab-pane fade show active" id="cores" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Cores Principais</h4>
                                <div class="mb-3">
                                    <label class="form-label">Cor Primária</label>
                                    <input type="color" class="form-control form-control-color" name="cor_site_primaria" 
                                           value="<?= $configs['cores']['site']['cor_primaria'] ?? '#007bff' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor Secundária</label>
                                    <input type="color" class="form-control form-control-color" name="cor_site_secundaria" 
                                           value="<?= $configs['cores']['site']['cor_secundaria'] ?? '#6c757d' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor de Sucesso</label>
                                    <input type="color" class="form-control form-control-color" name="cor_site_sucesso" 
                                           value="<?= $configs['cores']['site']['cor_sucesso'] ?? '#28a745' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor de Perigo</label>
                                    <input type="color" class="form-control form-control-color" name="cor_site_perigo" 
                                           value="<?= $configs['cores']['site']['cor_perigo'] ?? '#dc3545' ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Cores do Header</h4>
                                <div class="mb-3">
                                    <label class="form-label">Cor de Fundo</label>
                                    <input type="color" class="form-control form-control-color" name="cor_header_fundo" 
                                           value="<?= $configs['cores']['header']['cor_fundo'] ?? '#ffffff' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor do Texto</label>
                                    <input type="color" class="form-control form-control-color" name="cor_header_texto" 
                                           value="<?= $configs['cores']['header']['cor_texto'] ?? '#333333' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor dos Links</label>
                                    <input type="color" class="form-control form-control-color" name="cor_header_link" 
                                           value="<?= $configs['cores']['header']['cor_link'] ?? '#007bff' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor dos Links (Hover)</label>
                                    <input type="color" class="form-control form-control-color" name="cor_header_link_hover" 
                                           value="<?= $configs['cores']['header']['cor_link_hover'] ?? '#0056b3' ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Cores do Footer</h4>
                                <div class="mb-3">
                                    <label class="form-label">Cor de Fundo</label>
                                    <input type="color" class="form-control form-control-color" name="cor_footer_fundo" 
                                           value="<?= $configs['cores']['footer']['cor_fundo'] ?? '#f8f9fa' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor do Texto</label>
                                    <input type="color" class="form-control form-control-color" name="cor_footer_texto" 
                                           value="<?= $configs['cores']['footer']['cor_texto'] ?? '#6c757d' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor dos Links</label>
                                    <input type="color" class="form-control form-control-color" name="cor_footer_link" 
                                           value="<?= $configs['cores']['footer']['cor_link'] ?? '#007bff' ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Cores dos Botões</h4>
                                <div class="mb-3">
                                    <label class="form-label">Cor Primária</label>
                                    <input type="color" class="form-control form-control-color" name="cor_botao_primario" 
                                           value="<?= $configs['cores']['botao']['cor_primario'] ?? '#007bff' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor Secundária</label>
                                    <input type="color" class="form-control form-control-color" name="cor_botao_secundario" 
                                           value="<?= $configs['cores']['botao']['cor_secundario'] ?? '#6c757d' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor de Sucesso</label>
                                    <input type="color" class="form-control form-control-color" name="cor_botao_sucesso" 
                                           value="<?= $configs['cores']['botao']['cor_sucesso'] ?? '#28a745' ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Cores dos Cards</h4>
                                <div class="mb-3">
                                    <label class="form-label">Cor de Fundo</label>
                                    <input type="color" class="form-control form-control-color" name="cor_card_fundo" 
                                           value="<?= $configs['cores']['card']['cor_fundo'] ?? '#ffffff' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor da Borda</label>
                                    <input type="color" class="form-control form-control-color" name="cor_card_borda" 
                                           value="<?= $configs['cores']['card']['cor_borda'] ?? '#dee2e6' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cor do Texto</label>
                                    <input type="color" class="form-control form-control-color" name="cor_card_texto" 
                                           value="<?= $configs['cores']['card']['cor_texto'] ?? '#212529' ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                        </div>
                    </div>
                    
                    <!-- Aba de Fontes -->
                    <div class="tab-pane fade" id="fontes" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Fontes Principais</h4>
                                <div class="mb-3">
                                    <label class="form-label">Fonte Principal (Body)</label>
                                    <select class="form-select" name="fonte_site">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Courier New, monospace" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Courier New, monospace' ? 'selected' : '' ?>>Courier New</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['site']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonte dos Títulos</label>
                                    <select class="form-select" name="fonte_titulo">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['titulo']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonte dos Parágrafos</label>
                                    <select class="form-select" name="fonte_paragrafo">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['paragrafo']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonte dos Cards/Posts</label>
                                    <select class="form-select" name="fonte_card">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['card']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonte da Sidebar</label>
                                    <select class="form-select" name="fonte_sidebar">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['sidebar']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonte dos Meta Textos</label>
                                    <select class="form-select" name="fonte_meta">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['meta']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fonte dos Botões</label>
                                    <select class="form-select" name="fonte_botao">
                                        <option value="Segoe UI, Tahoma, Geneva, Verdana, sans-serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ? 'selected' : '' ?>>Segoe UI</option>
                                        <option value="Arial, sans-serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Arial, sans-serif' ? 'selected' : '' ?>>Arial</option>
                                        <option value="Helvetica, sans-serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Helvetica, sans-serif' ? 'selected' : '' ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Georgia, serif' ? 'selected' : '' ?>>Georgia</option>
                                        <option value="Times New Roman, serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Times New Roman, serif' ? 'selected' : '' ?>>Times New Roman</option>
                                        <option value="Merriweather, serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Merriweather, serif' ? 'selected' : '' ?>>Merriweather</option>
                                        <option value="Inter, sans-serif" <?= ($configs['fontes']['botao']['fonte'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Tamanhos de Fonte</h4>
                                <div class="mb-3">
                                    <label class="form-label">Tamanho dos Títulos</label>
                                    <select class="form-select" name="tamanho_titulo">
                                        <option value="28px" <?= ($configs['fontes']['titulo']['tamanho'] ?? '') === '28px' ? 'selected' : '' ?>>28px</option>
                                        <option value="24px" <?= ($configs['fontes']['titulo']['tamanho'] ?? '') === '24px' ? 'selected' : '' ?>>24px</option>
                                        <option value="20px" <?= ($configs['fontes']['titulo']['tamanho'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                                        <option value="18px" <?= ($configs['fontes']['titulo']['tamanho'] ?? '') === '18px' ? 'selected' : '' ?>>18px</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tamanho dos Subtítulos</label>
                                    <select class="form-select" name="tamanho_subtitulo">
                                        <option value="20px" <?= ($configs['fontes']['subtitulo']['tamanho'] ?? '') === '20px' ? 'selected' : '' ?>>20px</option>
                                        <option value="18px" <?= ($configs['fontes']['subtitulo']['tamanho'] ?? '') === '18px' ? 'selected' : '' ?>>18px</option>
                                        <option value="16px" <?= ($configs['fontes']['subtitulo']['tamanho'] ?? '') === '16px' ? 'selected' : '' ?>>16px</option>
                                        <option value="14px" <?= ($configs['fontes']['subtitulo']['tamanho'] ?? '') === '14px' ? 'selected' : '' ?>>14px</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tamanho dos Parágrafos</label>
                                    <select class="form-select" name="tamanho_paragrafo">
                                        <option value="16px" <?= ($configs['fontes']['paragrafo']['tamanho'] ?? '') === '16px' ? 'selected' : '' ?>>16px</option>
                                        <option value="14px" <?= ($configs['fontes']['paragrafo']['tamanho'] ?? '') === '14px' ? 'selected' : '' ?>>14px</option>
                                        <option value="12px" <?= ($configs['fontes']['paragrafo']['tamanho'] ?? '') === '12px' ? 'selected' : '' ?>>12px</option>
                                        <option value="18px" <?= ($configs['fontes']['paragrafo']['tamanho'] ?? '') === '18px' ? 'selected' : '' ?>>18px</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba de Preview -->
                    <div class="tab-pane fade" id="preview" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            As alterações serão aplicadas automaticamente após salvar.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Preview do Header</h4>
                                <div class="border rounded p-3" style="background-color: <?= $configs['cores']['header']['cor_fundo'] ?? '#ffffff' ?>; color: <?= $configs['cores']['header']['cor_texto'] ?? '#333333' ?>;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="logo">Brasil Hilário</div>
                                        <nav>
                                            <a href="#" style="color: <?= $configs['cores']['header']['cor_link'] ?? '#007bff' ?>;">Início</a>
                                            <a href="#" style="color: <?= $configs['cores']['header']['cor_link'] ?? '#007bff' ?>;">Sobre</a>
                                            <a href="#" style="color: <?= $configs['cores']['header']['cor_link'] ?? '#007bff' ?>;">Contato</a>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Preview do Conteúdo</h4>
                                <div class="border rounded p-3">
                                    <h1 style="font-family: <?= $configs['fontes']['titulo']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>; font-size: <?= $configs['fontes']['titulo']['tamanho'] ?? '28px' ?>; color: <?= $configs['cores']['site']['cor_primaria'] ?? '#007bff' ?>;">Título Principal</h1>
                                    <h2 style="font-family: <?= $configs['fontes']['titulo']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>; font-size: <?= $configs['fontes']['subtitulo']['tamanho'] ?? '20px' ?>; color: <?= $configs['cores']['site']['cor_secundaria'] ?? '#6c757d' ?>;">Subtítulo</h2>
                                    <p style="font-family: <?= $configs['fontes']['paragrafo']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>; font-size: <?= $configs['fontes']['paragrafo']['tamanho'] ?? '16px' ?>;">Este é um exemplo de parágrafo com as configurações de fonte aplicadas.</p>
                                    
                                    <div class="card mb-3" style="font-family: <?= $configs['fontes']['card']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>;">
                                        <div class="card-body">
                                            <h5 class="card-title">Título do Card</h5>
                                            <p class="card-text">Texto do card com fonte personalizada.</p>
                                            <small class="text-muted" style="font-family: <?= $configs['fontes']['meta']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>;">Meta texto - 15/01/2024</small>
                                        </div>
                                    </div>
                                    
                                    <div class="sidebar-preview" style="font-family: <?= $configs['fontes']['sidebar']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>;">
                                        <h6>Sidebar Preview</h6>
                                        <a href="#" class="d-block">Link da sidebar</a>
                                    </div>
                                    
                                    <button class="btn btn-primary" style="font-family: <?= $configs['fontes']['botao']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>; background-color: <?= $configs['cores']['site']['cor_primaria'] ?? '#007bff' ?>;">Botão Primário</button>
                                    <button class="btn btn-success" style="font-family: <?= $configs['fontes']['botao']['fonte'] ?? 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif' ?>; background-color: <?= $configs['cores']['site']['cor_sucesso'] ?? '#28a745' ?>;">Botão Sucesso</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                    <a href="configuracoes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>


<script>
// Atualizar preview em tempo real
document.querySelectorAll('input[type="color"], select').forEach(input => {
    input.addEventListener('change', function() {
        // Aqui você pode adicionar JavaScript para atualizar o preview em tempo real
        console.log('Configuração alterada:', this.name, this.value);
    });
});

// Atalho de teclado CTRL+S para salvar
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault(); // Previne o comportamento padrão do navegador
        
        // Mostrar feedback visual
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            submitBtn.disabled = true;
            
            // Simular clique no botão
            submitBtn.click();
            
            // Restaurar botão após 2 segundos
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        }
        
        // Mostrar notificação
        showNotification('Salvando configurações...', 'info');
    }
});

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Mostrar dica sobre o atalho
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.title = 'Pressione CTRL+S para salvar rapidamente';
    }
    
    // Mostrar dica inicial
    setTimeout(() => {
        showNotification('💡 Dica: Use CTRL+S para salvar rapidamente!', 'info');
    }, 1000);
});
</script>

<?php include 'includes/footer.php'; ?> 
