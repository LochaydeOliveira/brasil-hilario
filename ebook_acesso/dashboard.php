<?php
require_once __DIR__ . '/conexao.php';
require_login();

$ebookPath = __DIR__ . '/libido.txt';
$rawText = is_file($ebookPath) ? file_get_contents($ebookPath) : "Arquivo 'libido.txt' não encontrado.";

function convertEbookTextToHtml(string $text): array {
    $lines = preg_split("/\r?\n/", $text);
    $title = trim($lines[0] ?? 'E-book');
    $subtitle = trim($lines[1] ?? '');

    $html = '';
    foreach ($lines as $idx => $line) {
        $line = trim($line);
        if ($line === '') { continue; }
        if ($idx === 0 || $idx === 1) { continue; }

        if (preg_match('/^Introdução/i', $line)) {
            $html .= '<div class="section-divider"><span>Introdução</span></div>';
            $line = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
            $html .= '<h2 class="h3 mt-3">' . $line . '</h2>';
            continue;
        }
        if (preg_match('/^Fase\s*1/i', $line)) {
            $html .= '<div class="section-divider"><span>Fase 1</span></div>';
            $html .= '<h2 class="h4 mt-3">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</h2>';
            continue;
        }
        if (preg_match('/^Fase\s*2/i', $line)) {
            $html .= '<div class="section-divider"><span>Fase 2</span></div>';
            $html .= '<h2 class="h4 mt-3">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</h2>';
            continue;
        }
        if (preg_match('/^Fase\s*3/i', $line)) {
            $html .= '<div class="section-divider"><span>Fase 3</span></div>';
            $html .= '<h2 class="h4 mt-3">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</h2>';
            continue;
        }
        if (preg_match('/^Conclusão/i', $line)) {
            $html .= '<div class="section-divider"><span>Conclusão</span></div>';
            $html .= '<h2 class="h4 mt-3">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</h2>';
            continue;
        }

        // Lista marcada simples: linhas iniciando com "- " ou "* "
        if (preg_match('/^[-*]\s+/', $line)) {
            // abre/fecha UL inteligentemente
            if (!str_ends_with($html, '</ul>')) { $html .= '<ul class="lp-list">'; }
            $html .= '<li>' . htmlspecialchars(preg_replace('/^[-*]\s+/', '', $line), ENT_QUOTES, 'UTF-8') . '</li>';
            // lookahead próxima linha; se não for item, fecha ul depois no loop
        } else {
            if (str_ends_with($html, '</li>')) { $html .= '</ul>'; }
            $html .= '<p>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</p>';
        }
    }
    if (str_ends_with($html, '</li>')) { $html .= '</ul>'; }

    return [$title, $subtitle, $html];
}

[$ebookTitle, $ebookSubtitle, $conteudoHtml] = convertEbookTextToHtml($rawText);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($ebookTitle); ?> | Área Restrita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#faf7f2}
        .topbar{background:#d2691e;color:#fff}
        .content{max-width:980px;margin:20px auto}
        .hero{background:linear-gradient(180deg,#d2691e, #e59b6a);color:#fff;border-radius:14px;padding:28px}
        .hero h1{font-weight:800;margin:0}
        .hero p{margin:6px 0 0 0}
        .badge-pill{background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);padding:6px 10px;border-radius:999px;margin-right:6px}
        .section-divider{text-align:center;margin:36px 0;position:relative}
        .section-divider span{background:#faf7f2;padding:0 12px;color:#d2691e;font-weight:700}
        .section-divider:before{content:'';position:absolute;left:0;right:0;top:50%;height:1px;background:linear-gradient(90deg,transparent,#e5c7b4,transparent)}
        .lp-list{margin:12px 0 12px 18px}
        .card-ebook{box-shadow:0 12px 24px rgba(0,0,0,.06);border:1px solid #f0e7dd}
    </style>
</head>
<body>
    <nav class="topbar navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand text-white fw-bold">Redescobrindo o Desejo</span>
            <div class="ms-auto d-flex align-items-center gap-3">
                <?php if (is_admin()): ?><a class="btn btn-sm btn-light" href="admin/">Admin</a><?php endif; ?>
                <a class="btn btn-sm btn-outline-light" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="content px-3">
        <div class="hero mb-3">
            <h1><?php echo htmlspecialchars($ebookTitle); ?></h1>
            <?php if ($ebookSubtitle): ?><p class="lead"><?php echo htmlspecialchars($ebookSubtitle); ?></p><?php endif; ?>
            <div class="mt-2"><span class="badge-pill">Acesso exclusivo</span><span class="badge-pill">Somente leitura</span><span class="badge-pill">Conteúdo protegido</span></div>
        </div>

        <div class="card card-ebook">
            <div class="card-body" style="font-size:1.06rem; line-height:1.8">
                <?php echo $conteudoHtml; ?>
            </div>
        </div>
    </div>
</body>
</html>


