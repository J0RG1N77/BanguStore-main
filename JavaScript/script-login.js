// script-login.js - validação leve do formulário de login (não bloqueia envio para o servidor)

function showFeedback(msg, type) {
    const feedbackMessage = document.getElementById('feedback-message');
    if (!feedbackMessage) return;
    feedbackMessage.textContent = msg;
    feedbackMessage.className = 'feedback-message ' + (type || 'error');
    feedbackMessage.style.display = 'block';
    setTimeout(() => { feedbackMessage.style.display = 'none'; }, 4000);
}

// Máscara simples de CPF (000.000.000-00)
function maskCPF(value) {
    return value
      .replace(/\D/g, '')
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

// Mover seleção de elementos e listeners para quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function () {
    console.log('[login] script loaded, DOMContentLoaded');

    const formLogin = document.getElementById('login-form');
    const feedbackMessage = document.getElementById('feedback-message');
    const cpfInput = document.getElementById('cpf');

    if (cpfInput) {
        cpfInput.addEventListener('input', function () {
            this.value = maskCPF(this.value);
        });
    }

    if (formLogin) {
        formLogin.addEventListener('submit', function (e) {
            console.log('[login] submit handler start');
            const usuarioEl = document.getElementById('usuario');
            const senhaEl = document.getElementById('senha');
            const cpfEl = document.getElementById('cpf');

            const login = (usuarioEl && typeof usuarioEl.value === 'string') ? usuarioEl.value.trim() : '';
            const senha = (senhaEl && typeof senhaEl.value === 'string') ? senhaEl.value : '';
            const cpf = (cpfEl && typeof cpfEl.value === 'string') ? cpfEl.value.trim() : '';

            console.log('[login] values:', { login: login || null, senha: !!senha, cpf: cpf || null });

            if (!login || !senha || !cpf) {
                console.warn('[login] missing fields, allowing submit for debugging');
                showFeedback('Preencha usuário, senha e CPF.', 'error');
                return;
            }

            const digits = cpf.replace(/\D/g, '');
            if (digits.length !== 11) {
                console.warn('[login] CPF inválido length=' + digits.length + ', allowing submit for debugging');
                showFeedback('CPF inválido. Informe 11 dígitos.', 'error');
                return;
            }

            console.log('[login] validation passed, submitting form');
        });
    } else {
        console.warn('[login] formLogin not found');
    }

    // Exibir mensagens do servidor via query string (error / success)
    if (feedbackMessage) {
        const params = new URLSearchParams(window.location.search);
        if (params.has('error')) {
            showFeedback(decodeURIComponent(params.get('error')), 'error');
            history.replaceState(null, '', window.location.pathname);
        } else if (params.has('success')) {
            showFeedback(decodeURIComponent(params.get('success')), 'success');
            history.replaceState(null, '', window.location.pathname);
        }
    }
});
