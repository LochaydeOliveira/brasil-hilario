// Sistema de Anúncios Nativos
// Brasil Hilário

// Função para registrar clique em anúncio
function registrarCliqueAnuncio(anuncioId, tipoClique) {
    const postId = document.querySelector('meta[name="post-id"]')?.content || 0;
    
    fetch('/api/registrar-clique-anuncio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            anuncio_id: anuncioId,
            post_id: postId,
            tipo_clique: tipoClique
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Clique registrado com sucesso');
        }
    })
    .catch(error => {
        console.error('Erro ao registrar clique:', error);
    });
}

// Função para inicializar carrossel de anúncios
function inicializarCarrosselAnuncios() {
    const carrossel = document.querySelector('.anuncios-carrossel');
    if (!carrossel) return;
    
    let isDown = false;
    let startX;
    let scrollLeft;
    
    carrossel.addEventListener('mousedown', (e) => {
        isDown = true;
        carrossel.style.cursor = 'grabbing';
        startX = e.pageX - carrossel.offsetLeft;
        scrollLeft = carrossel.scrollLeft;
    });
    
    carrossel.addEventListener('mouseleave', () => {
        isDown = false;
        carrossel.style.cursor = 'grab';
    });
    
    carrossel.addEventListener('mouseup', () => {
        isDown = false;
        carrossel.style.cursor = 'grab';
    });
    
    carrossel.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - carrossel.offsetLeft;
        const walk = (x - startX) * 2;
        carrossel.scrollLeft = scrollLeft - walk;
    });
    
    // Adicionar indicadores de scroll
    const indicadores = document.createElement('div');
    indicadores.className = 'carrossel-indicadores';
    indicadores.innerHTML = `
        <button class="indicador-btn" onclick="scrollCarrossel('left')">‹</button>
        <button class="indicador-btn" onclick="scrollCarrossel('right')">›</button>
    `;
    carrossel.parentNode.appendChild(indicadores);
}

// Função para scroll do carrossel
function scrollCarrossel(direction) {
    const carrossel = document.querySelector('.anuncios-carrossel');
    if (!carrossel) return;
    
    const scrollAmount = 300;
    if (direction === 'left') {
        carrossel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        carrossel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Estilos CSS para anúncios
const anunciosCSS = `
<style>
.anuncio-card {
    position: relative;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    margin-bottom: 1rem;
}

.anuncio-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.anuncio-patrocinado {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    z-index: 2;
}

.anuncio-imagem {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}

.anuncio-conteudo {
    padding: 1rem;
}

.anuncio-titulo {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    line-height: 1.3;
    display: block;
    margin-bottom: 0.5rem;
}

.anuncio-titulo:hover {
    color: #007bff;
    text-decoration: underline;
}

.anuncio-cta {
    display: inline-block;
    background: #007bff;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.anuncio-cta:hover {
    background: #0056b3;
    color: #fff;
    text-decoration: none;
}

/* Indicadores do carrossel */
.carrossel-indicadores {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
}

.indicador-btn {
    background: #4285f4;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.indicador-btn:hover {
    background: #3367d6;
}

@media (max-width: 768px) {
    .carrossel-indicadores {
        display: none;
    }
}
</style>
`;

// Inserir CSS no head
document.head.insertAdjacentHTML('beforeend', anunciosCSS);

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    inicializarCarrosselAnuncios();
}); 