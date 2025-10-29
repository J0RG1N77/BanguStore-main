<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bangu Store - Produtos</title>

  <!-- Seus CSS -->
  <link rel="stylesheet" href="Styles/produtos.css" />
  <link rel="stylesheet" href="Styles/carinho.css" />
<link rel="stylesheet" href="Styles/carrossel.css">


  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
<?php
require_once __DIR__ . '/php/db.php';
$pdo = getPDO();
?>

  <header>
    <div class="logo">
      <img src="img/Bangu_escudo2.png" alt="Bangu Atlético Clube" />
    </div>
    <nav>
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="produtos.php">Produtos</a></li>
        <li><a href="sobre.html">Sobre</a></li>
        <li><a href="resultados.html">Resultados</a></li>
        <li>
<div class="cart-container">
  <button id="cart-button">
    <i class="fas fa-shopping-cart"></i>
    <span id="cart-count">0</span>
  </button>

  <div id="cart-dropdown" class="cart-dropdown hidden">
    <ul id="cart-items"></ul>
    <p><strong>Total: R$ <span id="cart-total">0,00</span></strong></p>
   <a href="carrinho.php"><button id="finalizar-compra">Finalizar Compra</button></a>
  
  </div>
</div>

        </li>
      </ul>
    </nav>
    <div class="user-options">
      <div class="account-container">
        <button id="account-button" class="account-button" aria-haspopup="true" aria-expanded="false">Entrar</button>
        <div id="account-dropdown" class="dropdown hidden" role="menu">
          <ul>
            <li id="manage-account" role="menuitem">Gerenciar Conta</li>
            <li id="logout" role="menuitem" class="hidden">Sair</li>
            <li id="login-link" role="menuitem"><a href="pag-login.html">Entrar / Cadastrar</a></li>
          </ul>
        </div>
      </div>
      <button id="theme-toggle">🌙</button>
    </div>
  </header>

  <main>
    <section class="produtos">
    

      <!-- Carrossel de promoções -->
      <div class="promo-carousel">
        <div class="promo-track">
          <div class="promo-slide">
            <img src="img/Banner promoção loja.png" alt="Promo 1" />
            <div class="promo-caption">Promoção 1 - CUPOM10</div>
          </div>
          <div class="promo-slide">
            <img src="img/Banner promoção frete grátis moderno em pink e branco.png" alt="Promo 2" />
            <div class="promo-caption">Promoção 2 - Frete grátis</div>
          </div>
        </div>
        <div class="promo-dots"></div>
      </div>

      <h2>Nossos Produtos</h2>
      <div class="grid-produtos">
  <?php
  $stmt = $pdo->query("SELECT * FROM produtos");
  $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($produtos as $produto):
    // Formata preço para moeda brasileira
    $precoFormatado = number_format($produto['preco'], 2, ',', '.');
  ?>
  <div class="produto" data-id="<?= $produto['id_produto'] ?>">
    <img src="<?= $produto['imagemURL'] ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="produto-img" id="open-modal-<?= $produto['id_produto'] ?>" style="cursor:pointer;" />
    <h3><?= htmlspecialchars($produto['nome']) ?></h3>
    <p>R$ <?= $precoFormatado ?></p>
    <button 
      class="add-to-cart"
      data-id="<?= $produto['id_produto'] ?>">
      Adicionar ao Carrinho
    </button>
  </div>

  <!-- Modal para <?= htmlspecialchars($produto['nome']) ?> -->
  <div id="modal-<?= $produto['id_produto'] ?>" class="modal-produto" style="display:none;">
    <div class="modal-content modal-flex">
      <span class="close-modal" id="close-modal-<?= $produto['id_produto'] ?>">&times;</span>
      <div class="modal-left">
        <img src="<?= $produto['imagemURL'] ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="modal-main-img" />
        <div class="modal-thumbs">
          <img src="<?= $produto['imagemURL'] ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" />
          <!-- Adicione mais imagens se houver variações -->
        </div>
      </div>
      <div class="modal-right">
        <h2 style="font-size:2em; margin-bottom:10px; color:#d32f2f;"><?= htmlspecialchars($produto['nome']) ?></h2>
        <p style="color:#d32f2f; font-size:1.5em; font-weight:bold; margin:0;">R$ <?= $precoFormatado ?> no pix</p>
        <p style="color:#aaa; margin:0 0 15px 0;">ou 2x de R$ <?= number_format($produto['preco']/2, 2, ',', '.') ?> no cartão</p>
        <div style="margin-bottom:15px;">
          <span style="font-weight:bold;">Medida do produto:</span><br>
          <div class="modal-tamanhos">
            <button class="tamanho-btn">PP</button>
            <button class="tamanho-btn">P</button>
            <button class="tamanho-btn">M</button>
            <button class="tamanho-btn">G</button>
            <button class="tamanho-btn">GG</button>
          </div>
        </div>
        <p><strong>Descrição:</strong> <?= htmlspecialchars($produto['descricao'] ?? 'Sem descrição') ?></p>
        <p><strong>Estoque:</strong> <?= $produto['estoque'] ?></p>
        <button 
          class="add-to-cart modal-add-cart"
          data-id="<?= $produto['id_produto'] ?>">
          Adicionar ao Carrinho
        </button>
        <ul class="modal-beneficios">
          <li>Compra confiável <span>👍</span></li>
          <li>Entrega rápida e eficiente <span>👍</span></li>
          <li>Produtos de alta qualidade <span>👍</span></li>
        </ul>
      </div>
    </div>
    <div class="modal-backdrop"></div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($produtos)): ?>
    <p>Nenhum produto encontrado.</p>
  <?php endif; ?>
</div>
    </section>
  </main>

  <div id="account-modal" class="modal hidden" role="dialog" aria-hidden="true">
    <div class="modal-content">
      <button id="close-account-modal" class="modal-close" aria-label="Fechar">&times;</button>
      <h2>Gerenciar Conta</h2>
      <form id="account-form">
        <label>Nome completo
          <input id="modal-nome" name="nome" type="text" />
        </label>
        <label>LOGIN
          <input id="modal-login" name="login" type="text" />
        </label>
        <label>Celular
          <input id="modal-celular" name="celular" type="text" />
        </label>
        <label>Email
          <input id="modal-email" name="email" type="email" />
        </label>
        <div class="modal-actions">
          <button type="button" id="cancel-account">Cancelar</button>
          <button type="submit" id="save-account">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; 2024 Bangu Store. Todos os direitos reservados.</p>
    <img src="img/logo-1701858752-1719005234-37cf381cc5c27eb83f33c1b2094320a91719005235-320-0.webp" alt="Bangu Atlético Clube" width="80" height="auto" />
    <div class="contato">
      <p>Estamos à disposição para atender você!</p>
      <p><a href="https://wa.me/+5521989496516?text=MENSAGEM" target="_blank">21989496516</a></p>
      <p><a href="mailto:bangustore7@gmail.com">bangustore7@gmail.com</a></p>
    </div>
  </footer>

  <script src="JavaScript/script.js"></script> <!-- Seu JS -->
  <script src="JavaScript/carrossel.js" defer></script> <!-- script do carrossel -->
  <script>
    // Função para inicializar modais de produtos
    function initProductModal(prodId) {
      const openBtn = document.getElementById('open-modal-' + prodId);
      const modal = document.getElementById('modal-' + prodId);
      const closeBtn = document.getElementById('close-modal-' + prodId);
      if(openBtn && modal && closeBtn) {
        openBtn.addEventListener('click', function() {
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        });
        closeBtn.addEventListener('click', function() {
          modal.style.display = 'none';
          document.body.style.overflow = '';
        });
        modal.addEventListener('click', function(e) {
          if(e.target.classList.contains('modal-produto') || e.target.classList.contains('modal-backdrop')) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
          }
        });
        // Troca imagem principal ao clicar na thumb
        const mainImg = modal.querySelector('.modal-main-img');
        const thumbs = modal.querySelectorAll('.modal-thumbs img');
        thumbs.forEach(thumb => {
          thumb.addEventListener('click', function() {
            mainImg.src = this.src;
          });
        });
        // Seleção de tamanho
        const tamanhoBtns = modal.querySelectorAll('.tamanho-btn');
        tamanhoBtns.forEach(btn => {
          btn.addEventListener('click', function() {
            tamanhoBtns.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
          });
        });
        // Adicionar ao carrinho e fechar modal
        const addCartBtn = modal.querySelector('.modal-add-cart');
        if(addCartBtn) {
          addCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addCartBtn.classList.add('clicked');
            addCartBtn.disabled = true;
            // Simula clique no botão de produto da lista (para reaproveitar lógica existente)
            const gridBtn = document.querySelector('.produto[data-id="'+prodId+'"] .add-to-cart');
            if(gridBtn) gridBtn.click();
            setTimeout(() => {
              modal.style.display = 'none';
              document.body.style.overflow = '';
              addCartBtn.classList.remove('clicked');
              addCartBtn.disabled = false;
            }, 200);
          });
        }
      }
    }
    // Inicializa todos os modais de produtos
    [1,2,3,4,5,6].forEach(initProductModal);
  </script>
  <style>
    .modal-produto {
      position: fixed;
      z-index: 1000;
      left: 0; top: 0; width: 100vw; height: 100vh;
      display: flex; align-items: center; justify-content: center;
      background: rgba(0,0,0,0.95);
    }
    .modal-content {
      background: transparent;
      box-shadow: none;
      border-radius: 0;
      padding: 0;
      max-width: 1100px;
      width: 98vw;
      min-height: 400px;
      display: block;
      position: relative;
      animation: modalShow 0.2s;
    }
    .modal-flex {
      display: flex;
      gap: 40px;
      background: #181818;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      padding: 40px 30px 30px 30px;
      align-items: flex-start;
      justify-content: center;
    }
    .modal-left {
      flex: 1.2;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }
    .modal-main-img {
      width: 350px;
      max-width: 90vw;
      border-radius: 24px;
      object-fit: cover;
      margin-bottom: 10px;
    }
    .modal-thumbs {
      display: flex;
      gap: 16px;
    }
    .modal-thumbs img {
      width: 110px;
      height: 110px;
      object-fit: cover;
      border-radius: 18px;
      cursor: pointer;
      border: 2px solid #fff;
      transition: border 0.2s;
    }
    .modal-thumbs img:hover {
      border: 2px solid #d32f2f;
    }
    .modal-right {
      flex: 1.5;
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: flex-start;
      min-width: 300px;
      max-width: 500px;
    }
    .modal-tamanhos {
      display: flex;
      gap: 10px;
      margin: 10px 0 20px 0;
    }
    .tamanho-btn {
      background: #fff;
      color: #222;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      font-size: 1.1em;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .tamanho-btn.selected, .tamanho-btn:hover {
      background: #d32f2f;
      color: #fff;
    }
    .modal-add-cart {
      width: 100%;
      margin: 18px 0 10px 0;
      padding: 16px 0;
      font-size: 1.2em;
      font-weight: bold;
      background: linear-gradient(90deg, #d32f2f 0%, #3a3aff 100%);
      color: #fff;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0,0,0,0.12);
      transition: background 0.2s;
    }
    .modal-add-cart:hover {
      background: linear-gradient(90deg, #3a3aff 0%, #d32f2f 100%);
    }
    .modal-beneficios {
      margin-top: 18px;
      color: #fff;
      font-size: 1.1em;
      list-style: none;
      padding: 0;
    }
    .modal-beneficios li {
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .close-modal {
      position: absolute;
      top: 18px; right: 30px;
      font-size: 2.2em;
      color: #fff;
      cursor: pointer;
      font-weight: bold;
      z-index: 10;
      background: rgba(0,0,0,0.2);
      border-radius: 50%;
      width: 44px; height: 44px;
      display: flex; align-items: center; justify-content: center;
      transition: background 0.2s;
    }
    .close-modal:hover {
      background: #d32f2f;
      color: #fff;
    }
    .modal-backdrop {
      display: none;
    }
    @media (max-width: 900px) {
      .modal-flex { flex-direction: column; align-items: center; gap: 20px; padding: 20px 5vw; }
      .modal-main-img { width: 90vw; max-width: 400px; }
      .modal-right { max-width: 100vw; }
    }
    @media (max-width: 600px) {
      .modal-main-img { width: 90vw; max-width: 98vw; }
      .modal-thumbs img { width: 80px; height: 80px; }
      .modal-flex { padding: 10px 2vw; }
    }

    .hidden{display:none!important}
.account-container{position:relative}
.account-button{background:#fff;border:1px solid #ddd;padding:8px 12px;border-radius:8px;display:inline-flex;align-items:center;gap:8px;cursor:pointer;color:#111;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,0.06);transition:background .12s, box-shadow .12s, transform .06s}
.account-button:hover{background:#f7f7f7}
.account-button:active{transform:translateY(1px)}
.account-button:focus{outline:2px solid rgba(0,123,255,0.12)}
.dropdown{position:absolute;right:0;top:calc(100% + 8px);min-width:180px;background:#fff;border:1px solid #ddd;border-radius:8px;padding:6px;box-shadow:0 10px 30px rgba(0,0,0,0.08);z-index:40}
.dropdown ul{list-style:none;margin:0;padding:0}
.dropdown li{padding:10px 14px;cursor:pointer;color:#000;border-radius:6px}
.dropdown li a{color:inherit;text-decoration:none;display:block}
.dropdown li:hover{background:#f5f5f5}
.modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);z-index:50}
.modal .modal-content{background:#fff;padding:18px;border-radius:8px;max-width:520px;width:90%;box-shadow:0 10px 30px rgba(0,0,0,0.2);position:relative}
.modal .modal-content label{display:block;margin-bottom:8px;font-size:14px}
.modal .modal-content input{width:100%;padding:8px;margin-top:4px;border:1px solid #ccc;border-radius:6px}
.modal .modal-content .modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
.modal .modal-close{position:absolute;right:8px;top:6px;border:none;background:transparent;font-size:20px;cursor:pointer}
  </style>
  <script>
document.addEventListener('DOMContentLoaded', function(){
(function(){
  const accountButton = document.getElementById('account-button');
  const accountDropdown = document.getElementById('account-dropdown');
  const manageAccount = document.getElementById('manage-account');
  const logoutItem = document.getElementById('logout');
  const loginLink = document.getElementById('login-link');
  const accountModal = document.getElementById('account-modal');
  const closeModal = document.getElementById('close-account-modal');
  const cancelAccount = document.getElementById('cancel-account');
  const accountForm = document.getElementById('account-form');

  // Sync server session -> localStorage
  (function syncCurrentUser(){
    fetch('php/current_user.php', { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        if (data && data.logged && data.user) {
          const u = { nomeCompleto: data.user.nome_completo || '', login: data.user.login || '', celular: data.user.celular || '', email: data.user.email || '' };
          localStorage.setItem('usuario', JSON.stringify(u));
          const loggedKey = data.user.login || (data.user.nome_completo && data.user.nome_completo.split(' ')[0]) || '';
          if (loggedKey) localStorage.setItem('loggedUser', loggedKey);
          else localStorage.removeItem('loggedUser');
        } else {
          localStorage.removeItem('loggedUser');
        }
      })
      .catch(()=>{ localStorage.removeItem('loggedUser'); });
  })();

  function getStoredUser(){
    try{
      const user = JSON.parse(localStorage.getItem('usuario')) || null;
      const logged = localStorage.getItem('loggedUser');
      if(!user) return null;
      if(logged && (logged === user.login || logged === (user.nomeCompleto && user.nomeCompleto.split(' ')[0]) || logged === user.nomeCompleto)){
        return user;
      }
      return null;
    }catch(e){return null}
  }

  function setStoredUser(u){ localStorage.setItem('usuario', JSON.stringify(u)); }

  function updateUIForUser(){
    const user = getStoredUser();
    if(user){
      accountButton.textContent = user.nomeCompleto ? user.nomeCompleto.split(' ')[0] : user.login || 'Usuário';
      logoutItem.classList.remove('hidden');
      loginLink.classList.add('hidden');
    } else {
      if(accountButton) accountButton.textContent = 'Entrar';
      if(logoutItem) logoutItem.classList.add('hidden');
      if(loginLink) loginLink.classList.remove('hidden');
    }
  }

  if(accountButton){
    accountButton.addEventListener('click', ()=>{
      accountDropdown.classList.toggle('hidden');
      accountButton.setAttribute('aria-expanded', String(!accountDropdown.classList.contains('hidden')));
    });
  }

  document.addEventListener('click',(e)=>{
    if(!e.target.closest('.account-container') && !e.target.closest('#account-dropdown')){
      if(accountDropdown && !accountDropdown.classList.contains('hidden')) accountDropdown.classList.add('hidden');
    }
  });

  if(manageAccount){
    manageAccount.addEventListener('click', ()=>{
      const user = getStoredUser();
      if(!user){ window.location.href = 'pag-login.html'; return; }
      document.getElementById('modal-nome').value = user.nomeCompleto || '';
      console.log('Tentando preencher modal-login:', document.getElementById('modal-login'));
      console.log('Valor de user.login:', user.login);
      document.getElementById('modal-login').value = user.login || '';
      console.log('Tentando preencher modal-celular:', document.getElementById('modal-celular'));
      console.log('Valor de user.celular:', user.celular);
      document.getElementById('modal-celular').value = user.celular || '';
      document.getElementById('modal-email').value = user.email || '';
      accountModal.classList.remove('hidden');
      accountDropdown.classList.add('hidden');
    });
  }

  if(closeModal) closeModal.addEventListener('click', ()=> accountModal.classList.add('hidden'));
  if(cancelAccount) cancelAccount.addEventListener('click', ()=> accountModal.classList.add('hidden'));

  if(accountForm) accountForm.addEventListener('submit', (e)=>{
    e.preventDefault();
    const updated = {
      nomeCompleto: document.getElementById('modal-nome').value.trim(),
      login: document.getElementById('modal-login').value.trim(),
      celular: document.getElementById('modal-celular').value.trim(),
      email: document.getElementById('modal-email').value.trim()
    };
    setStoredUser(updated);
    updateUIForUser();

    fetch('php/update_user.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(updated)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Dados atualizados com sucesso.');
      } else {
        alert('Erro ao atualizar dados: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Erro:', error);
      alert('Erro de comunicação com o servidor.');
    });
    accountModal.classList.add('hidden');
  });

  if(logoutItem) logoutItem.addEventListener('click', ()=>{
    fetch('php/logout.php', { method: 'GET', credentials: 'same-origin' })
      .finally(()=>{
        localStorage.removeItem('loggedUser');
        localStorage.removeItem('usuario');
        try { localStorage.setItem('loggedOut', String(Date.now())); } catch(e){}
        if(accountDropdown) accountDropdown.classList.add('hidden');
        alert('Você saiu da conta.');
      });
  });

  // listen for logout/login across tabs
  window.addEventListener('storage', function(e){ if(!e) return; if(e.key === 'loggedOut' || e.key === 'loggedUser' || e.key === 'usuario') { updateUIForUser(); } });

  updateUIForUser();
})();
});
  </script>
    <div id="login-modal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 10px; text-align: center; position: relative;">
            <span class="close-button" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; position: absolute; right: 10px; top: 5px;">&times;</span>
            <p style="font-size: 1.2em; margin-top: 20px;">Você precisa estar logado para adicionar produtos ao carrinho</p>
            <button id="login-modal-button" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; margin-top: 20px;">Fazer Login</button>
        </div>
    </div>
</body>
</html>
