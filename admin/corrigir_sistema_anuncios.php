<?php
/**
 * CORREÇÃO AUTOMÁTICA DO SISTEMA DE ANÚNCIOS
 * Brasil Hilário - Correção de problemas identificados
 */

// Configurações
define('DEBUG_MODE', true);

// Incluir configurações
require_once '../config/config.php';
require_once '../config/database_unified.php';

// Verificar se o usuário está logado como admin
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    die("Acesso negado. Apenas administradores podem executar este script.");
}

// Função para log
function logMessage($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] [$type] $message\n";
    
    if (DEBUG_MODE) {
        echo $log;
    }
    
    error_log($log);
}

// Início da correção
logMessage("🔧 INICIANDO CORREÇÃO AUTOMÁTICA DO SISTEMA DE ANÚNCIOS", 'info');
logMessage("=========================================================", 'info');

$dbManager = DatabaseManager::getInstance();

// 1. VERIFICAR E CORRIGIR ANÚNCIOS SEM GRUPO
logMessage("📋 1. VERIFICANDO ANÚNCIOS SEM GRUPO", 'info');

try {
    $anuncios_sem_grupo = $dbManager->query("
        SELECT a.id, a.titulo, a.ativo
        FROM anuncios a
        LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
        WHERE gi.anuncio_id IS NULL AND a.ativo = 1
    ");
    
    if (!empty($anuncios_sem_grupo)) {
        logMessage("⚠️ Encontrados " . count($anuncios_sem_grupo) . " anúncios ativos sem grupo", 'warning');
        
        // Verificar se existe grupo padrão para sidebar
        $grupo_padrao = $dbManager->queryOne("
            SELECT id, nome FROM grupos_anuncios 
            WHERE localizacao = 'sidebar' AND ativo = 1 AND aparecer_inicio = 1
            LIMIT 1
        ");
        
        if ($grupo_padrao) {
            logMessage("✅ Usando grupo padrão: {$grupo_padrao['nome']}", 'success');
            
            foreach ($anuncios_sem_grupo as $anuncio) {
                // Pegar próxima ordem disponível
                $ultima_ordem = $dbManager->queryOne("
                    SELECT MAX(ordem) as max_ordem 
                    FROM grupos_anuncios_items 
                    WHERE grupo_id = ?
                ", [$grupo_padrao['id']]);
                
                $nova_ordem = ($ultima_ordem['max_ordem'] ?? -1) + 1;
                
                $resultado = $dbManager->execute("
                    INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) 
                    VALUES (?, ?, ?)
                ", [$grupo_padrao['id'], $anuncio['id'], $nova_ordem]);
                
                if ($resultado) {
                    logMessage("✅ Anúncio '{$anuncio['titulo']}' associado ao grupo padrão", 'success');
                } else {
                    logMessage("❌ Erro ao associar anúncio '{$anuncio['titulo']}'", 'error');
                }
            }
        } else {
            logMessage("⚠️ Nenhum grupo padrão encontrado. Criando grupo automático...", 'warning');
            
            // Criar grupo padrão
            $grupo_id = $dbManager->execute("
                INSERT INTO grupos_anuncios (nome, localizacao, layout, marca, ativo, posts_especificos, aparecer_inicio, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ", ['Anúncios Padrão', 'sidebar', 'carrossel', '', 1, 0, 1]);
            
            if ($grupo_id) {
                $grupo_id = $dbManager->lastInsertId();
                logMessage("✅ Grupo padrão criado com ID: $grupo_id", 'success');
                
                // Associar anúncios ao novo grupo
                foreach ($anuncios_sem_grupo as $ordem => $anuncio) {
                    $resultado = $dbManager->execute("
                        INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) 
                        VALUES (?, ?, ?)
                    ", [$grupo_id, $anuncio['id'], $ordem]);
                    
                    if ($resultado) {
                        logMessage("✅ Anúncio '{$anuncio['titulo']}' associado ao novo grupo", 'success');
                    }
                }
            }
        }
    } else {
        logMessage("✅ Todos os anúncios ativos estão em grupos", 'success');
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao corrigir anúncios sem grupo: " . $e->getMessage(), 'error');
}

// 2. VERIFICAR E CORRIGIR GRUPOS SEM ANÚNCIOS
logMessage("📋 2. VERIFICANDO GRUPOS SEM ANÚNCIOS", 'info');

try {
    $grupos_sem_anuncios = $dbManager->query("
        SELECT g.id, g.nome, g.localizacao, g.ativo
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
        WHERE gi.anuncio_id IS NULL AND g.ativo = 1
    ");
    
    if (!empty($grupos_sem_anuncios)) {
        logMessage("⚠️ Encontrados " . count($grupos_sem_anuncios) . " grupos ativos sem anúncios", 'warning');
        
        foreach ($grupos_sem_anuncios as $grupo) {
            logMessage("   - Grupo '{$grupo['nome']}' ({$grupo['localizacao']})", 'warning');
            
            // Verificar se há anúncios disponíveis
            $anuncios_disponiveis = $dbManager->query("
                SELECT a.id, a.titulo
                FROM anuncios a
                LEFT JOIN grupos_anuncios_items gi ON a.id = gi.anuncio_id
                WHERE gi.anuncio_id IS NULL AND a.ativo = 1
                LIMIT 3
            ");
            
            if (!empty($anuncios_disponiveis)) {
                foreach ($anuncios_disponiveis as $ordem => $anuncio) {
                    $resultado = $dbManager->execute("
                        INSERT INTO grupos_anuncios_items (grupo_id, anuncio_id, ordem) 
                        VALUES (?, ?, ?)
                    ", [$grupo['id'], $anuncio['id'], $ordem]);
                    
                    if ($resultado) {
                        logMessage("✅ Anúncio '{$anuncio['titulo']}' adicionado ao grupo '{$grupo['nome']}'", 'success');
                    }
                }
            } else {
                logMessage("⚠️ Nenhum anúncio disponível para o grupo '{$grupo['nome']}'", 'warning');
            }
        }
    } else {
        logMessage("✅ Todos os grupos ativos têm anúncios", 'success');
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao corrigir grupos sem anúncios: " . $e->getMessage(), 'error');
}

// 3. VERIFICAR E CORRIGIR CONFIGURAÇÕES DE GRUPOS
logMessage("📋 3. VERIFICANDO CONFIGURAÇÕES DE GRUPOS", 'info');

try {
    // Grupos de sidebar que não aparecem na página inicial
    $grupos_sidebar_sem_inicio = $dbManager->query("
        SELECT id, nome, aparecer_inicio
        FROM grupos_anuncios 
        WHERE localizacao = 'sidebar' AND ativo = 1 AND aparecer_inicio = 0
    ");
    
    if (!empty($grupos_sidebar_sem_inicio)) {
        logMessage("⚠️ Encontrados " . count($grupos_sidebar_sem_inicio) . " grupos de sidebar que não aparecem na página inicial", 'warning');
        
        foreach ($grupos_sidebar_sem_inicio as $grupo) {
            logMessage("   - Grupo '{$grupo['nome']}' (aparecer_inicio: {$grupo['aparecer_inicio']})", 'warning');
            
            // Perguntar se deve corrigir (em produção, poderia ser automático)
            logMessage("   → Configurando para aparecer na página inicial...", 'info');
            
            $resultado = $dbManager->execute("
                UPDATE grupos_anuncios 
                SET aparecer_inicio = 1 
                WHERE id = ?
            ", [$grupo['id']]);
            
            if ($resultado) {
                logMessage("✅ Grupo '{$grupo['nome']}' configurado para aparecer na página inicial", 'success');
            }
        }
    } else {
        logMessage("✅ Todos os grupos de sidebar estão configurados corretamente", 'success');
    }
    
    // Grupos com posts específicos mas sem posts associados
    $grupos_posts_sem_associacao = $dbManager->query("
        SELECT g.id, g.nome, g.posts_especificos,
               COUNT(gap.post_id) as posts_associados
        FROM grupos_anuncios g
        LEFT JOIN grupos_anuncios_posts gap ON g.id = gap.grupo_id
        WHERE g.ativo = 1 AND g.posts_especificos = 1
        GROUP BY g.id
        HAVING posts_associados = 0
    ");
    
    if (!empty($grupos_posts_sem_associacao)) {
        logMessage("⚠️ Encontrados " . count($grupos_posts_sem_associacao) . " grupos com posts específicos mas sem posts associados", 'warning');
        
        foreach ($grupos_posts_sem_associacao as $grupo) {
            logMessage("   - Grupo '{$grupo['nome']}' (posts associados: {$grupo['posts_associados']})", 'warning');
            
            // Desativar posts específicos para este grupo
            $resultado = $dbManager->execute("
                UPDATE grupos_anuncios 
                SET posts_especificos = 0 
                WHERE id = ?
            ", [$grupo['id']]);
            
            if ($resultado) {
                logMessage("✅ Grupo '{$grupo['nome']}' configurado para aparecer em todos os posts", 'success');
            }
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao corrigir configurações: " . $e->getMessage(), 'error');
}

// 4. VERIFICAR ESTRUTURA DO BANCO
logMessage("📋 4. VERIFICANDO ESTRUTURA DO BANCO", 'info');

try {
    // Verificar se a coluna 'ativo' existe na tabela anuncios
    $colunas_anuncios = $dbManager->query("SHOW COLUMNS FROM anuncios");
    $colunas_anuncios_nomes = array_column($colunas_anuncios, 'Field');
    
    if (!in_array('ativo', $colunas_anuncios_nomes)) {
        logMessage("⚠️ Coluna 'ativo' não existe na tabela anuncios. Criando...", 'warning');
        
        $resultado = $dbManager->execute("
            ALTER TABLE anuncios 
            ADD COLUMN ativo TINYINT(1) DEFAULT 1 
            AFTER link_compra
        ");
        
        if ($resultado) {
            logMessage("✅ Coluna 'ativo' criada na tabela anuncios", 'success');
        }
    } else {
        logMessage("✅ Coluna 'ativo' existe na tabela anuncios", 'success');
    }
    
    // Verificar se a coluna 'aparecer_inicio' existe na tabela grupos_anuncios
    $colunas_grupos = $dbManager->query("SHOW COLUMNS FROM grupos_anuncios");
    $colunas_grupos_nomes = array_column($colunas_grupos, 'Field');
    
    if (!in_array('aparecer_inicio', $colunas_grupos_nomes)) {
        logMessage("⚠️ Coluna 'aparecer_inicio' não existe na tabela grupos_anuncios. Criando...", 'warning');
        
        $resultado = $dbManager->execute("
            ALTER TABLE grupos_anuncios 
            ADD COLUMN aparecer_inicio TINYINT(1) DEFAULT 1 
            AFTER posts_especificos
        ");
        
        if ($resultado) {
            logMessage("✅ Coluna 'aparecer_inicio' criada na tabela grupos_anuncios", 'success');
        }
    } else {
        logMessage("✅ Coluna 'aparecer_inicio' existe na tabela grupos_anuncios", 'success');
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao verificar estrutura: " . $e->getMessage(), 'error');
}

// 5. TESTE FINAL
logMessage("📋 5. TESTE FINAL", 'info');

try {
    require_once '../includes/GruposAnunciosManager.php';
    $gruposManager = new GruposAnunciosManager($dbManager->getConnection());
    
    // Testar busca de grupos para sidebar (página inicial)
    $grupos_sidebar = $gruposManager->getGruposPorLocalizacao('sidebar', null, true);
    logMessage("📊 Grupos encontrados para sidebar (página inicial): " . count($grupos_sidebar), 'info');
    
    if (empty($grupos_sidebar)) {
        logMessage("⚠️ PROBLEMA: Ainda não há grupos para sidebar na página inicial", 'warning');
    } else {
        foreach ($grupos_sidebar as $grupo) {
            logMessage("✅ Grupo '{$grupo['nome']}' configurado corretamente", 'success');
            
            $anuncios_grupo = $gruposManager->getAnunciosDoGrupo($grupo['id']);
            logMessage("   Anúncios no grupo: " . count($anuncios_grupo), 'info');
            
            foreach ($anuncios_grupo as $anuncio) {
                logMessage("     - {$anuncio['titulo']}", 'info');
            }
        }
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro no teste final: " . $e->getMessage(), 'error');
}

// 6. RESUMO FINAL
logMessage("📊 RESUMO DA CORREÇÃO", 'info');
logMessage("====================", 'info');

try {
    $stats_finais = [
        'anuncios_ativos' => $dbManager->queryOne("SELECT COUNT(*) as total FROM anuncios WHERE ativo = 1")['total'],
        'grupos_ativos' => $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios WHERE ativo = 1")['total'],
        'associacoes' => $dbManager->queryOne("SELECT COUNT(*) as total FROM grupos_anuncios_items")['total'],
        'grupos_sidebar_inicio' => $dbManager->queryOne("
            SELECT COUNT(*) as total 
            FROM grupos_anuncios g
            LEFT JOIN grupos_anuncios_items gi ON g.id = gi.grupo_id
            WHERE g.localizacao = 'sidebar' AND g.ativo = 1 AND g.aparecer_inicio = 1
            GROUP BY g.id
            HAVING COUNT(gi.anuncio_id) > 0
        ")['total'] ?? 0
    ];
    
    logMessage("📈 Estatísticas Finais:", 'info');
    logMessage("   - Anúncios ativos: {$stats_finais['anuncios_ativos']}", 'info');
    logMessage("   - Grupos ativos: {$stats_finais['grupos_ativos']}", 'info');
    logMessage("   - Associações: {$stats_finais['associacoes']}", 'info');
    logMessage("   - Grupos sidebar (página inicial): {$stats_finais['grupos_sidebar_inicio']}", 'info');
    
    if ($stats_finais['grupos_sidebar_inicio'] > 0) {
        logMessage("✅ SISTEMA CORRIGIDO! Anúncios devem aparecer na sidebar", 'success');
    } else {
        logMessage("⚠️ Ainda há problemas. Verifique as configurações manualmente", 'warning');
    }
    
} catch (Exception $e) {
    logMessage("❌ Erro ao gerar resumo: " . $e->getMessage(), 'error');
}

logMessage("🎉 CORREÇÃO CONCLUÍDA!", 'success');

echo "<h2>✅ Correção Automática Concluída!</h2>";
echo "<p>Verifique os logs acima para detalhes das correções aplicadas.</p>";
echo "<p><a href='../index.php' target='_blank'>← Testar no Site</a></p>";
echo "<p><a href='anuncios.php'>← Voltar para Anúncios</a></p>";
echo "<p><a href='grupos-anuncios.php'>← Voltar para Grupos</a></p>";
?>
