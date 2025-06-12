<?php
/**
 * Arquivo: editor-config.php
 * Descrição: Configuração do editor TinyMCE
 * Funcionalidades:
 * - Define configurações do editor
 * - Configura plugins e barras de ferramentas
 * - Define estilos e formatação
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';

// Configuração do editor TinyMCE
$editor_config = [
    // Seletor do elemento que será transformado em editor
    'selector' => '#editor',
    
    // Altura do editor
    'height' => 500,
    
    // Plugins habilitados
    'plugins' => [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    
    // Barra de ferramentas
    'toolbar' => 'undo redo | blocks | ' .
                'bold italic backcolor | alignleft aligncenter ' .
                'alignright alignjustify | bullist numlist outdent indent | ' .
                'removeformat | help',
    
    // Estilos de conteúdo
    'content_style' => 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }',
    
    // Configurações de linguagem
    'language' => 'pt_BR',
    'language_url' => 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/langs/pt_BR.min.js',
    
    // Configurações de imagem
    'images_upload_url' => 'upload-image.php',
    'images_upload_handler' => 'function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open("POST", "upload-image.php");
        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                failure("HTTP Error: " + xhr.status);
                return;
            }
            json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != "string") {
                failure("Invalid JSON: " + xhr.responseText);
                return;
            }
            success(json.location);
        };
        formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    }',
    
    // Configurações de segurança
    'valid_elements' => '*[*]', // Permite todos os elementos e atributos
    'extended_valid_elements' => 'script[src|type|async|defer]', // Permite scripts
    
    // Configurações de formatação
    'formats' => [
        'alignleft' => [
            'selector' => 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
            'styles' => ['text-align' => 'left']
        ],
        'aligncenter' => [
            'selector' => 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
            'styles' => ['text-align' => 'center']
        ],
        'alignright' => [
            'selector' => 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
            'styles' => ['text-align' => 'right']
        ],
        'alignjustify' => [
            'selector' => 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li',
            'styles' => ['text-align' => 'justify']
        ]
    ],
    
    // Configurações de estilo
    'style_formats' => [
        [
            'title' => 'Títulos',
            'items' => [
                ['title' => 'Título 1', 'format' => 'h1'],
                ['title' => 'Título 2', 'format' => 'h2'],
                ['title' => 'Título 3', 'format' => 'h3'],
                ['title' => 'Título 4', 'format' => 'h4'],
                ['title' => 'Título 5', 'format' => 'h5'],
                ['title' => 'Título 6', 'format' => 'h6']
            ]
        ],
        [
            'title' => 'Blocos',
            'items' => [
                ['title' => 'Parágrafo', 'format' => 'p'],
                ['title' => 'Bloco', 'format' => 'div'],
                ['title' => 'Citação', 'format' => 'blockquote']
            ]
        ],
        [
            'title' => 'Alinhamento',
            'items' => [
                ['title' => 'Esquerda', 'format' => 'alignleft'],
                ['title' => 'Centro', 'format' => 'aligncenter'],
                ['title' => 'Direita', 'format' => 'alignright'],
                ['title' => 'Justificado', 'format' => 'alignjustify']
            ]
        ]
    ]
];
?>
