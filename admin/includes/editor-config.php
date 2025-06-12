<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';

// Configuração do TinyMCE
$editor_config = [
    'selector' => '#editor',
    'height' => 500,
    'plugins' => [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    'toolbar' => 'undo redo | blocks | ' .
                'bold italic backcolor | alignleft aligncenter ' .
                'alignright alignjustify | bullist numlist outdent indent | ' .
                'removeformat | help',
    'content_style' => 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }',
    'images_upload_url' => 'upload-image.php',
    'images_upload_handler' => 'function (blobInfo, success, failure) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "upload-image.php");
        xhr.onload = function() {
            if (xhr.status != 200) {
                failure("Erro ao fazer upload da imagem: " + xhr.statusText);
                return;
            }
            var json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location != "string") {
                failure("Resposta inválida do servidor");
                return;
            }
            success(json.location);
        };
        var formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    }',
    'language' => 'pt_BR',
    'language_url' => '../assets/js/tinymce/langs/pt_BR.js'
];
?>
