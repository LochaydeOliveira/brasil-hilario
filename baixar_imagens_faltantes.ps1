# Script para baixar imagens faltantes dos posts a partir do ID 45
$baseUrl = "https://www.brasilhilario.com.br"

# Dicionário com as imagens faltantes dos posts a partir do ID 45
$imagensPorPost = @{
    "post-45-nordeste-rio-culinaria" = @(
        "uploads/images/6880df89b9dcb.jpg"
    )
    
    "post-46-corinthians-saidas-elenco" = @(
        "uploads/images/6880a8be8ecf1.jpeg"
    )
    
    "post-47-fx4-agro-inovacao-genetica" = @(
        "uploads/images/6881078782400.jpg"
    )
    
    "post-48-roger-waters-cinemas" = @(
        "uploads/images/68811dc423825.jpg"
    )
    
    "post-49-conta-luz-tecnologia-energia-limpa" = @(
        "uploads/images/6881231caed62.jpg"
    )
    
    "post-50-navio-britanico-250-anos" = @(
        "uploads/images/68812ddbbdb01.jpg"
    )
    
    "post-51-vinhos-nova-zelandia-brasil" = @(
        "uploads/images/68822047d4e19.jpg"
    )
    
    "post-52-dancar-pecado-danca" = @(
        "uploads/images/6882e16f3058f.jpeg"
    )
    
    "post-53-neymar-santos-torcedor" = @(
        "uploads/images/6882d89a4b921.jpg"
    )
    
    "post-54-asteroide-67-metros-terra" = @(
        "uploads/images/6884009c2f008.jpeg"
    )
    
    "post-55-pesquisa-58-brasileiros-economia" = @(
        "uploads/images/68852bfe5732f.jpg"
    )
    
    "post-56-agronegocio-brasil-perder-6bi" = @(
        "uploads/images/6885964e9a25e.jpg"
    )
    
    "post-57-fortaleza-bragantino-brasileirao" = @(
        "uploads/images/6885994aae904.jpg"
    )
    
    "post-58-senadores-eua-tarifa" = @(
        "uploads/images/68859f2547b8e.jpg"
    )
    
    "post-59-palmeiras-gremio-brasileirao" = @(
        "uploads/images/6885a536007ab.jpg"
    )
    
    "post-60-homens-dancam-desejo" = @(
        "uploads/images/688635666aed7.jpg"
    )
    
    "post-61-bomba-suja-arma-radiologica" = @(
        "uploads/images/6886b12f83e6c.webp"
    )
    
    "post-62-objeto-interestelar-3i-atlas" = @(
        "uploads/images/6886b9102571f.jpg"
    )
    
    "post-63-simbolismo-ataque-igreja-gaza" = @(
        "uploads/images/688841d887902.webp"
    )
    
    "post-64-cientistas-alertam-amostras-marte" = @(
        "uploads/images/6887c0c6468b7.jpg"
    )
    
    "post-65-russia-invadir-moldavia" = @(
        "uploads/images/6888058b080c9.jpg"
    )
    
    "post-67-ossos-humanos-titanic" = @(
        "uploads/images/688941f859729.jpg"
    )
    
    "post-68-10-profissoes-psicopatas" = @(
        "uploads/images/68896ac988856.webp"
    )
    
    "post-69-roberta-miranda-chute-palco" = @(
        "uploads/images/688b7b790e6df.jpg"
    )
    
    "post-70-esporte-politica-polarizacao" = @(
        "uploads/images/688b8ee90c854.jpg"
    )
    
    "post-71-impeachment-ministro-stf" = @(
        "uploads/images/68956669b729b.jpg"
    )
    
    "post-72-adultizacao-infantil-redes-sociais" = @(
        "uploads/images/6898408d64370.jpg"
    )
    
    "post-73-algoritmos-exposicao-criancas" = @(
        "uploads/images/6898425920a8f.jpeg"
    )
    
    "post-74-cnh-sem-autoescola" = @()
}

# Função para baixar uma imagem
function Download-Image {
    param(
        [string]$Url,
        [string]$OutputPath
    )
    
    try {
        Write-Host "Baixando: $Url"
        Invoke-WebRequest -Uri $Url -OutFile $OutputPath -UseBasicParsing
        Write-Host "Baixado com sucesso: $OutputPath" -ForegroundColor Green
    }
    catch {
        Write-Host "Erro ao baixar: $Url" -ForegroundColor Red
        Write-Host "Erro: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Processar cada post
foreach ($post in $imagensPorPost.Keys) {
    $postPath = "img_posts\$post"
    
    if (Test-Path $postPath) {
        Write-Host "Processando post: $post" -ForegroundColor Yellow
        
        $imagens = $imagensPorPost[$post]
        
        if ($imagens.Count -eq 0) {
            Write-Host "Nenhuma imagem encontrada para este post" -ForegroundColor Gray
            continue
        }
        
        foreach ($imagem in $imagens) {
            $url = "$baseUrl/$imagem"
            $fileName = Split-Path $imagem -Leaf
            $outputPath = Join-Path $postPath $fileName
            
            # Verificar se o arquivo já existe
            if (Test-Path $outputPath) {
                Write-Host "Arquivo já existe: $fileName" -ForegroundColor Cyan
                continue
            }
            
            Download-Image -Url $url -OutputPath $outputPath
        }
    }
    else {
        Write-Host "Pasta nao encontrada: $postPath" -ForegroundColor Red
    }
}

Write-Host "Processo de download das imagens faltantes concluido!" -ForegroundColor Green 