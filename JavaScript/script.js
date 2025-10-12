document.addEventListener("DOMContentLoaded", () => {
  const STORAGE_KEY = 'bangu-cart';
  let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

  const cartButton = document.getElementById('cart-button');
  const cartDropdown = document.getElementById('cart-dropdown');
  const cartItems = document.getElementById('cart-items');
  const cartTotal = document.getElementById('cart-total');
  const cartCount = document.getElementById('cart-count');

  const themeToggle = document.getElementById('theme-toggle');

  const formatMoney = (n) => new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(n);

  // ------------------ Funções do carrinho ------------------
  function salvarCarrinho() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
  }

  function updateCart() {
    if (!cartItems || !cartTotal || !cartCount) return;
    cartItems.innerHTML = '';
    let total = 0;
    let count = 0;

    cart.forEach((item, index) => {
      total += item.preco * item.quantidade;
      count += item.quantidade;

      const li = document.createElement('li');
      li.style.display = 'flex';
      li.style.alignItems = 'center';
      li.style.gap = '8px';
      li.style.marginBottom = '8px';

      li.innerHTML = `
        <img src="${item.imagem}" alt="${item.nome}" style="width:50px;height:auto;border-radius:6px;">
        <div style="flex:1;">
          <strong>${item.nome}</strong><br>
          ${formatMoney(item.preco)} x ${item.quantidade} = <strong>${formatMoney(item.preco * item.quantidade)}</strong>
        </div>
        <button data-index="${index}" style="background:#f8d7da;border:none;color:#d32f2f;padding:4px 8px;border-radius:4px;cursor:pointer;">x</button>
      `;
      cartItems.appendChild(li);
    });

    cartTotal.textContent = total.toFixed(2);
    cartCount.textContent = count;

    cartItems.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', () => {
        const index = parseInt(btn.dataset.index);
        if (!isNaN(index)) {
          cart[index].quantidade -= 1;
          if (cart[index].quantidade <= 0) {
            cart.splice(index, 1);
          }
          salvarCarrinho();
          updateCart();
        }
      });
    });
  }

  // ------------------ Toggle do dropdown ------------------
  if (cartButton && cartDropdown) {
    cartButton.addEventListener('click', () => {
      cartDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.cart-container') && !cartDropdown.classList.contains('hidden')) {
        cartDropdown.classList.add('hidden');
      }
    });
  }

  // ------------------ Adicionar produtos ------------------
  const buttons = document.querySelectorAll('.add-to-cart');
  buttons.forEach(button => {
    button.addEventListener('click', async (event) => {
      event.preventDefault(); // Previne o comportamento padrão do botão

      // Verifica o status de login
      const response = await fetch('php/check_login_status.php');
      const data = await response.json();

      if (!data.isLoggedIn) {
        // Exibe o modal de login se o usuário não estiver logado
        const loginModal = document.getElementById('login-modal');
        loginModal.style.display = 'block';

        // Adiciona evento para fechar o modal
        loginModal.querySelector('.close-button').onclick = () => {
          loginModal.style.display = 'none';
        };
        window.onclick = (event) => {
          if (event.target == loginModal) {
            loginModal.style.display = 'none';
          }
        };

        // Redireciona para a página de login ao clicar no botão "Fazer Login"
        document.getElementById('login-modal-button').onclick = () => {
          window.location.href = 'pag-login.html'; // Ajuste o caminho para sua página de login
        };

        return; // Impede a adição ao carrinho
      }

      const card = button.closest('.produto');
      const nome = button.dataset.name || card.querySelector('h3')?.textContent || 'Produto';
      const preco = parseFloat(button.dataset.price || card.querySelector('p')?.textContent.replace('R$', '').replace(',', '.') || 0);
      const imagem = card.querySelector('img')?.src || '';

      const existente = cart.find(item => item.nome === nome);
      if (existente) {
        existente.quantidade += 1;
      } else {
        cart.push({ nome, preco, imagem, quantidade: 1 });
      }

      salvarCarrinho();
      updateCart();

      button.disabled = true;
      const original = button.textContent;
      button.textContent = 'Adicionado!';
      setTimeout(() => {
        button.textContent = original;
        button.disabled = false;
      }, 800);
    });
  });

  updateCart();

  // ------------------ Tema claro/escuro ------------------
  const currentTheme = localStorage.getItem('theme') || 'light';
  document.body.classList.toggle('dark-theme', currentTheme === 'dark');

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const isDark = document.body.classList.toggle('dark-theme');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
  }

  // ------------------ Função global ------------------
  window.getCart = () => [...cart];
});
