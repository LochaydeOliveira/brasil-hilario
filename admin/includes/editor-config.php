<?php
// Configuração do TinyMCE
$editor_config = [
    'selector' => '#editor',
    'plugins' => [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'codesample', 'hr', 'pagebreak', 'nonbreaking', 'toc', 'visualchars',
        'quickbars', 'emoticons', 'codesample', 'hr', 'pagebreak', 'nonbreaking',
        'toc', 'visualchars', 'quickbars'
    ],
    'toolbar' => 'undo redo | blocks | ' .
                'bold italic backcolor | alignleft aligncenter ' .
                'alignright alignjustify | bullist numlist outdent indent | ' .
                'removeformat | image media link | help',
    'images_upload_url' => 'upload.php',
    'images_upload_handler' => 'function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open("POST", "upload.php");
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
    'height' => 500,
    'menubar' => 'file edit view insert format tools table help',
    'content_style' => 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; }',
    'branding' => false,
    'promotion' => false,
    'language' => 'pt_BR',
    'language_url' => '../assets/js/tinymce/langs/pt_BR.js',
    'quickbars_selection_toolbar' => 'bold italic | quicklink h2 h3 blockquote',
    'quickbars_insert_toolbar' => 'quickimage quicktable',
    'contextmenu' => 'link image table',
    'automatic_uploads' => true,
    'file_picker_types' => 'image',
    'images_reuse_filename' => true,
    'relative_urls' => false,
    'remove_script_host' => false,
    'convert_urls' => true,
    'image_title' => true,
    'image_caption' => true,
    'image_advtab' => true,
    'image_class_list' => [
        ['title' => 'Nenhuma', 'value' => ''],
        ['title' => 'Responsiva', 'value' => 'img-fluid'],
        ['title' => 'Arredondada', 'value' => 'rounded'],
        ['title' => 'Circular', 'value' => 'rounded-circle'],
        ['title' => 'Com Sombra', 'value' => 'shadow']
    ],
    'templates' => [
        [
            'title' => 'Post Padrão',
            'description' => 'Template para posts padrão',
            'content' => '<h2>Título do Post</h2><p>Conteúdo do post...</p>'
        ],
        [
            'title' => 'Post com Imagem',
            'description' => 'Template para posts com imagem destacada',
            'content' => '<h2>Título do Post</h2><img src="" alt="Imagem destacada" class="img-fluid mb-3"><p>Conteúdo do post...</p>'
        ]
    ]
]; 