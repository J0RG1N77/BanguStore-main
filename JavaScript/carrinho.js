(() => {
  const STORAGE_KEY = 'bangu-cart';
  let cart = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

  function saveCart() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    renderMiniCart();
  }

  function formatMoney(n) {
    return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }

  // --- Renderiza o mini-carrinho do cabeçalho ---
  function renderMiniCart() {
    const countEl = document.getElementById('cart-count');
    const itemsEl = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');

    if (!countEl || !itemsEl || !totalEl) return;

    countEl.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    itemsEl.innerHTML = '';

    if (cart.length === 0) {
      itemsEl.innerHTML = '<li>Carrinho vazio.</li>';
      totalEl.textContent = '0,00';
      return;
    }

    let total = 0;
    cart.forEach((item, index) => {
      total += item.price * item.quantity;
      const li = document.createElement('li');
      li.style.display = 'flex';
      li.style.alignItems = 'center';
      li.style.justifyContent = 'space-between';
      li.style.gap = '8px';

      li.innerHTML = `
        <img src="${item.image}" alt="${item.name}" width="40" style="border-radius:4px;">
        <span>${item.name} x${item.quantity}</span>
        <span>${formatMoney(item.price * item.quantity)}</span>
        <button class="remove-btn" data-index="${index}" style="background:none;border:none;cursor:pointer;">❌</button>
      `;
      itemsEl.appendChild(li);
    });

    totalEl.textContent = formatMoney(total);

    // Remover itens
    itemsEl.querySelectorAll('.remove-btn').forEach(btn => {
      btn.onclick = () => {
        const i = parseInt(btn.dataset.index);
        cart.splice(i, 1);
        saveCart();
      };
    });
  }

  // --- Adicionar produtos ---
  document.addEventListener('click', e => {
    const btn = e.target.closest('.add-to-cart');
    if (!btn) return;

    const productDiv = btn.closest('.produto');
    const id = productDiv.dataset.id || crypto.randomUUID();
    const name = productDiv.querySelector('h3').textContent;
    const price = parseFloat(productDiv.querySelector('p').textContent.replace('R$', '').replace(',', '.'));
    const image = productDiv.querySelector('img').src;

    const existing = cart.find(p => p.id === id);
    if (existing) existing.quantity += 1;
    else cart.push({ id, name, price, image, quantity: 1 });

    saveCart();
    alert('Produto adicionado ao carrinho!');
  });

  // Inicializa
  renderMiniCart();
})();
