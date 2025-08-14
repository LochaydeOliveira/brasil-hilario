# 🎯 GUIA SIMPLES - Brasil Hilário

## 🚀 **COMO USAR (PASSO A PASSO)**

### **PASSO 1: Verificar se o PHP está funcionando**
1. **Acesse**: `https://seu-site.com/info.php`
2. **Se aparecer informações**, o PHP está funcionando
3. **Se der erro 403**, o servidor está bloqueando arquivos PHP

### **PASSO 2: Configurar o Projeto**
1. **Acesse**: `https://seu-site.com/configurar_projeto.php`
2. **Preencha os dados** (já estão preenchidos)
3. **Clique em "Salvar Configuração"**
4. **Delete o arquivo** `configurar_projeto.php` por segurança

### **PASSO 3: Testar Funcionalidades**
1. **Backup**: Acesse `/admin/backup.php`
2. **Newsletter**: Acesse `/newsletter`
3. **Site principal**: Acesse `/index.php`

---

## 🔧 **SE DER ERRO 403**

### **Problema**: Servidor bloqueando arquivos PHP
### **Soluções**:

#### **1. Verificar permissões**
```bash
chmod 644 *.php
chmod 755 includes/
chmod 755 config/
```

#### **2. Verificar .htaccess**
Crie um arquivo `.htaccess` na raiz:
```apache
Options +FollowSymLinks
RewriteEngine On

# Permitir acesso a arquivos PHP
<Files "*.php">
    Require all granted
</Files>

# Proteger arquivos sensíveis
<Files ".env">
    Require all denied
</Files>
```

#### **3. Contatar o provedor**
- Alguns provedores bloqueiam arquivos PHP por segurança
- Peça para liberar acesso aos arquivos PHP

---

## 📁 **ARQUIVOS IMPORTANTES**

### **Arquivos de Configuração**
- `config/config.php` - Configurações principais
- `includes/db.php` - Conexão com banco
- `.env` - Dados sensíveis (criado pelo configurador)

### **Arquivos de Funcionalidade**
- `includes/Logger.php` - Sistema de logs
- `includes/CacheManager.php` - Sistema de cache
- `includes/Validator.php` - Validação de dados
- `includes/session_init.php` - Sessões seguras

### **Arquivos de Interface**
- `admin/backup.php` - Sistema de backup
- `newsletter.php` - Página de newsletter
- `confirmar-newsletter.php` - Confirmação de newsletter

---

## 🎯 **TESTES SIMPLES**

### **1. Teste de PHP**
- Acesse: `info.php`
- Deve mostrar informações do PHP

### **2. Teste de Configuração**
- Acesse: `configurar_projeto.php`
- Deve criar o arquivo `.env`

### **3. Teste de Backup**
- Acesse: `admin/backup.php`
- Deve mostrar interface de backup

### **4. Teste de Newsletter**
- Acesse: `newsletter`
- Deve mostrar formulário de inscrição

---

## 🆘 **PROBLEMAS COMUNS**

### **Erro 403 (Forbidden)**
- **Causa**: Servidor bloqueando arquivos PHP
- **Solução**: Verificar permissões e .htaccess

### **Erro 500 (Internal Server Error)**
- **Causa**: Erro no código PHP
- **Solução**: Verificar logs do servidor

### **Erro de Conexão com Banco**
- **Causa**: Credenciais incorretas
- **Solução**: Verificar arquivo `.env`

### **Arquivo não encontrado**
- **Causa**: Arquivo não existe
- **Solução**: Verificar se todos os arquivos foram criados

---

## 📞 **CONTATOS ÚTEIS**

### **Arquivos de Teste**
- `info.php` - Informações do sistema
- `configurar_projeto.php` - Configurador

### **Funcionalidades**
- `admin/backup.php` - Sistema de backup
- `newsletter` - Newsletter
- `index.php` - Site principal

### **Logs**
- `logs/app.log` - Logs da aplicação
- `logs/php_errors.log` - Erros do PHP

---

## 🎉 **RESULTADO ESPERADO**

Após seguir os passos, você terá:
- ✅ PHP funcionando
- ✅ Configuração salva
- ✅ Sistema de backup ativo
- ✅ Newsletter funcionando
- ✅ Site mais rápido e seguro

**Se der erro 403, o problema é do servidor, não do código!** 