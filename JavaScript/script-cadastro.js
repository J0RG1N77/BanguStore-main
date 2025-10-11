// Elementos do formulário de cadastro
const formCadastro = document.getElementById('form-cadastro');
const feedbackMessage = document.getElementById('feedback-message');

// Função para exibir mensagens de feedback
function mostrarFeedback(mensagem, tipo) {
    feedbackMessage.textContent = mensagem;
    feedbackMessage.className = `feedback-message ${tipo}`;
    feedbackMessage.style.display = 'block';

    setTimeout(() => {
        feedbackMessage.style.display = 'none';
    }, 3000);
}

// Validação para o nome
function validarNome(nome) {
    const nomeRegex = /^[a-zA-Z\s]{15,60}$/; // 15 a 60 caracteres, alfabéticos e espaços permitidos
    return nomeRegex.test(nome);
}

// Validação para CPF (com ou sem pontuação)
function validarCPF(cpf) {
    const cpfRegex = /^(?:\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/; // Permite CPF com ou sem pontuação
    return cpfRegex.test(cpf);
}

// Validação para celular (padrão de celular no Brasil)
function validarCelular(celular) {
    const celularRegex = /^(?:\(?\d{2}\)?\s?)?\d{5}-\d{4}$/; // Formato (XX) XXXXX-XXXX ou XX XXXXX-XXXX
    return celularRegex.test(celular);
}

// Validação para login (exatamente 6 caracteres alfabéticos)
function validarLogin(login) {
    const loginRegex = /^[a-zA-Z0-9]{1,20}$/; // 1 a 20 caracteres, alfanuméricos
    return loginRegex.test(login);
}

// Validação para senha (1 a 20 caracteres alfanuméricos)
function validarSenha(senha) {
    const senhaRegex = /^[a-zA-Z0-9]{1,20}$/; // 1 a 20 caracteres, alfanuméricos
    return senhaRegex.test(senha);
}

// Função para validar o formulário de cadastro
function validarCadastro() {
    const nomeCompleto = document.getElementById('nome-completo').value.trim();
    const cpf = document.getElementById('cpf').value.trim();
    const celular = document.getElementById('celular').value.trim();
    const login = document.getElementById('login').value.trim();
    const senha = document.getElementById('senha').value.trim();
    const confirmarSenha = document.getElementById('confirmar-senha').value.trim();

    // 1.0 - Cria um objeto usuario com os valores provenientes de cada id
    const usuario = {
        nomeCompleto: nomeCompleto,
        cpf: cpf,
        telefoneCelular: celular,
        login: login,
        senha: senha,
        confirmaSenha: confirmarSenha
    };


// Máscara CPF
function maskCPF(value) {
  return value
    .replace(/\D/g, '')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}
// Máscara Celular (21) 99999-9999
function maskCelular(value) {
  return value
    .replace(/\D/g, '')
    .replace(/(\d{2})(\d)/, '($1) $2')
    .replace(/(\d{5})(\d)/, '$1-$2')
    .replace(/(-\d{4})\d+?$/, '$1');
}

document.addEventListener('DOMContentLoaded', function() {
  const cpf = document.getElementById('cpf');
  const celular = document.getElementById('celular');
  if (cpf) {
    cpf.addEventListener('input', function(e) {
      this.value = maskCPF(this.value);
    });
  }
  if (celular) {
    celular.addEventListener('input', function(e) {
      this.value = maskCelular(this.value);
    });
  }
});


    // Validação do nome
    if (!validarNome(nomeCompleto)) {
        mostrarFeedback('O nome deve ter entre 15 e 60 caracteres alfabéticos, podendo conter espaços.', 'error');
        return false;
    }

    // Validação do CPF
    if (!validarCPF(cpf)) {
        mostrarFeedback('O CPF deve ser válido, com ou sem pontuação.', 'error');
        return false;
    }

    // Validação do celular
    if (!validarCelular(celular)) {
        mostrarFeedback('O celular deve estar no formato (XX) XXXX-XXXX ou (XX) XXXXX-XXXX.', 'error');
        return false;
    }

    // Validação do login
    if (!validarLogin(login)) {
        mostrarFeedback('O login deve ter entre 1 e 20 caracteres alfanuméricos.', 'error');
        return false;
    }

    // Validação da senha
    if (!validarSenha(senha)) {
        mostrarFeedback('A senha deve ter entre 1 e 20 caracteres alfanuméricos.', 'error');
        return false;
    }

    // Verificação das senhas
    if (senha !== confirmarSenha) {
        mostrarFeedback('As senhas não coincidem.', 'error');
        return false;
    }

    // Não armazenar no cliente; o envio será feito pelo formulário ao servidor (PHP)
    return true;
}

// Evento de envio do formulário — apenas validar e permitir envio nativo
if (formCadastro) {
    formCadastro.addEventListener('submit', (event) => {
        if (!validarCadastro()) {
            // impede envio se inválido
            event.preventDefault();
        }
        // se válido, o formulário realiza o POST definido no HTML (php/register.php)
    });
}
