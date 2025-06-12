<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';

// Configuração do TinyMCE
$editor_config = [
    'selector' => 'textarea#editor',
    'height' => 500,
    'menubar' => true,
    'plugins' => [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
        'codesample', 'hr', 'pagebreak', 'nonbreaking', 'toc', 'visualchars',
        'quickbars', 'imagetools', 'paste', 'autoresize'
    ],
    'toolbar' => 'undo redo | styles | bold italic underline strikethrough | ' .
                'alignleft aligncenter alignright alignjustify | ' .
                'bullist numlist outdent indent | link image media | ' .
                'forecolor backcolor emoticons | removeformat code | ' .
                'fullscreen preview | help',
    'content_style' => 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; } ' .
                      'h1, h2, h3, h4, h5, h6 { margin-top: 24px; margin-bottom: 16px; font-weight: 600; line-height: 1.25; } ' .
                      'p { margin-top: 0; margin-bottom: 16px; } ' .
                      'img { max-width: 100%; height: auto; } ' .
                      'blockquote { padding: 0 1em; color: #6a737d; border-left: 0.25em solid #dfe2e5; margin: 0 0 16px 0; } ' .
                      'code { padding: 0.2em 0.4em; margin: 0; font-size: 85%; background-color: rgba(27,31,35,0.05); border-radius: 3px; } ' .
                      'pre { padding: 16px; overflow: auto; font-size: 85%; line-height: 1.45; background-color: #f6f8fa; border-radius: 3px; } ' .
                      'table { border-spacing: 0; border-collapse: collapse; margin: 16px 0; } ' .
                      'table th, table td { padding: 6px 13px; border: 1px solid #dfe2e5; } ' .
                      'table tr { background-color: #fff; border-top: 1px solid #c6cbd1; } ' .
                      'table tr:nth-child(2n) { background-color: #f6f8fa; }',
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
    'quickbars_selection_toolbar' => 'bold italic | quicklink h2 h3 blockquote',
    'quickbars_insert_toolbar' => 'quickimage quicktable',
    'contextmenu' => 'link image table',
    'branding' => false,
    'promotion' => false,
    'browser_spellcheck' => true,
    'paste_data_images' => true,
    'image_advtab' => true,
    'image_title' => true,
    'automatic_uploads' => true,
    'file_picker_types' => 'image',
    'images_reuse_filename' => true,
    'relative_urls' => false,
    'remove_script_host' => false,
    'convert_urls' => true,
    'language' => 'pt_BR',
    'language_url' => '/assets/js/tinymce/langs/pt_BR.js'
];
?>
