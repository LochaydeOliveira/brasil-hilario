# Script para baixar imagens dos posts
# Base URL do site
$baseUrl = "https://www.brasilhilario.com.br"

# Dicionário com as imagens de cada post
$imagensPorPost = @{
    "post-8-esporte-transformacao-social" = @()
    
    "post-9-7-beneficios-da-musculacao" = @(
        "uploads/images/684bcd814998f.jpg"
    )
    
    "post-10-de-icones-globais-a-referencias-de-estilo" = @(
        "uploads/images/684bd7f67c691.jpg",
        "uploads/images/684c63034763a.webp",
        "uploads/images/684c46729e7f2.webp",
        "uploads/images/684c49c9994eb.webp",
        "uploads/images/684c4ccbcdc55.webp",
        "uploads/images/684c50b155a87.webp"
    )
    
    "post-11-top-7-inovacoes-agronegocio-2025" = @(
        "uploads/images/684ccae9ea8ac.jpg",
        "uploads/images/684ccaa9dde8d.jpg",
        "uploads/images/684cd12059b77.webp",
        "uploads/images/684cd4bf79699.webp"
    )
    
    "post-12-brasil-2025-10-tendencias" = @(
        "uploads/images/684f3791689f0.webp",
        "uploads/images/684f38fe51a07.jpg",
        "uploads/images/684f3d0c998c5.webp"
    )
    
    "post-13-filmes-esperados-2025" = @(
        "uploads/images/68505074865e4.webp",
        "uploads/images/685056e3bb335.webp"
    )
    
    "post-14-obstrucao-justica" = @(
        "uploads/images/6853446e83707.png"
    )
    
    "post-15-alerta-oriente-medio" = @(
        "uploads/images/6853f5432e4ef.jpeg",
        "uploads/images/6853f55a8d175.jpg",
        "uploads/images/6853f684af9ab.jpg"
    )
    
    "post-16-tardigrados-animal-resistente" = @(
        "uploads/images/6855c7f538d8f.jpg",
        "uploads/images/6855e48b56cd7.jpg"
    )
    
    "post-17-granulos-fordyce-bolinhas" = @(
        "uploads/images/68571c82c361e.jpg"
    )
    
    "post-19-instituto-weizmann-ciencia-guerra" = @(
        "uploads/images/6858613f1806d.jpg",
        "uploads/images/68586680e33a8.jpg",
        "uploads/images/6858696218ce9.jpg"
    )
    
    "post-20-imunoterapia-cancer" = @(
        "uploads/images/6858734fb1df6.jpg",
        "uploads/images/685873782e84d.jpg"
    )
    
    "post-21-cultura-cancelamento" = @(
        "uploads/images/68599ff9d8a93.jpg",
        "uploads/images/6859fa5d247af.jpg",
        "uploads/images/6859fb2798333.jpg",
        "uploads/images/6859fb3fc3ad3.jpg"
    )
    
    "post-22-andressa-urach-olhos" = @(
        "uploads/images/685c73dc77446.jpg",
        "uploads/images/685c791e8409c.jpg"
    )
    
    "post-23-habito-protege-cerebro-memoria" = @(
        "uploads/images/687d47171ad21.jpg",
        "uploads/images/687d508a8a326.jpg"
    )
    
    "post-24-emprego-infelicidade-brasil" = @(
        "uploads/images/687d5817049d8.jpg",
        "uploads/images/687d5981ef6eb.jpg"
    )
    
    "post-25-jose-maria-marin-cbf" = @(
        "uploads/images/687d658281d01.jpg",
        "uploads/images/687d6a689b435.jpeg"
    )
    
    "post-26-preta-gil-morte" = @(
        "uploads/images/687d9ca821603.jpg"
    )
    
    "post-27-pressao-externa-politica" = @(
        "uploads/images/687e4a63e4d79.webp",
        "uploads/images/687da7dd43d23.jpg"
    )
    
    "post-28-coral-invasor-baia-todos-santos" = @(
        "uploads/images/687e519f977ee.jpeg",
        "uploads/images/687e531aa9a15.webp"
    )
    
    "post-29-diplomacia-digital-politica" = @(
        "uploads/images/687efc696b308.jpg",
        "uploads/images/687efe070cdff.jpg"
    )
    
    "post-30-vacinacao-saude-cerebral" = @(
        "uploads/images/687f04c30d6d4.jpg",
        "uploads/images/687f0598c1683.jpg"
    )
    
    "post-31-brasil-paraguai-copa-america" = @(
        "uploads/images/687f4f361c3b6.jpg",
        "uploads/images/687f4fd3ceb42.jpg"
    )
    
    "post-32-terra-gira-mais-rapido" = @(
        "uploads/images/687f580d97515.jpg"
    )
    
    "post-33-policia-civil-oruam-rj" = @(
        "uploads/images/687f5fe506f8a.jpg",
        "uploads/images/687f60a519f36.jpg",
        "uploads/images/687f6147d93ca.jpg"
    )
    
    "post-34-prisao-preventiva-medidas-cautelares" = @(
        "uploads/images/687f6c32c1670.jpg",
        "uploads/images/687f6cdb22451.jpg"
    )
    
    "post-35-sao-paulo-fria-neblina" = @(
        "uploads/images/687f772278038.jpg",
        "uploads/images/687f7856c7c3a.jpeg"
    )
    
    "post-36-conflito-acoes-judiciais-liberdade-expressao" = @(
        "uploads/images/687f7d3397793.jpeg",
        "uploads/images/687f7f380f7bc.jpg"
    )
    
    "post-37-joey-jones-liverpool-morte" = @(
        "uploads/images/687f860d35637.jpg"
    )
    
    "post-38-palestina-mortes-fome-gaza" = @(
        "uploads/images/687f95139b6d5.jpg",
        "uploads/images/687f957989911.jpg"
    )
    
    "post-39-72-brasileiros-conflitos-mundiais-economia" = @(
        "uploads/images/687faf792db94.jpg"
    )
    
    "post-40-7500-exoplanetas-nasa" = @(
        "uploads/images/687fb3f83ee7d.jpg",
        "uploads/images/687fb5ba35aa8.jpg"
    )
    
    "post-41-tubarao-boca-grande-sergipe" = @(
        "uploads/images/687fbc9bcde98.jpg",
        "uploads/images/687fbd245c7d1.jpg",
        "uploads/images/687fc157aa204.jpg"
    )
    
    "post-43-exportacoes-carne-bovina-eua" = @(
        "uploads/images/687fe84aef98e.jpg",
        "uploads/images/687fe8d5144b3.jpg"
    )
    
    "post-44-itau-banco-digital-ia" = @(
        "uploads/images/687feb842297d.jpeg",
        "uploads/images/687fec0e4cc55.jpg"
    )
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
            
            Download-Image -Url $url -OutputPath $outputPath
        }
    }
    else {
        Write-Host "Pasta nao encontrada: $postPath" -ForegroundColor Red
    }
}

Write-Host "Processo de download concluido!" -ForegroundColor Green 