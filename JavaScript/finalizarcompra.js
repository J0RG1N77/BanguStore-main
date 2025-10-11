document.addEventListener("DOMContentLoaded", () => {
  const STORAGE_KEY = "bangu-cart";
  let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
  let freteBase = 25;
  let freteAtual = freteBase;
  let descontoAtivo = 0;

  const carrinhoEl = document.getElementById("carrinho-itens-js");
  const subtotalEl = document.getElementById("subtotal-carrinho");
  const freteEl = document.getElementById("frete-carrinho");
  const descontoEl = document.getElementById("desconto-carrinho");
  const totalEl = document.getElementById("total-carrinho");
  const qtdItensEl = document.getElementById("qtd-itens");
  const campoCupom = document.getElementById("campo-cupom");
  const aplicarBtn = document.getElementById("aplicar-cupom");
  const removerBtn = document.getElementById("remover-cupom");
  const msgCupom = document.getElementById("cupom-msg");

  const money = (n) => new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(n);

  function salvarCarrinho() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
  }

  function renderCarrinhoFinal() {
    if (!carrinhoEl) return;
    carrinhoEl.innerHTML = "";

    if (cart.length === 0) {
      carrinhoEl.innerHTML = "<p>Seu carrinho está vazio.</p>";
      subtotalEl.textContent = money(0);
      freteEl.textContent = money(freteBase);
      descontoEl.textContent = money(0);
      totalEl.textContent = money(freteBase);
      qtdItensEl.textContent = 0;
      return;
    }

    let subtotal = 0;
    cart.forEach((item, index) => {
      subtotal += item.preco * item.quantidade;

      const div = document.createElement("div");
      div.classList.add("carrinho-item");
      div.style.display = "flex";
      div.style.alignItems = "center";
      div.style.justifyContent = "space-between";
      div.style.gap = "10px";
      div.style.padding = "5px 0";

      div.innerHTML = `
        <img src="${item.imagem}" alt="${item.nome}" width="60">
        <span>${item.nome}</span>
        <div class="qtd-controls" style="display:flex;align-items:center;gap:5px;">
          <button class="diminuir-qtd" data-index="${index}">➖</button>
          <span class="qtd">${item.quantidade}</span>
          <button class="aumentar-qtd" data-index="${index}">➕</button>
        </div>
        <span>${money(item.preco * item.quantidade)}</span>
        <button class="remover-item" data-index="${index}">❌</button>
      `;
      carrinhoEl.appendChild(div);
    });

    subtotalEl.textContent = money(subtotal);
    freteEl.textContent = money(freteAtual);
    descontoEl.textContent = money(descontoAtivo);
    totalEl.textContent = money(subtotal + freteAtual - descontoAtivo);
    qtdItensEl.textContent = cart.reduce((sum, i) => sum + i.quantidade, 0);

    // ===== Eventos dos botões =====
    carrinhoEl.querySelectorAll(".aumentar-qtd").forEach((btn) => {
      btn.onclick = () => {
        const i = parseInt(btn.dataset.index);
        cart[i].quantidade += 1;
        salvarCarrinho();
        renderCarrinhoFinal();
      };
    });

    carrinhoEl.querySelectorAll(".diminuir-qtd").forEach((btn) => {
      btn.onclick = () => {
        const i = parseInt(btn.dataset.index);
        if (cart[i].quantidade > 1) cart[i].quantidade -= 1;
        else cart.splice(i, 1);
        salvarCarrinho();
        renderCarrinhoFinal();
      };
    });

    carrinhoEl.querySelectorAll(".remover-item").forEach((btn) => {
      btn.onclick = () => {
        const i = parseInt(btn.dataset.index);
        cart.splice(i, 1);
        salvarCarrinho();
        renderCarrinhoFinal();
      };
    });
  }

  // ===== Cupom =====
  if (aplicarBtn) {
    aplicarBtn.onclick = () => {
      const valorCupom = campoCupom.value.trim().toUpperCase();
      descontoAtivo = 0;
      freteAtual = freteBase;

      if (valorCupom === "UNI25OFF") {
        descontoAtivo = cart.reduce((sum, i) => sum + i.preco * i.quantidade, 0) * 0.25;
        msgCupom.innerText = "✅ Cupom de 25% aplicado!";
        msgCupom.style.color = "green";
        aplicarBtn.disabled = true;
        removerBtn.style.display = "inline-block";
      } else if (valorCupom === "BGNOTA10") {
        freteAtual = 0;
        msgCupom.innerText = "✅ Frete grátis aplicado!";
        msgCupom.style.color = "green";
        aplicarBtn.disabled = true;
        removerBtn.style.display = "inline-block";
      } else {
        msgCupom.innerText = "❌ Cupom inválido!";
        msgCupom.style.color = "red";
        return;
      }

      renderCarrinhoFinal();
    };
  }

  if (removerBtn) {
    removerBtn.onclick = () => {
      descontoAtivo = 0;
      freteAtual = freteBase;
      campoCupom.value = "";
      msgCupom.innerText = "Cupom removido!";
      msgCupom.style.color = "orange";
      aplicarBtn.disabled = false;
      removerBtn.style.display = "none";
      renderCarrinhoFinal();
    };
  }

  renderCarrinhoFinal();
});
