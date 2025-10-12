<?php
session_start();
require_once 'php/db.php';

$isLoggedIn = isset($_SESSION['user_id']);

// Redireciona se não estiver logado
if (!$isLoggedIn) {
    header('Location: pag-login.html'); // Ou para uma página de aviso
    exit();
}

?><!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bangu Store - Carrinho</title>

  <!-- Seus CSS -->
  <link rel="stylesheet" href="Styles/produtos.css" />
  <link rel="stylesheet" href="Styles/carinho.css" />
  <link rel="stylesheet" href="Styles/carrossel.css">
  <link rel="stylesheet" href="Styles/finalizarcompra.css">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <!-- Account/menu styles (copiado de index.html para garantir consistência) -->
  <style>
    .hidden{display:none!important}
    .account-container{position:relative}
    /* Estilo do botão do menu de conta */
    .account-button{
      background:#fff;
      border:1px solid #ddd;
      padding:8px 12px;
      border-radius:8px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      cursor:pointer;
      color:#111;
      font-weight:600;
      box-shadow:0 2px 6px rgba(0,0,0,0.06);
      transition:background .12s, box-shadow .12s, transform .06s;
    }
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
</head>
<body>

  <!-- =========================
       Cabeçalho
       ========================= -->
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
              <p><strong>Total: R$ <span id="cart-total">0.00</span></strong></p>
            </div>
          </div>
        </li>
      </ul>
    </nav>
    <div class="user-options">
      <a href="pag-login.html"><i class="fas fa-user"></i></a>
      <button id="theme-toggle">🌙</button>
    </div>
  </header>

  <!-- =========================
       Carrinho
       ========================= -->
  <main class="carrinho-container">
    <div class="carrinho-header">
      <h1 class="carrinho-title">Meu Carrinho</h1>        
    </div>    

    <!-- Etapas -->
    <div class="steps">
      <!-- Etapa Loja (ativa) -->
      <div class="step active">
        <div class="icon">🏬</div>
        <span>Loja</span>
      </div>

      <div class="line"></div>

      <!-- Etapa Casa -->
      <div class="step">
        <div class="icon">🏠</div>
        <span>Sua Casa</span>
      </div>
    </div>

    <!-- Conteúdo do carrinho -->
    <div class="carrinho-content">
      <div class="carrinho-items" id="carrinho-itens-js">
        <!-- Itens reais do carrinho aparecerão aqui via JS -->
      </div>      

      <div class="carrinho-summary">
        <h3 class="summary-title">Resumo do Pedido</h3>

        <!-- Campos de cupom e CEP -->
        <div style="display:flex;gap:32px;flex-wrap:wrap;margin-bottom:20px;">
          <div>
            <label for="campo-cep" style="font-weight:bold;">Calcule o Frete:</label>
            <div style="display:flex;gap:8px;">
              <input type="text" id="campo-cep" placeholder="Digite seu CEP" style="width:120px;">
              <button type="button" id="calcular-frete">Calcular</button>
              <button type="button" id="limpar-frete">Limpar</button>
            </div>
            <span id="cep-msg" style="color:#009688;font-size:13px;display:block;margin-top:6px;"></span>
          </div>
        </div>

        <div class="summary-row">
          <span class="summary-label">Subtotal (<span id="qtd-itens">0</span> itens)</span>
          <span class="summary-value" id="subtotal-carrinho">R$ 0,00</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Frete</span>
          <span class="summary-value" id="frete-carrinho">R$ 0,00</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Descontos</span>
          <span class="summary-value" id="desconto-carrinho">R$ 0,00</span>
        </div>
        <div class="summary-row total-row">
          <span>Total</span>
          <span id="total-carrinho">R$ 0,00</span>
        </div>

        <button class="checkout-btn" id="finalizar-compra">Comprar</button>
        <a href="produtos.php" class="continue-shopping">Continuar comprando</a>

        <!-- Campo de cupom -->
        <div class="campo-cupom">
          <label for="campo-cupom" style="font-weight:bold;">Cupom:</label>
          <div style="display:flex;gap:8px;">
            <input type="text" id="campo-cupom" placeholder="Digite o cupom" style="width:120px;">
            <button type="button" id="aplicar-cupom">Aplicar</button>
            <span id="cupom-msg" style="color:#009688;font-size:13px;"></span>
            <button type="button" id="remover-cupom" style="display:none;margin-left:8px;">Remover</button>
          </div>
        </div>
      </div>
    </div>
  </main>



  <!-- =========================
       Footer
       ========================= -->
  <footer>
    <p>&copy; 2024 Bangu Store. Todos os direitos reservados.</p>
    <img src="img/logo-1701858752-1719005234-37cf381cc5c27eb83f33c1b2094320a91719005235-320-0.webp" alt="Bangu Atlético Clube" width="80" height="auto" />
    <div class="contato">
      <p>Estamos à disposição para atender você!</p>
      <p><a href="https://wa.me/+5521989496516?text=MENSAGEM" target="_blank">21989496516</a></p>
      <p><a href="mailto:bangustore7@gmail.com">bangustore7@gmail.com</a></p>
    </div>
  </footer>

  <!-- =========================
       Scripts
       ========================= -->
  <!-- =========================
       Scripts
       ========================= -->
  

  <script src="JavaScript/script.js"></script>
  <script src="JavaScript/finalizarcompra.js"></script>

  <script>
    (function(){
      const openBtn = document.getElementById('finalizar-compra');
      const modal = document.getElementById('checkout-modal');
      const cancelBtn = document.getElementById('checkout-cancel');
      const closeBtn = document.getElementById('checkout-close');
      const cartInput = document.getElementById('checkout-cart-data');
      const form = document.getElementById('checkout-form');
      const summaryEl = document.getElementById('checkout-summary');

      const cepInput = document.getElementById('campo-cep');
      const calcBtn = document.getElementById('calcular-frete');
      const clearBtn = document.getElementById('limpar-frete');
      const cepMsg = document.getElementById('cep-msg');
      const freteEl = document.getElementById('frete-carrinho');
      const subtotalEl = document.getElementById('subtotal-carrinho');
      const descontoEl = document.getElementById('desconto-carrinho');
      const totalEl = document.getElementById('total-carrinho');
      const qtdItensEl = document.getElementById('qtd-itens');

      function getCartData() {
        // Tenta ler do localStorage (chave comum 'cart' ou 'carrinho')
        let cart = [];
        try {
          cart = JSON.parse(localStorage.getItem('cart') || localStorage.getItem('carrinho') || '[]');
        } catch (e) { cart = []; }

        // Se estiver vazio, tenta extrair do DOM (itens renderizados)
        if (!Array.isArray(cart) || cart.length === 0) {
          const items = [];
          document.querySelectorAll('#carrinho-itens-js .carrinho-item').forEach(el => {
            const name = el.querySelector('.item-nome')?.textContent?.trim() || el.dataset.name || 'Produto';
            const priceText = el.querySelector('.item-preco')?.textContent?.replace(/[^0-9,\.]/g,'') || '0';
            const price = parseFloat(priceText.replace(',','.')) || 0;
            const qty = parseInt(el.querySelector('.item-quantidade')?.value || el.dataset.qty || '1', 10) || 1;
            items.push({ name, qty, price });
          });
          cart = items;
        }
        return cart;
      }

      function renderSummary(cart) {
        if (!Array.isArray(cart) || cart.length === 0) {
          summaryEl.textContent = 'Carrinho vazio';
          subtotalEl.textContent = formatCurrencyBR(0);
          qtdItensEl.textContent = '0';
          recalcTotal();
          return;
        }
        let html = '';
        let total = 0;
        let totalQty = 0;
        cart.forEach(it => {
          const qty = (it.qty || 1);
          const lineTotal = qty * (it.price || 0);
          totalQty += qty;
          total += lineTotal;
          html += `<div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>${escapeHtml(it.name)}</span><strong>R$ ${lineTotal.toFixed(2).replace('.',',')}</strong></div>`;
        });
        html += `<hr style="border:none;border-top:1px solid #eee;margin:8px 0;">`;
        html += `<div style="display:flex;justify-content:space-between;font-weight:600;"><span>Total</span><span>R$ ${total.toFixed(2).replace('.',',')}</span></div>`;
        summaryEl.innerHTML = html;

        subtotalEl.textContent = formatCurrencyBR(total);
        qtdItensEl.textContent = String(totalQty);
        recalcTotal();
      }

      function escapeHtml(unsafe) {
        return unsafe?.replace(/[&<>\\"']/g, function(m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','\\':'&#92;','"':'&quot;',"'":'&#39;'}[m]; });
      }

      function parseCurrencyBR(text) {
        if (!text) return 0;
        text = String(text).trim();
        text = text.replace(/^R\$\s?/, '');
        // remover pontos de milhares e trocar vírgula por ponto
        text = text.replace(/\./g, '');
        text = text.replace(/,/g, '.');
        const n = parseFloat(text);
        return isNaN(n) ? 0 : n;
      }

      function formatCurrencyBR(num) {
        return 'R$ ' + num.toFixed(2).replace('.', ',');
      }

      function recalcTotal() {
        const subtotal = parseCurrencyBR(subtotalEl.textContent);
        const frete = parseCurrencyBR(freteEl.textContent);
        const desconto = parseCurrencyBR(descontoEl.textContent);
        const tot = Math.max(0, subtotal + frete - desconto);
        totalEl.textContent = formatCurrencyBR(tot);
      }

      // máscara simples de CEP: 00000-000
      cepInput && cepInput.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,8);
        if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
        this.value = v;
      });

      // calcular frete (valor fixo R$25,00)
      calcBtn && calcBtn.addEventListener('click', function(){
        const cep = (cepInput?.value || '').replace(/\D/g,'')
        if (!cep || cep.length !== 8) {
          cepMsg.textContent = 'CEP inválido. Digite 8 dígitos.';
          return;
        }
        freteEl.textContent = formatCurrencyBR(25.00);
        cepMsg.textContent = 'Frete calculado para CEP ' + cep.slice(0,5) + '-' + cep.slice(5);
        recalcTotal();
      });

      // limpar frete
      clearBtn && clearBtn.addEventListener('click', function(){
        const modal = document.getElementById('checkout-modal'); // Assumindo que o modal tem o ID 'checkout-modal'
        const checkoutCepInput = document.getElementById('checkout-cep');

        if (modal && modal.style.display === 'flex' && checkoutCepInput) {
          checkoutCepInput.value = '';
        } else if (cepInput) {
          cepInput.value = '';
        }
        freteEl.textContent = formatCurrencyBR(0);
        cepMsg.textContent = '';
        recalcTotal();
      });

      // abrir modal e renderizar resumo
      openBtn && openBtn.addEventListener('click', function(e){
        e.preventDefault();
        const cart = getCartData();
        cartInput.value = JSON.stringify(cart);
        renderSummary(cart);
        modal.style.display = 'flex';
        document.getElementById('checkout-step-1').style.display = 'block'; // Mostrar passo 1
        document.getElementById('checkout-step-2').style.display = 'none'; // Esconder passo 2
        setTimeout(()=>{
          const checkoutCepInput = document.getElementById('checkout-cep');
          if (checkoutCepInput) {
            checkoutCepInput.focus();
          }
        }, 100); // Focar no CEP
      });

      cancelBtn && cancelBtn.addEventListener('click', function(){
        modal.style.display = 'none';
      });

      closeBtn && closeBtn.addEventListener('click', function(){
        modal.style.display = 'none';
      });

      // fechar clicando fora
      modal && modal.addEventListener('click', function(e){
        if (e.target === modal) modal.style.display = 'none';
      });

      // Lógica para o botão "Continuar para Pagamento"
      const nextStepButton = document.getElementById('next-step-button');
      nextStepButton && nextStepButton.addEventListener('click', async function(e) {
        e.preventDefault();

        const cep = document.getElementById('checkout-cep').value.trim();
        const rua = document.getElementById('checkout-rua').value.trim();
        const numero = document.getElementById('checkout-numero').value.trim();
        const bairro = document.getElementById('checkout-bairro').value.trim();
        const cidade = document.getElementById('checkout-cidade').value.trim();
        const estado = document.getElementById('checkout-estado').value.trim();
        const complemento = document.getElementById('checkout-complemento').value.trim();

        // Validação dos campos
        if (!cep || !rua || !numero || !bairro || !cidade || !estado) {
          alert('Por favor, preencha todos os campos obrigatórios do endereço.');
          return;
        }
        if (cep.length !== 9 || !/^\d{5}-\d{3}$/.test(cep)) {
          alert('Por favor, insira um CEP válido (formato XXXXX-XXX).');
          return;
        }
        if (estado.length !== 2) {
          alert('Por favor, insira a UF do estado com 2 letras.');
          return;
        }

        const addressData = {
          cep: cep,
          rua: rua,
          numero: numero,
          complemento: complemento,
          bairro: bairro,
          cidade: cidade,
          estado: estado
        };

        try {
          const response = await fetch('php/save_address.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(addressData).toString()
          });

          const result = await response.json();

          if (result.success) {
            alert(result.message);
            document.getElementById('checkout-address-data').value = JSON.stringify(addressData); // Salvar dados do endereço para o próximo passo
            document.getElementById('checkout-step-1').style.display = 'none';
            document.getElementById('checkout-step-2').style.display = 'block';
            setTimeout(() => document.getElementById('checkout-email').focus(), 100);
          } else {
            alert('Erro ao salvar endereço: ' + result.message);
          }
        } catch (error) {
          console.error('Erro ao enviar dados do endereço:', error);
          alert('Ocorreu um erro ao salvar o endereço. Tente novamente.');
        }
      });

      // Lógica para o botão "Voltar" (no passo 2)
      const prevStepButton = document.getElementById('prev-step-button');
      prevStepButton && prevStepButton.addEventListener('click', function() {
        document.getElementById('checkout-step-2').style.display = 'none';
        document.getElementById('checkout-step-1').style.display = 'block';
        setTimeout(() => document.getElementById('checkout-cep').focus(), 100);
      });

      // mascaramento número do cartão
      const cardNumber = document.getElementById('checkout-card-number');
      cardNumber && cardNumber.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,16);
        v = v.replace(/(\d{4})(?=\d)/g, '$1 ');
        this.value = v;
      });

      // mascaramento validade (MM/AA)
      const expiryInput = document.getElementById('checkout-expiry');
      expiryInput && expiryInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').slice(0,4);
        if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
        this.value = v;
      });

      // validação simples antes de enviar
      form && form.addEventListener('submit', function(e){
        const email = document.getElementById('checkout-email').value.trim();
        if (!email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
          e.preventDefault();
          alert('Informe um e-mail válido.');
          return false;
        }
        // Adicionar validação para nome no cartão, número, validade e CVV
        const cardName = document.getElementById('checkout-card-name').value.trim();
        const cardNumberValue = document.getElementById('checkout-card-number').value.trim().replace(/\s/g, '');
        const expiry = document.getElementById('checkout-expiry').value.trim();
        const cvv = document.getElementById('checkout-cvv').value.trim();

        if (!cardName || !cardNumberValue || !expiry || !cvv) {
          e.preventDefault();
          alert('Por favor, preencha todos os campos do cartão.');
          return false;
        }
        if (cardNumberValue.length < 13 || cardNumberValue.length > 19) {
          e.preventDefault();
          alert('Número do cartão inválido.');
          return false;
        }
        if (!/^\d{2}\/\d{2}$/.test(expiry)) {
          e.preventDefault();
          alert('Validade do cartão inválida (MM/AA).');
          return false;
        }
        const [month, year] = expiry.split('/').map(Number);
        const currentYear = new Date().getFullYear() % 100; // last two digits
        const currentMonth = new Date().getMonth() + 1;

        if (month < 1 || month > 12 || year < currentYear || (year === currentYear && month < currentMonth)) {
          e.preventDefault();
          alert('Validade do cartão expirada ou inválida.');
          return false;
        }
        if (cvv.length < 3 || cvv.length > 4) {
          e.preventDefault();
          alert('CVV inválido.');
          return false;
        }

        // Preencher o campo hidden address_data com os dados do endereço
        const addressDataInput = document.getElementById('checkout-address-data');
        const addressData = {
          cep: document.getElementById('checkout-cep').value.trim(),
          rua: document.getElementById('checkout-rua').value.trim(),
          numero: document.getElementById('checkout-numero').value.trim(),
          complemento: document.getElementById('checkout-complemento').value.trim(),
          bairro: document.getElementById('checkout-bairro').value.trim(),
          cidade: document.getElementById('checkout-cidade').value.trim(),
          estado: document.getElementById('checkout-estado').value.trim()
        };
        addressDataInput.value = JSON.stringify(addressData);

        // deixa o envio prosseguir; o checkout.php lida com o restante
      });

      // recalcular inicialmente
      freteEl.textContent = freteEl.textContent || formatCurrencyBR(0);
      recalcTotal();

      // reagir a mudanças no carrinho via storage
      window.addEventListener('storage', function(e){
        if (e.key === 'cart' || e.key === 'carrinho') {
          const cart = getCartData();
          renderSummary(cart);
        }
      });

    })();
  </script>

  <!-- Sincronizar estado de sessão do servidor para o cabeçalho do carrinho -->
  <script>
document.addEventListener('DOMContentLoaded', function(){
  fetch('php/current_user.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data && data.logged && data.user) {
        const u = { nomeCompleto: data.user.nome_completo || '', login: data.user.login || '', cpf: data.user.cpf || '' };
        localStorage.setItem('usuario', JSON.stringify(u));
        const loggedKey = data.user.login || (data.user.nome_completo && data.user.nome_completo.split(' ')[0]) || '';
        if (loggedKey) localStorage.setItem('loggedUser', loggedKey);

        // inserir markup consistente com as outras páginas
        const userOptions = document.querySelector('.user-options');
        if (userOptions) {
          const displayName = (u.nomeCompleto || u.login || 'Usuário').split(' ')[0];
          userOptions.innerHTML = `
            <div class="account-container">
              <button id="account-button" class="account-button" aria-haspopup="true" aria-expanded="false">${displayName}</button>
              <div id="account-dropdown" class="dropdown hidden" role="menu">
                <ul>
                  <li id="manage-account" role="menuitem">Gerenciar Conta</li>
                  <li id="logout" role="menuitem">Sair</li>
                  <li id="login-link" role="menuitem" class="hidden"><a href="pag-login.html">Entrar / Cadastrar</a></li>
                </ul>
              </div>
            </div>
            <button id="theme-toggle">🌙</button>
          `;

          const accountButton = document.getElementById('account-button');
          const accountDropdown = document.getElementById('account-dropdown');
          const logoutItem = document.getElementById('logout');
          const manageAccount = document.getElementById('manage-account');

          // toggle dropdown
          if (accountButton && accountDropdown) {
            accountButton.addEventListener('click', function(e){
              e.stopPropagation();
              accountDropdown.classList.toggle('hidden');
              accountButton.setAttribute('aria-expanded', String(!accountDropdown.classList.contains('hidden')));
            });
          }

          // fechar ao clicar fora
          document.addEventListener('click', function(e){
            if (!e.target.closest('.account-container') && accountDropdown && !accountDropdown.classList.contains('hidden')) {
              accountDropdown.classList.add('hidden');
            }
          });

          // gerenciar conta (redireciona para página de login/gestão)
          if (manageAccount) {
            manageAccount.addEventListener('click', function(){
              window.location.href = 'pag-login.html';
            });
          }

          // logout: chamar endpoint e limpar localStorage
          if (logoutItem) {
            logoutItem.addEventListener('click', function(){
              fetch('php/logout.php', { method: 'GET', credentials: 'same-origin' })
                .finally(function(){
                  localStorage.removeItem('loggedUser');
                  localStorage.removeItem('usuario');
                  window.location.reload();
                });
            });
          }
        }
      } else {
        localStorage.removeItem('loggedUser');
      }
    })
    .catch(err => {
      console.warn('current_user fetch failed', err);
      localStorage.removeItem('loggedUser');
    });
});
  </script>

  <!-- Inserir modal de Gerenciar Conta (mesmo markup do index.html) -->
<div id="account-modal" class="modal hidden" role="dialog" aria-hidden="true">
  <div class="modal-content">
    <button id="close-account-modal" class="modal-close" aria-label="Fechar">&times;</button>
    <h2>Gerenciar Conta</h2>
    <form id="account-form">
      <label>Nome completo
        <input id="modal-nome" name="nome" type="text" />
      </label>
      <label>CPF
        <input id="modal-cpf" name="cpf" type="text" />
      </label>
      <label>Celular
        <input id="modal-celular" name="celular" type="text" />
      </label>
      <label>Login
        <input id="modal-login" name="login" type="text" />
      </label>
      <label>Senha
        <input id="modal-senha" name="senha" type="password" />
      </label>
      <div class="modal-actions">
        <button type="button" id="cancel-account">Cancelar</button>
        <button type="submit" id="save-account">Salvar</button>
      </div>
    </form>
  </div>
</div>

<script>
// Wire up modal behavior to the injected manage-account / logout elements
document.addEventListener('DOMContentLoaded', function(){
  const manageAccount = document.getElementById('manage-account');
  const accountModal = document.getElementById('account-modal');
  const closeModal = document.getElementById('close-account-modal');
  const cancelAccount = document.getElementById('cancel-account');
  const accountForm = document.getElementById('account-form');
  const accountDropdown = document.getElementById('account-dropdown');
  const accountButton = document.getElementById('account-button');

  function getStoredUser(){
    try{ return JSON.parse(localStorage.getItem('usuario')) || null; }catch(e){ return null }
  }

  if (manageAccount) {
    // Support async resolution: if localStorage doesn't have the user yet (fetch may be pending),
    // attempt to fetch current_user.php before redirecting to login. This prevents redirect when
    // the page is still syncing session state from the server.
    manageAccount.addEventListener('click', async function(e){
      e.preventDefault();

      function getStoredUserLocal(){
        try{ return JSON.parse(localStorage.getItem('usuario')) || null; }catch(e){ return null }
      }

      let user = getStoredUserLocal();
      if (!user) {
        // try to sync from server synchronously
        try {
          const resp = await fetch('php/current_user.php', { credentials: 'same-origin' });
          if (resp && resp.ok) {
            const data = await resp.json();
            if (data && data.logged && data.user) {
              const u = { nomeCompleto: data.user.nome_completo || '', login: data.user.login || '', cpf: data.user.cpf || '' };
              localStorage.setItem('usuario', JSON.stringify(u));
              const loggedKey = u.login || (u.nomeCompleto && u.nomeCompleto.split(' ')[0]) || '';
              if (loggedKey) localStorage.setItem('loggedUser', loggedKey);
              user = u;
            }
          }
        } catch (err) {
          console.warn('sync current_user failed', err);
        }
      }

      if (!user) {
        // still no user -> go to login
        window.location.href = 'pag-login.html';
        return;
      }

      // fill modal fields exactly like index.html
      const modalNome = document.getElementById('modal-nome');
      const modalCpf = document.getElementById('modal-cpf');
      const modalCel = document.getElementById('modal-celular');
      const modalLogin = document.getElementById('modal-login');
      const modalSenha = document.getElementById('modal-senha');

      if (modalNome) modalNome.value = user.nomeCompleto || '';
      if (modalCpf) modalCpf.value = user.cpf || '';
      if (modalCel) modalCel.value = user.telefoneCelular || '';
      if (modalLogin) modalLogin.value = user.login || '';
      if (modalSenha) modalSenha.value = user.senha || '';

      const accountModalEl = document.getElementById('account-modal');
      const accountDropdownEl = document.getElementById('account-dropdown');
      if (accountModalEl) accountModalEl.classList.remove('hidden');
      if (accountDropdownEl) accountDropdownEl.classList.add('hidden');
    });
  }

  if (closeModal) closeModal.addEventListener('click', function(){ if (accountModal) accountModal.classList.add('hidden'); });
  if (cancelAccount) cancelAccount.addEventListener('click', function(){ if (accountModal) accountModal.classList.add('hidden'); });

  if (accountForm) {
    accountForm.addEventListener('submit', function(e){
      e.preventDefault();
      const updated = {
        nomeCompleto: (document.getElementById('modal-nome')||{}).value?.trim() || '',
        cpf: (document.getElementById('modal-cpf')||{}).value?.trim() || '',
        telefoneCelular: (document.getElementById('modal-celular')||{}).value?.trim() || '',
        login: (document.getElementById('modal-login')||{}).value?.trim() || '',
        senha: (document.getElementById('modal-senha')||{}).value?.trim() || ''
      };
      localStorage.setItem('usuario', JSON.stringify(updated));
      // ensure loggedUser matches updated login or first name
      const loggedKey = updated.login || (updated.nomeCompleto && updated.nomeCompleto.split(' ')[0]) || '';
      if (loggedKey) localStorage.setItem('loggedUser', loggedKey);
      else localStorage.removeItem('loggedUser');

      if (accountButton) accountButton.textContent = updated.nomeCompleto ? updated.nomeCompleto.split(' ')[0] : updated.login || 'Usuário';

      if (accountModal) accountModal.classList.add('hidden');
      alert('Dados atualizados com sucesso.');
    });
  }

  // ensure logout also clears session on server and localStorage
  const logoutItem = document.getElementById('logout');
  if (logoutItem) {
    logoutItem.addEventListener('click', function(){
      fetch('php/logout.php', { method: 'GET', credentials: 'same-origin' })
        .finally(function(){
          localStorage.removeItem('loggedUser');
          localStorage.removeItem('usuario');
          window.location.reload();
        });
    });
  }
});
</script>

  <!-- Checkout Modal de Duas Etapas -->
  <div id="checkout-modal" class="modal" style="display:none; position:fixed; inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;z-index:9999;padding:20px;">
    <div style="background:#fff;padding:20px;border-radius:12px;max-width:600px;width:100%;box-shadow:0 10px 40px rgba(0,0,0,0.4);position:relative;font-family:Arial,Helvetica,sans-serif;">
      <button id="checkout-close" aria-label="Fechar" style="position:absolute;right:14px;top:12px;background:transparent;border:none;font-size:22px;cursor:pointer;color:#666;">&times;</button>
      <h2 style="margin:0 0 15px 0;font-size:22px;color:#222;text-align:center;">Finalizar Compra</h2>

      <!-- Etapa 1: Endereço -->
      <div id="checkout-step-1">
        <h3 style="margin:0 0 15px 0;font-size:18px;color:#333;">1. Endereço de Entrega</h3>
        <form id="address-form" style="display:flex;flex-direction:column;gap:10px;">
          <label style="font-weight:600;font-size:13px;color:#333;">CEP</label>
          <input type="text" name="cep" id="checkout-cep" required placeholder="00000-000" style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <label style="font-weight:600;font-size:13px;color:#333;">Rua</label>
          <input type="text" name="rua" id="checkout-rua" required style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <div style="display:flex;gap:10px;">
            <div style="flex:1;">
              <label style="font-weight:600;font-size:13px;color:#333;">Número</label>
              <input type="text" name="numero" id="checkout-numero" required style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">
            </div>
            <div style="flex:2;">
              <label style="font-weight:600;font-size:13px;color:#333;">Bairro</label>
              <input type="text" name="bairro" id="checkout-bairro" required style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">
            </div>
          </div>

          <label style="font-weight:600;font-size:13px;color:#333;">Cidade</label>
          <input type="text" name="cidade" id="checkout-cidade" required style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <label style="font-weight:600;font-size:13px;color:#333;">Estado (UF)</label>
          <input type="text" name="estado" id="checkout-estado" required maxlength="2" style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <label style="font-weight:600;font-size:13px;color:#333;">Complemento (Opcional)</label>
          <input type="text" name="complemento" id="checkout-complemento" style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <button type="button" id="next-step-button" style="padding:12px 20px;background:#b52a37;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;margin-top:15px;">Continuar para Pagamento</button>
        </form>
      </div>

      <!-- Etapa 2: Pagamento -->
      <div id="checkout-step-2" style="display:none;">
        <h3 style="margin:0 0 15px 0;font-size:18px;color:#333;">2. Dados de Pagamento</h3>
        <form id="payment-form" method="POST" action="php/checkout.php" style="display:flex;flex-direction:column;gap:10px;">
          <label style="font-weight:600;font-size:13px;color:#333;">E-mail para Nota Fiscal</label>
          <input type="email" name="email" id="checkout-email" required style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <label style="font-weight:600;font-size:13px;color:#333;">Nome no Cartão</label>
          <input type="text" name="card_name" id="checkout-card-name" required style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <label style="font-weight:600;font-size:13px;color:#333;">Número do Cartão</label>
          <input type="text" name="card_number" id="checkout-card-number" inputmode="numeric" required placeholder="0000 0000 0000 0000" style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">

          <div style="display:flex;gap:10px;">
            <div style="flex:1;">
              <label style="font-weight:600;font-size:13px;color:#333;">Validade (MM/AA)</label>
              <input type="text" name="expiry" id="checkout-expiry" required placeholder="MM/AA" style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">
            </div>
            <div style="flex:1;">
              <label style="font-weight:600;font-size:13px;color:#333;">CVV</label>
              <input type="text" name="cvv" id="checkout-cvv" inputmode="numeric" required placeholder="123" style="padding:10px;border:1px solid #e3e3e3;border-radius:8px;width:100%;font-size:14px;">
            </div>
          </div>

          <input type="hidden" name="cart_data" id="checkout-cart-data" value="[]">
          <input type="hidden" name="address_data" id="checkout-address-data" value="[]">

          <div style="display:flex;gap:10px;justify-content:space-between;align-items:center;margin-top:15px;">
            <button type="button" id="prev-step-button" style="padding:10px 14px;background:#f3f3f3;border:1px solid #e0e0e0;border-radius:8px;cursor:pointer;color:#333;">Voltar</button>
            <button type="submit" id="checkout-submit" style="padding:12px 20px;background:#b52a37;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Pagar e Finalizar</button>
          </div>
        </form>
      </div>

      <aside style="width:220px;background:#fafafa;border-left:1px solid #f0f0f0;padding-left:16px;border-radius:8px;margin-top:20px;">
        <h3 style="margin-top:0;font-size:14px;color:#222;">Resumo do Pedido</h3>
        <div id="checkout-summary" style="font-size:13px;color:#444;line-height:1.4;">Carregando resumo...</div>
      </aside>

    </div>
  </div>

  <!-- =========================
       Footer
       ========================= -->
  <footer>
    <p>&copy; 2024 Bangu Store. Todos os direitos reservados.</p>
    <img src="img/logo-1701858752-1719005234-37cf381cc5c27eb83f33c1b2094320a91719005235-320-0.webp" alt="Bangu Atlético Clube" width="80" height="auto" />
    <div class="contato">
      <p>Estamos à disposição para atender você!</p>
      <p><a href="https://wa.me/+5521989496516?text=MENSAGEM" target="_blank">21989496516</a></p>
      <p><a href="mailto:bangustore7@gmail.com">bangustore7@gmail.com</a></p>
    </div>
  </footer>

  <!-- =========================
       Scripts
       ========================= -->

  <script src="JavaScript/script.js"></script>
  <script src="JavaScript/finalizarcompra.js"></script>

  <script>
    (function(){
      const openBtn = document.getElementById('finalizar-compra');
      const modal = document.getElementById('checkout-modal');
      const closeBtn = document.getElementById('checkout-close');

      const step1 = document.getElementById('checkout-step-1');
      const step2 = document.getElementById('checkout-step-2');
      const nextStepBtn = document.getElementById('next-step-button');
      const prevStepBtn = document.getElementById('prev-step-button');

      const addressForm = document.getElementById('address-form');
      const paymentForm = document.getElementById('payment-form');

      const cartInput = document.getElementById('checkout-cart-data');
      const addressInput = document.getElementById('checkout-address-data');
      const summaryEl = document.getElementById('checkout-summary');

      const cepInput = document.getElementById('checkout-cep');
      const ruaInput = document.getElementById('checkout-rua');
      const numeroInput = document.getElementById('checkout-numero');
      const bairroInput = document.getElementById('checkout-bairro');
      const cidadeInput = document.getElementById('checkout-cidade');
      const estadoInput = document.getElementById('checkout-estado');
      const complementoInput = document.getElementById('checkout-complemento');
      const checkoutCartData = document.getElementById('checkout-cart-data');

      const emailInput = document.getElementById('checkout-email');
      const cardNameInput = document.getElementById('checkout-card-name');
      const cardNumberInput = document.getElementById('checkout-card-number');
      const expiryInput = document.getElementById('checkout-expiry');
      const cvvInput = document.getElementById('checkout-cvv');

      const subtotalEl = document.getElementById('subtotal-carrinho');
      const freteEl = document.getElementById('frete-carrinho');
      const descontoEl = document.getElementById('desconto-carrinho');
      const totalEl = document.getElementById('total-carrinho');
      const qtdItensEl = document.getElementById('qtd-itens');

      function getCartData() {
        let cart = [];
        try {
          cart = JSON.parse(localStorage.getItem('cart') || localStorage.getItem('carrinho') || '[]');
        } catch (e) { cart = []; }

        if (!Array.isArray(cart) || cart.length === 0) {
          const items = [];
          document.querySelectorAll('#carrinho-itens-js .carrinho-item').forEach(el => {
            const name = el.querySelector('.item-nome')?.textContent?.trim() || el.dataset.name || 'Produto';
            const priceText = el.querySelector('.item-preco')?.textContent?.replace(/[^0-9,\.]/g,'') || '0';
            const price = parseFloat(priceText.replace(',','.')) || 0;
            const qty = parseInt(el.querySelector('.item-quantidade')?.value || el.dataset.qty || '1', 10) || 1;
            items.push({ name, qty, price });
          });
          cart = items;
        }
        return cart;
      }

      function renderSummary(cart) {
        if (!Array.isArray(cart) || cart.length === 0) {
          summaryEl.textContent = 'Carrinho vazio';
          return;
        }
        let html = '';
        let total = 0;
        let totalQty = 0;
        cart.forEach(it => {
          const qty = (it.qty || 1);
          const lineTotal = qty * (it.price || 0);
          totalQty += qty;
          total += lineTotal;
          html += `<div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>${escapeHtml(it.name)}</span><strong>R$ ${lineTotal.toFixed(2).replace('.',',')}</strong></div>`;
        });
        html += `<hr style="border:none;border-top:1px solid #eee;margin:8px 0;">`;
        html += `<div style="display:flex;justify-content:space-between;font-weight:600;"><span>Total</span><span>R$ ${total.toFixed(2).replace('.',',')}</span></div>`;
        summaryEl.innerHTML = html;
      }

      function escapeHtml(unsafe) {
        return unsafe?.replace(/[&<>\\"']/g, function(m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','\\':'&#92;','"':'&quot;',"'":'&#39;'}[m]; });
      }

      function parseCurrencyBR(text) {
        if (!text) return 0;
        text = String(text).trim();
        text = text.replace(/^R\$\s?/, '');
        text = text.replace(/\./g, '');
        text = text.replace(/,/g, '.');
        const n = parseFloat(text);
        return isNaN(n) ? 0 : n;
      }

      function formatCurrencyBR(num) {
        return 'R$ ' + num.toFixed(2).replace('.', ',');
      }

      function validateAddressForm() {
        const fields = [cepInput, ruaInput, numeroInput, bairroInput, cidadeInput, estadoInput];
        for (const field of fields) {
          if (!field.value.trim()) {
            alert(`Por favor, preencha o campo ${field.previousElementSibling.textContent.replace(':','').trim()}.`);
            field.focus();
            return false;
          }
        }
        if (!/^\d{5}-\d{3}$/.test(cepInput.value.trim())) {
          alert('Por favor, insira um CEP válido (formato XXXXX-XXX).');
          cepInput.focus();
          return false;
        }
        if (estadoInput.value.trim().length !== 2) {
          alert('Por favor, insira a sigla do Estado com 2 caracteres.');
          estadoInput.focus();
          return false;
        }
        return true;
      }

      function validatePaymentForm() {
        const fields = [emailInput, cardNameInput, cardNumberInput, expiryInput, cvvInput];
        for (const field of fields) {
          if (!field.value.trim()) {
            alert(`Por favor, preencha o campo ${field.previousElementSibling.textContent.replace(':','').trim()}.`);
            field.focus();
            return false;
          }
        }
        if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(emailInput.value.trim())) {
          alert('Por favor, insira um e-mail válido.');
          emailInput.focus();
          return false;
        }
        return true;
      }

      openBtn && openBtn.addEventListener('click', function(e){
        e.preventDefault();
        const cart = getCartData();
        cartInput.value = JSON.stringify(cart);
        renderSummary(cart);
        modal.style.display = 'flex';
        step1.style.display = 'block'; // Mostrar passo 1
        step2.style.display = 'none'; // Esconder passo 2
        setTimeout(()=>document.getElementById('checkout-cep').focus(), 100); // Focar no CEP
      });

      closeBtn && closeBtn.addEventListener('click', function(){
        modal.style.display = 'none';
      });

      modal && modal.addEventListener('click', function(e){
        if (e.target === modal) modal.style.display = 'none';
      });

      nextStepBtn && nextStepBtn.addEventListener('click', function(){
        const cepInput = document.getElementById('checkout-cep');
        const ruaInput = document.getElementById('checkout-rua');
        const numeroInput = document.getElementById('checkout-numero');
        const complementoInput = document.getElementById('checkout-complemento');
        const bairroInput = document.getElementById('checkout-bairro');
        const cidadeInput = document.getElementById('checkout-cidade');
        const estadoInput = document.getElementById('checkout-estado');

        function validateAddressForm() {
          if (!cepInput || !ruaInput || !numeroInput || !bairroInput || !cidadeInput || !estadoInput) {
            alert('Erro: Campos do endereço não encontrados.');
            return false;
          }
          if (!cepInput.value.trim() || !ruaInput.value.trim() || !numeroInput.value.trim() || !bairroInput.value.trim() || !cidadeInput.value.trim() || !estadoInput.value.trim()) {
            alert('Todos os campos obrigatórios do endereço devem ser preenchidos.');
            return false;
          }
          return true;
        }

        if (validateAddressForm()) {
          const addressData = {
            cep: cepInput.value.trim(),
            rua: ruaInput.value.trim(),
            numero: numeroInput.value.trim(),
            complemento: complementoInput.value.trim(),
            bairro: bairroInput.value.trim(),
            cidade: cidadeInput.value.trim(),
            estado: estadoInput.value.trim()
          };

          const formData = new URLSearchParams();
          for (const key in addressData) {
            formData.append(key, addressData[key]);
          }

          fetch('php/save_address.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Armazenar o address_id em um campo oculto no formulário de checkout
              const checkoutForm = document.getElementById('checkout-form');
              let addressIdInput = checkoutForm.querySelector('input[name="address_id"]');
              if (!addressIdInput) {
                addressIdInput = document.createElement('input');
                addressIdInput.type = 'hidden';
                addressIdInput.name = 'address_id';
                checkoutForm.appendChild(addressIdInput);
              }
              addressIdInput.value = data.address_id;

              // Transicionar para o próximo passo (pagamento)
              step1.style.display = 'none';
              step2.style.display = 'block';
              setTimeout(()=>emailInput.focus(), 100);
            } else {
              alert(data.message);
            }
          })
          .catch(error => {
            console.error('Erro ao salvar endereço:', error);
            alert('Erro ao salvar endereço. Tente novamente.');
          });
        }
      });

      prevStepBtn && prevStepBtn.addEventListener('click', function(){
        step1.style.display = 'block';
        step2.style.display = 'none';
        setTimeout(()=>cepInput.focus(), 100);
      });

      paymentForm && paymentForm.addEventListener('submit', function(e){
        e.preventDefault();
        if (validatePaymentForm()) {
          // Aqui você faria a requisição AJAX para processar o pagamento e salvar o pedido
          // Por enquanto, vamos simular o sucesso e fechar o modal
          alert('Pagamento processado com sucesso! Seu pedido foi realizado.');
          modal.style.display = 'none';
          // Limpar carrinho e redirecionar ou atualizar a página
          localStorage.removeItem('cart');
          localStorage.removeItem('carrinho');
          window.location.reload();
        }
      });

      // Máscaras e validações de input
      cepInput && cepInput.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,8);
        if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
        this.value = v;
      });

      cardNumberInput && cardNumberInput.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,16);
        v = v.replace(/(\d{4})(?=\d)/g, '$1 ');
        this.value = v;
      });

      expiryInput && expiryInput.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,4);
        if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
        this.value = v;
      });

      cvvInput && cvvInput.addEventListener('input', function(){
        this.value = this.value.replace(/\D/g,'').slice(0,4);
      });

    })();
  </script>

  <!-- Sincronizar estado de sessão do servidor para o cabeçalho do carrinho -->
  <script>
document.addEventListener('DOMContentLoaded', function(){
  fetch('php/current_user.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data && data.logged && data.user) {
        const u = { nomeCompleto: data.user.nome_completo || '', login: data.user.login || '', cpf: data.user.cpf || '' };
        localStorage.setItem('usuario', JSON.stringify(u));
        const loggedKey = data.user.login || (data.user.nomeCompleto && data.user.nomeCompleto.split(' ')[0]) || '';
        if (loggedKey) localStorage.setItem('loggedUser', loggedKey);

        const userOptions = document.querySelector('.user-options');
        if (userOptions) {
          const displayName = (u.nomeCompleto || u.login || 'Usuário').split(' ')[0];
          userOptions.innerHTML = `
            <div class="account-container">
              <button id="account-button" class="account-button" aria-haspopup="true" aria-expanded="false">${displayName}</button>
              <div id="account-dropdown" class="dropdown hidden" role="menu">
                <ul>
                  <li id="manage-account" role="menuitem">Gerenciar Conta</li>
                  <li id="logout" role="menuitem">Sair</li>
                  <li id="login-link" role="menuitem" class="hidden"><a href="pag-login.html">Entrar / Cadastrar</a></li>
                </ul>
              </div>
            </div>
            <button id="theme-toggle">🌙</button>
          `;

          const accountButton = document.getElementById('account-button');
          const accountDropdown = document.getElementById('account-dropdown');

          if (accountButton && accountDropdown) {
            accountButton.addEventListener('click', () => {
              accountDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
              if (!e.target.closest('.account-container') && !accountDropdown.classList.contains('hidden')) {
                accountDropdown.classList.add('hidden');
              }
            });
          }

          const logoutButton = document.getElementById('logout');
          if (logoutButton) {
            logoutButton.addEventListener('click', () => {
              fetch('php/logout.php')
                .then(() => {
                  localStorage.removeItem('usuario');
                  localStorage.removeItem('loggedUser');
                  window.location.reload();
                });
            });
          }
        }
      }
    })
    .catch(error => console.error('Erro ao buscar status do usuário:', error));
});
  </script>

  <!-- Modal de Checkout -->
  <div id="checkout-modal" class="modal hidden">
    <div class="modal-content">
      <button id="checkout-close" class="modal-close">&times;</button>
      <h2>Finalizar Compra</h2>

      <form id="checkout-form" action="php/checkout.php" method="POST">
        <!-- Passo 1: Endereço de Entrega -->
        <div id="checkout-step-1">
          <h3>1. Endereço de Entrega</h3>
          <div class="address-form">
            <label for="checkout-cep">CEP:</label>
            <input type="text" id="checkout-cep" name="cep" required>

            <label for="checkout-rua">Rua:</label>
            <input type="text" id="checkout-rua" name="rua" required>

            <label for="checkout-numero">Número:</label>
            <input type="text" id="checkout-numero" name="numero" required>

            <label for="checkout-complemento">Complemento (Opcional):</label>
            <input type="text" id="checkout-complemento" name="complemento">

            <label for="checkout-bairro">Bairro:</label>
            <input type="text" id="checkout-bairro" name="bairro" required>

            <label for="checkout-cidade">Cidade:</label>
            <input type="text" id="checkout-cidade" name="cidade" required>

            <label for="checkout-estado">Estado (UF):</label>
            <input type="text" id="checkout-estado" name="estado" maxlength="2" required>
          </div>
          <button type="button" id="next-step-button">Continuar para Pagamento</button>
        </div>

        <!-- Passo 2: Pagamento -->
        <div id="checkout-step-2" style="display:none;">
          <h3>2. Pagamento</h3>
          <div class="payment-form">
            <label for="checkout-email">E-mail:</label>
            <input type="email" id="checkout-email" name="email" required>

            <label for="checkout-card-name">Nome no Cartão:</label>
            <input type="text" id="checkout-card-name" name="card_name" required>

            <label for="checkout-card-number">Número do Cartão:</label>
            <input type="text" id="checkout-card-number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" required>

            <label for="checkout-expiry">Validade (MM/AA):</label>
            <input type="text" id="checkout-expiry" name="expiry" placeholder="MM/AA" required>

            <label for="checkout-cvv">CVV:</label>
            <input type="text" id="checkout-cvv" name="cvv" maxlength="4" required>
          </div>
          <button type="button" id="prev-step-button">Voltar</button>
          <button type="submit">Finalizar Pedido</button>
        </div>

        <!-- Campos ocultos para dados do carrinho e endereço -->
        <input type="hidden" id="checkout-cart-data" name="cart_data">
        <input type="hidden" id="checkout-address-data" name="address_data">
      </form>
    </div>
  </div>

</body>
</html>
