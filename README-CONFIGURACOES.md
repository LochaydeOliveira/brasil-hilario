# Sistema de Configurações do Site

Este sistema permite gerenciar configurações do site de forma dinâmica através do banco de dados, oferecendo flexibilidade e facilidade de manutenção.

## 📋 Índice

- [Instalação](#instalação)
- [Estrutura do Sistema](#estrutura-do-sistema)
- [Como Usar](#como-usar)
- [Migração do Sistema Atual](#migração-do-sistema-atual)
- [Funcionalidades](#funcionalidades)
- [Troubleshooting](#troubleshooting)
- [Exemplos Avançados](#exemplos-avançados)

## 🚀 Instalação

### 1. Executar o Script SQL

Execute o arquivo `Importantes/configuracoes-iniciais.sql` no seu banco de dados para criar a tabela e inserir as configurações iniciais:

```sql
-- Execute este script no seu banco de dados
-- O arquivo está em: Importantes/configuracoes-iniciais.sql
```

### 2. Verificar Arquivos

Certifique-se de que os seguintes arquivos estão presentes:

- `includes/ConfigManager.php` - Classe para gerenciar configurações
- `includes/SiteConfig.php` - Classe para facilitar o uso das configurações
- `admin/configuracoes.php` - Interface de administração
- `includes/db.php` - Arquivo de conexão com o banco de dados

### 3. Acessar a Interface de Administração

Acesse `admin/configuracoes.php` para gerenciar as configurações através da interface web.

## 🏗️ Estrutura do Sistema

### Arquivos Principais

```
├── includes/
│   ├── ConfigManager.php      # Classe para CRUD das configurações
│   ├── SiteConfig.php         # Classe para uso fácil das configurações
│   └── db.php                 # Conexão com o banco de dados
├── admin/
│   └── configuracoes.php      # Interface de administração
├── Importantes/
│   └── configuracoes-iniciais.sql  # Script SQL inicial
└── exemplo-uso-configuracoes.php   # Exemplo de uso
```

### Tabela do Banco de Dados

A tabela `configuracoes` possui a seguinte estrutura:

```sql
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(255) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('string', 'integer', 'boolean', 'json', 'array', 'float') DEFAULT 'string',
    grupo VARCHAR(100) DEFAULT 'geral',
    descricao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 💡 Como Usar

### Uso Básico

```php
<?php
require_once 'includes/SiteConfig.php';

// Inicializar o sistema
$config = SiteConfig::getInstance();

// Obter configurações
$siteTitle = $config->getSiteTitle();
$siteDescription = $config->getSiteDescription();
$primaryColor = $config->getPrimaryColor();
?>
```

### Métodos Disponíveis

#### Configurações Gerais
- `getSiteTitle()` - Título do site
- `getSiteDescription()` - Descrição do site
- `getSiteUrl()` - URL do site
- `getAdminEmail()` - Email do administrador
- `getPostsPerPage()` - Posts por página
- `isCommentsActive()` - Se comentários estão ativos

#### Cores e Aparência
- `getPrimaryColor()` - Cor primária
- `getSecondaryColor()` - Cor secundária
- `getLogoUrl()` - URL do logo
- `getFaviconUrl()` - URL do favicon

#### SEO
- `getMetaKeywords()` - Palavras-chave
- `getOgImage()` - Imagem para redes sociais
- `getGoogleAnalyticsId()` - ID do Google Analytics

#### Redes Sociais
- `getSocialLinks()` - Links das redes sociais

#### Integração
- `getIntegrationCodes()` - Códigos de integração
- `generateHeadCodes()` - Códigos para o head
- `generateBodyCodes()` - Códigos para o body

#### Newsletter
- `isNewsletterActive()` - Se newsletter está ativa
- `getNewsletterTitle()` - Título da newsletter
- `getNewsletterDescription()` - Descrição da newsletter

### Uso Avançado

```php
// Obter configuração específica com valor padrão
$customValue = $config->get('minha_configuracao', 'valor_padrao');

// Obter todas as configurações de um grupo
$seoConfigs = $config->getGroup('seo');

// Gerar meta tags dinâmicas
echo $config->generateMetaTags('Título da Página', 'Descrição personalizada');

// Gerar CSS customizado
echo $config->generateCustomCSS();
```

## 🔄 Migração do Sistema Atual

### 1. Substituir Constantes Hardcoded

**Antes:**
```php
define('SITE_TITLE', 'Brasil Hilário');
define('SITE_DESCRIPTION', 'O melhor do humor brasileiro');
```

**Depois:**
```php
$config = SiteConfig::getInstance();
$siteTitle = $config->getSiteTitle();
$siteDescription = $config->getSiteDescription();
```

### 2. Atualizar Header

**Antes:**
```php
<title><?= SITE_TITLE ?></title>
<meta name="description" content="<?= SITE_DESCRIPTION ?>">
```

**Depois:**
```php
<?= $config->generateMetaTags() ?>
```

### 3. Atualizar Cores CSS

**Antes:**
```css
:root {
    --primary-color: #0b8103;
    --secondary-color: #b30606;
}
```

**Depois:**
```php
<style>
<?= $config->generateCustomCSS() ?>
</style>
```

## ⚙️ Funcionalidades

### Interface de Administração

- **Abas organizadas:** Geral, SEO, Redes Sociais, Integração, Páginas
- **Formulários intuitivos:** Campos específicos para cada tipo de configuração
- **Validação automática:** Verificação de tipos de dados
- **Feedback visual:** Mensagens de sucesso/erro
- **Responsivo:** Funciona em dispositivos móveis

### Tipos de Configuração Suportados

- **string:** Texto simples
- **integer:** Números inteiros
- **boolean:** Valores verdadeiro/falso
- **json:** Dados estruturados
- **array:** Arrays PHP
- **float:** Números decimais

### Grupos de Configuração

- **geral:** Configurações básicas do site
- **seo:** Otimização para motores de busca
- **redes_sociais:** Links das redes sociais
- **integracao:** Códigos de terceiros
- **paginas:** Configurações específicas de páginas

## 🔧 Troubleshooting

### Problemas Comuns

#### 1. Erro de Conexão com Banco
```
Erro: Call to undefined function mysqli_connect()
```
**Solução:** Verifique se a extensão mysqli está habilitada no PHP.

#### 2. Tabela Não Encontrada
```
Table 'database.configuracoes' doesn't exist
```
**Solução:** Execute o script SQL `configuracoes-iniciais.sql`.

#### 3. Permissões de Escrita
```
Warning: Cannot modify header information
```
**Solução:** Certifique-se de que não há saída antes dos headers.

#### 4. Configurações Não Carregam
```
Configuração retorna valor padrão
```
**Solução:** Verifique se a chave existe na tabela e se o grupo está correto.

### Logs e Debug

Para ativar logs de debug, adicione no início do arquivo:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📚 Exemplos Avançados

### 1. Configuração Dinâmica de Tema

```php
<?php
$config = SiteConfig::getInstance();
$theme = $config->get('site_theme', 'default');
$primaryColor = $config->getPrimaryColor();
$secondaryColor = $config->getSecondaryColor();

echo "<style>
:root {
    --primary-color: {$primaryColor};
    --secondary-color: {$secondaryColor};
}
.theme-{$theme} {
    /* Estilos específicos do tema */
}
</style>";
?>
```

### 2. Sistema de Cache Inteligente

```php
<?php
class ConfigCache {
    private static $cache = [];
    private static $config;
    
    public static function get($key, $default = null) {
        if (!isset(self::$config)) {
            self::$config = SiteConfig::getInstance();
        }
        
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = self::$config->get($key, $default);
        }
        
        return self::$cache[$key];
    }
    
    public static function clear() {
        self::$cache = [];
    }
}

// Uso
$siteTitle = ConfigCache::get('site_title', 'Site Padrão');
?>
```

### 3. Configurações Condicionais

```php
<?php
$config = SiteConfig::getInstance();

// Mostrar newsletter apenas se ativa
if ($config->isNewsletterActive()) {
    echo '<div class="newsletter">';
    echo '<h3>' . $config->getNewsletterTitle() . '</h3>';
    echo '<p>' . $config->getNewsletterDescription() . '</p>';
    echo '</div>';
}

// Mostrar redes sociais apenas se configuradas
$socialLinks = $config->getSocialLinks();
if (!empty($socialLinks)) {
    echo '<div class="social-links">';
    foreach ($socialLinks as $network => $url) {
        if (!empty($url)) {
            echo "<a href='{$url}' target='_blank'>{$network}</a>";
        }
    }
    echo '</div>';
}
?>
```

### 4. Integração com WordPress

```php
<?php
// Para sites que migram do WordPress
function getWordPressConfig($option, $default = '') {
    $config = SiteConfig::getInstance();
    
    $mapping = [
        'blogname' => 'site_title',
        'blogdescription' => 'site_description',
        'admin_email' => 'admin_email',
        'siteurl' => 'site_url',
        'posts_per_page' => 'posts_per_page'
    ];
    
    $key = $mapping[$option] ?? $option;
    return $config->get($key, $default);
}

// Uso
$siteName = getWordPressConfig('blogname', 'Meu Site');
?>
```

## 📞 Suporte

Para dúvidas ou problemas:

1. Verifique a seção [Troubleshooting](#troubleshooting)
2. Consulte os [Exemplos Avançados](#exemplos-avançados)
3. Verifique se todos os arquivos estão no lugar correto
4. Confirme se o banco de dados está configurado corretamente

## 🔄 Atualizações

Para atualizar o sistema:

1. Faça backup do banco de dados
2. Substitua os arquivos PHP
3. Execute novos scripts SQL se necessário
4. Teste as funcionalidades

---

**Desenvolvido para facilitar a manutenção e personalização de sites dinâmicos.** 