// Adiciona e remove linhas de produto no formulário do frete
document.addEventListener('DOMContentLoaded', function () {
    const lista = document.getElementById('itens');
    const botaoAdicionar = document.getElementById('adicionar-produto');

    botaoAdicionar.addEventListener('click', function () {
        const novaLinha = lista.querySelector('.linha-produto').cloneNode(true);
        novaLinha.querySelector('select').value = '';
        novaLinha.querySelector('input').value = 1;
        lista.appendChild(novaLinha);
    });

    lista.addEventListener('click', function (evento) {
        if (!evento.target.classList.contains('remover-produto')) {
            return;
        }
        if (lista.querySelectorAll('.linha-produto').length > 1) {
            evento.target.closest('.linha-produto').remove();
        } else {
            alert('O frete precisa de pelo menos um produto.');
        }
    });
});