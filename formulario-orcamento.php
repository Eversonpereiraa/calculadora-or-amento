<?php
/*
Plugin Name: Formulário de Orçamento
Description: Um plugin para criar um formulário de orçamento em 3 etapas.
Version: 1.0
Author: Seu Nome
*/

function formulario_orcamento_shortcode() {
    ob_start();
    ?>
    <div id="orcamento-form">
      <!-- Etapa 1 -->
      <div id="step-1">
        <h3>Etapa 1: Quantidade e Tamanho</h3>
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" required>
        
        <label for="tamanho">Tamanho:</label>
        <select id="tamanho" name="tamanho" required>
          <option value="" disabled selected>Selecione</option>
          <option value="4x2">4x2</option>
          <option value="2x2">2x2</option>
          <option value="4x4">4x4</option>
        </select>
        
        <button type="button" onclick="nextStep(1)">Próximo</button>
      </div>

      <!-- Etapa 2 -->
      <div id="step-2" style="display:none;">
        <h3>Etapa 2: Cor</h3>
        <label for="cor">Cor:</label>
        <select id="cor" name="cor" required>
          <option value="" disabled selected>Selecione</option>
          <option value="Preto">Preto</option>
          <option value="Vermelho">Vermelho</option>
        </select>
        
        <button type="button" onclick="nextStep(2)">Próximo</button>
      </div>

      <!-- Etapa 3 -->
      <div id="step-3" style="display:none;">
        <h3>Etapa 3: Categoria</h3>
        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria" required>
          <option value="" disabled selected>Selecione</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
        </select>
        
        <button type="button" onclick="finish()">Finalizar</button>
      </div>

      <div id="orcamento-results"></div>
      <div id="buttons" style="display:none;">
        <button type="button" onclick="addProduct()">Adicionar Produto</button>
        <button type="button" onclick="conclude()">Concluir</button>
      </div>
    </div>
    
    <script>
    let formData = [];

    function nextStep(currentStep) {
      if (!validateStep(currentStep)) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
      }
      document.getElementById(`step-${currentStep}`).style.display = 'none';
      document.getElementById(`step-${currentStep + 1}`).style.display = 'block';
    }

    function validateStep(step) {
      let valid = true;
      const fields = document.querySelectorAll(`#step-${step} [required]`);
      fields.forEach(field => {
        if (!field.value) {
          valid = false;
          field.classList.add('error');
        } else {
          field.classList.remove('error');
        }
      });
      return valid;
    }

    function finish() {
      if (!validateStep(3)) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
      }
      
      const quantidade = document.getElementById('quantidade').value;
      const tamanho = document.getElementById('tamanho').value;
      const cor = document.getElementById('cor').value;
      const categoria = document.getElementById('categoria').value;
      
      // Adiciona os dados ao array de formData
      formData.push({ quantidade, tamanho, cor, categoria });
      
      // Exibe a tabela com os dados
      displayTable();
    }

    function calculatePrice(quantidade, tamanho, cor, categoria) {
      // Preços fictícios para cada atributo
      const tamanhoPrices = {
        '4x2': 10,
        '2x2': 15,
        '4x4': 20
      };
      const corPrices = {
        'Preto': 5,
        'Vermelho': 8
      };
      const categoriaPrices = {
        'A': 25,
        'B': 30,
        'C': 35
      };

      const sizePrice = tamanhoPrices[tamanho] || 0;
      const colorPrice = corPrices[cor] || 0;
      const categoryPrice = categoriaPrices[categoria] || 0;

      // Calcula o preço total
      return (sizePrice + colorPrice + categoryPrice) * quantidade;
    }

    function displayTable() {
      const table = document.getElementById('orcamento-results');
      let tableHtml = `
        <table border="1">
          <tr>
            <th>Quantidade</th>
            <th>Tamanho</th>
            <th>Cor</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Excluir</th>
          </tr>
      `;
      let total = 0;
      
      formData.forEach((data, index) => {
        const price = calculatePrice(data.quantidade, data.tamanho, data.cor, data.categoria);
        total += price;
        
        tableHtml += `
          <tr id="row-${index}">
            <td>${data.quantidade}</td>
            <td>${data.tamanho}</td>
            <td>${data.cor}</td>
            <td>${data.categoria}</td>
            <td>${price.toFixed(2)} R$</td>
            <td><button onclick="deleteRow(${index})">Excluir</button></td>
          </tr>
        `;
      });
      
      tableHtml += `
        <tr>
          <td colspan="5">Total</td>
          <td>${total.toFixed(2)} R$</td>
        </tr>
      </table>
      `;
      
      // Adiciona o HTML da tabela ao DOM
      table.innerHTML = tableHtml;
      
      // Mostra os botões de concluir e adicionar produto
      document.getElementById('buttons').style.display = 'block';
    }

    function deleteRow(index) {
      // Remove o item do array formData
      formData.splice(index, 1);
      
      // Recalcula os preços e exibe a tabela novamente
      displayTable();
    }

    function addProduct() {
      // Reinicia o formulário
      document.getElementById('step-1').style.display = 'block';
      document.getElementById('step-2').style.display = 'none';
      document.getElementById('step-3').style.display = 'none';
      
      // Oculta os botões de concluir e adicionar produto
      document.getElementById('buttons').style.display = 'none';
      
      // Limpa os campos do formulário
      document.getElementById('quantidade').value = '';
      document.getElementById('tamanho').value = '';
      document.getElementById('cor').value = '';
      document.getElementById('categoria').value = '';
    }

    function conclude() {
      alert('Orçamento concluído!');
    }
    </script>
    <style>
      .error {
        border: 2px solid red;
      }
    </style>
    <?php
    return ob_get_clean();
}

add_shortcode('formulario_orcamento', 'formulario_orcamento_shortcode');

function formulario_orcamento_enqueue_styles() {
    wp_enqueue_style('formulario-orcamento-style', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'formulario_orcamento_enqueue_styles');
