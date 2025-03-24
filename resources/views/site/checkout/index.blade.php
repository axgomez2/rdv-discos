@extends('layouts.site')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Finalizar Compra</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Resumo do Carrinho -->
        <div class="col-md-5 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span>Seu Carrinho</span>
                <span class="badge badge-secondary badge-pill">{{ $cart->items->count() }}</span>
            </h4>
            <ul class="list-group mb-3">
                @foreach($cart->items as $item)
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">{{ $item->product->name }}</h6>
                        <small class="text-muted">Quantidade: {{ $item->quantity }}</small>
                    </div>
                    <span class="text-muted">R$ {{ number_format($item->quantity * $item->product->price, 2, ',', '.') }}</span>
                </li>
                @endforeach

                <li class="list-group-item d-flex justify-content-between">
                    <span>Subtotal</span>
                    <strong>R$ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Frete</span>
                    <strong class="shipping-cost">R$ {{ number_format($shippingCost, 2, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Impostos</span>
                    <strong>R$ {{ number_format($tax, 2, ',', '.') }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <span class="text-success">Total</span>
                    <strong class="order-total text-success">R$ {{ number_format($total, 2, ',', '.') }}</strong>
                </li>
            </ul>
        </div>

        <!-- Formulário de Checkout -->
        <div class="col-md-7 order-md-1">
            <form id="payment-form" action="{{ route('site.checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="card_token" id="card_token" value="">
                <input type="hidden" name="sender_hash" id="sender_hash" value="">
                <input type="hidden" name="shipping_option" id="shipping_option" value="{{ session('shipping_method') }}">

                <!-- Informações de Entrega -->
                <h4 class="mb-3">Informações de Entrega</h4>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="endereco">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="numero">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="complemento">Complemento</label>
                        <input type="text" class="form-control" id="complemento" name="complemento">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bairro">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cidade">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="">Selecione...</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cep">CEP</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" id="calcular-frete">Calcular Frete</button>
                            </div>
                        </div>
                        <small class="text-muted">Digite apenas números</small>
                    </div>
                </div>

                <!-- Opções de Envio -->
                <div class="mb-4" id="opcoes-frete">
                    <h5 class="mb-3">Opções de Envio</h5>
                    <div class="alert alert-info d-none" id="calculando-frete">
                        <i class="fas fa-spinner fa-spin"></i> Calculando opções de frete...
                    </div>
                    <div class="alert alert-warning d-none" id="erro-frete">
                        Não foi possível calcular o frete. Verifique se o CEP está correto.
                    </div>
                    
                    <!-- As opções de frete serão carregadas aqui via JavaScript -->
                    <div id="lista-opcoes-frete" class="d-none">
                        <!-- Template para opções de frete -->
                    </div>
                </div>

                <!-- Método de Pagamento -->
                <h4 class="mb-3">Método de Pagamento</h4>
                <div class="d-block my-3">
                    <div class="form-check">
                        <input id="credit_card" name="payment_method" type="radio" class="form-check-input" value="credit_card" checked required>
                        <label class="form-check-label" for="credit_card">Cartão de Crédito</label>
                    </div>
                    <div class="form-check">
                        <input id="boleto" name="payment_method" type="radio" class="form-check-input" value="boleto" required>
                        <label class="form-check-label" for="boleto">Boleto Bancário</label>
                    </div>
                    <div class="form-check">
                        <input id="pix" name="payment_method" type="radio" class="form-check-input" value="pix" required>
                        <label class="form-check-label" for="pix">PIX</label>
                    </div>
                </div>

                <!-- Informações do Cartão (exibido condicionalmente via JavaScript) -->
                <div id="credit-card-details">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cc-name">Nome no cartão</label>
                            <input type="text" class="form-control" id="cc-name" placeholder="">
                            <small class="text-muted">Nome completo como mostrado no cartão</small>
                            <div class="invalid-feedback">
                                Nome no cartão é obrigatório
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cc-cpf">CPF do titular</label>
                            <input type="text" class="form-control" id="cc-cpf" placeholder="123.456.789-00">
                            <div class="invalid-feedback">
                                CPF é obrigatório
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cc-birth-date">Data de Nascimento</label>
                            <input type="date" class="form-control" id="cc-birth-date">
                            <div class="invalid-feedback">
                                Data de nascimento é obrigatória
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cc-number">Número do cartão</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cc-number" placeholder="1234 1234 1234 1234" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i id="card-brand-icon" class="fas fa-credit-card"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                Número do cartão é obrigatório
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cc-expiration-month">Mês</label>
                            <select class="form-control" id="cc-expiration-month" required>
                                <option value="">Mês</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">
                                Mês de expiração obrigatório
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cc-expiration-year">Ano</label>
                            <select class="form-control" id="cc-expiration-year" required>
                                <option value="">Ano</option>
                                @for ($i = date('Y'); $i <= date('Y') + 15; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">
                                Ano de expiração obrigatório
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cc-cvv">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" placeholder="123" required>
                            <div class="invalid-feedback">
                                Código de segurança obrigatório
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="installments">Parcelas</label>
                            <select class="form-control" id="installments" name="installments">
                                <option value="1">1x de R$ {{ number_format($total, 2, ',', '.') }}</option>
                                <!-- As demais parcelas serão preenchidas via JavaScript -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos para armazenar informações do cartão -->
                <input type="hidden" name="card_holder_name" id="card_holder_name">
                <input type="hidden" name="card_holder_cpf" id="card_holder_cpf">
                <input type="hidden" name="card_holder_birth_date" id="card_holder_birth_date">

                <!-- Botão de Finalizar Compra -->
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" type="submit" id="checkout-button">Finalizar Compra</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Script do PagSeguro -->
<script type="text/javascript" src="{{ config('pagseguro.sandbox') ? 'https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js' : 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js' }}"></script>
<!-- Script do MercadoPago -->
<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicialização do PagSeguro
        PagSeguroDirectPayment.setSessionId('{{ $pagSeguroSessionId }}');

        // Elementos do formulário de frete
        const cepInput = document.getElementById('cep');
        const calcularFreteBtn = document.getElementById('calcular-frete');
        const calculandoFreteAlert = document.getElementById('calculando-frete');
        const erroFreteAlert = document.getElementById('erro-frete');
        const listaOpcoesFrete = document.getElementById('lista-opcoes-frete');
        const shippingOptionInput = document.getElementById('shipping_option');
        
        // Formatar input de CEP
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) value = value.substring(0, 8);
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5);
            }
            e.target.value = value;
        });
        
        // Calcular frete quando clicar no botão
        calcularFreteBtn.addEventListener('click', function() {
            const cep = cepInput.value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('Por favor, digite um CEP válido com 8 dígitos.');
                return;
            }
            
            // Mostrar loading
            calculandoFreteAlert.classList.remove('d-none');
            listaOpcoesFrete.classList.add('d-none');
            erroFreteAlert.classList.add('d-none');
            
            // Fazer requisição para o backend
            fetch('{{ route("site.checkout.calcular-frete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    cep: cep
                })
            })
            .then(response => response.json())
            .then(data => {
                // Esconder loading
                calculandoFreteAlert.classList.add('d-none');
                
                if (!data.success || !data.options || Object.keys(data.options).length === 0) {
                    erroFreteAlert.classList.remove('d-none');
                    return;
                }
                
                // Limpar e mostrar opções de frete
                listaOpcoesFrete.innerHTML = '';
                listaOpcoesFrete.classList.remove('d-none');
                
                // Adicionar cada opção de frete como um radio button
                Object.keys(data.options).forEach(key => {
                    const option = data.options[key];
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'form-check mb-2 p-2 border rounded';
                    
                    const radioInput = document.createElement('input');
                    radioInput.type = 'radio';
                    radioInput.className = 'form-check-input';
                    radioInput.name = 'frete_option';
                    radioInput.id = 'frete_' + key;
                    radioInput.value = key;
                    radioInput.dataset.price = option.price;
                    
                    // Se tiver um método já selecionado na sessão, selecionar ele
                    if (key === '{{ session("shipping_method") }}') {
                        radioInput.checked = true;
                    }
                    
                    radioInput.addEventListener('change', function() {
                        // Atualizar valor do frete no resumo do pedido
                        document.querySelector('.shipping-cost').innerText = 'R$ ' + option.price.toFixed(2).replace('.', ',');
                        
                        // Atualizar valor total
                        const subtotal = parseFloat('{{ $subtotal }}');
                        const tax = parseFloat('{{ $tax }}');
                        const total = subtotal + option.price + tax;
                        document.querySelector('.order-total').innerText = 'R$ ' + total.toFixed(2).replace('.', ',');
                        
                        // Atualizar o input hidden
                        shippingOptionInput.value = key;
                        
                        // Salvar a opção selecionada via AJAX
                        fetch('{{ route("site.checkout.calcular-frete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                cep: cep,
                                selected_method: key
                            })
                        });
                    });
                    
                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = 'frete_' + key;
                    label.innerHTML = `
                        <strong>${option.name}</strong> - ${option.company} <br>
                        <span class="text-muted">Entrega em ${option.delivery_time} dia(s) úteis</span>
                        <span class="badge badge-success float-right">R$ ${option.price.toFixed(2).replace('.', ',')}</span>
                    `;
                    
                    optionDiv.appendChild(radioInput);
                    optionDiv.appendChild(label);
                    listaOpcoesFrete.appendChild(optionDiv);
                });
                
                // Se não tiver nenhuma opção selecionada, selecionar a primeira
                if (!document.querySelector('input[name="frete_option"]:checked') && listaOpcoesFrete.children.length > 0) {
                    const firstOption = document.querySelector('input[name="frete_option"]');
                    firstOption.checked = true;
                    firstOption.dispatchEvent(new Event('change'));
                }
            })
            .catch(error => {
                console.error('Erro ao calcular frete:', error);
                calculandoFreteAlert.classList.add('d-none');
                erroFreteAlert.classList.remove('d-none');
            });
        });
        
        // Se já tiver um CEP preenchido, calcular o frete automaticamente
        if (cepInput.value && cepInput.value.replace(/\D/g, '').length === 8) {
            calcularFreteBtn.click();
        }
        
        // Elementos do formulário de pagamento
        const paymentForm = document.getElementById('payment-form');
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        const cardDetails = document.getElementById('credit-card-details');

        // Campos do cartão
        const cardNumber = document.getElementById('cc-number');
        const cardBrandIcon = document.getElementById('card-brand-icon');
        const cardExpirationMonth = document.getElementById('cc-expiration-month');
        const cardExpirationYear = document.getElementById('cc-expiration-year');
        const cardCvv = document.getElementById('cc-cvv');
        const cardHolderName = document.getElementById('cc-name');
        const cardHolderCpf = document.getElementById('cc-cpf');
        const cardHolderBirthDate = document.getElementById('cc-birth-date');
        const installmentsSelect = document.getElementById('installments');

        // Campos ocultos
        const cardTokenInput = document.getElementById('card_token');
        const senderHashInput = document.getElementById('sender_hash');
        const cardHolderNameInput = document.getElementById('card_holder_name');
        const cardHolderCpfInput = document.getElementById('card_holder_cpf');
        const cardHolderBirthDateInput = document.getElementById('card_holder_birth_date');

        // Alternar visibilidade do formulário de cartão
        function toggleCardDetails() {
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            cardDetails.style.display = selectedPaymentMethod === 'credit_card' ? 'block' : 'none';
        }

        // Detectar bandeira do cartão
        cardNumber.addEventListener('input', function() {
            if (cardNumber.value.length >= 6) {
                PagSeguroDirectPayment.getBrand({
                    cardBin: cardNumber.value.replace(/\D/g, '').substring(0, 6),
                    success: function(response) {
                        const brand = response.brand.name;
                        cardBrandIcon.className = `fab fa-cc-${brand.toLowerCase()}`;

                        // Obter parcelas disponíveis
                        getInstallments(brand);
                    },
                    error: function(error) {
                        console.error('Erro ao detectar bandeira do cartão:', error);
                        cardBrandIcon.className = 'fas fa-credit-card';
                    }
                });
            } else {
                cardBrandIcon.className = 'fas fa-credit-card';
            }
        });

        // Obter parcelas disponíveis
        function getInstallments(brand) {
            PagSeguroDirectPayment.getInstallments({
                amount: {{ $total }},
                brand: brand,
                maxInstallmentNoInterest: 3, // Até 3x sem juros
                success: function(response) {
                    // Limpar opções atuais
                    installmentsSelect.innerHTML = '';

                    // Adicionar novas opções
                    response.installments[brand].forEach(function(option) {
                        const installmentText = option.quantity === 1
                            ? `${option.quantity}x de R$ ${option.installmentAmount.toFixed(2).replace('.', ',')} (à vista)`
                            : `${option.quantity}x de R$ ${option.installmentAmount.toFixed(2).replace('.', ',')}${option.interestFree ? ' sem juros' : ' com juros'}`;

                        const opt = document.createElement('option');
                        opt.value = option.quantity;
                        opt.textContent = installmentText;
                        installmentsSelect.appendChild(opt);
                    });
                },
                error: function(error) {
                    console.error('Erro ao obter parcelas:', error);
                }
            });
        }

        // Gerar token do cartão
        function generateCardToken() {
            const cardData = {
                cardNumber: cardNumber.value.replace(/\D/g, ''),
                cvv: cardCvv.value,
                expirationMonth: cardExpirationMonth.value,
                expirationYear: cardExpirationYear.value,
                success: function(response) {
                    cardTokenInput.value = response.card.token;

                    // Armazenar informações do titular
                    cardHolderNameInput.value = cardHolderName.value;
                    cardHolderCpfInput.value = cardHolderCpf.value;
                    cardHolderBirthDateInput.value = cardHolderBirthDate.value;

                    // Obter o hash do comprador
                    getSenderHash();
                },
                error: function(error) {
                    console.error('Erro ao gerar token do cartão:', error);
                    alert('Ocorreu um erro ao processar os dados do cartão. Por favor, verifique os dados e tente novamente.');
                }
            };

            PagSeguroDirectPayment.createCardToken(cardData);
        }

        // Obter hash do comprador
        function getSenderHash() {
            senderHashInput.value = PagSeguroDirectPayment.getSenderHash();

            // Enviar formulário
            paymentForm.submit();
        }

        // Adicionar listeners
        paymentMethodRadios.forEach(function(radio) {
            radio.addEventListener('change', toggleCardDetails);
        });

        // Submissão do formulário
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (selectedPaymentMethod === 'credit_card') {
                // Validar campos do cartão
                if (!cardNumber.value || !cardExpirationMonth.value || !cardExpirationYear.value || !cardCvv.value || !cardHolderName.value || !cardHolderCpf.value || !cardHolderBirthDate.value) {
                    alert('Por favor, preencha todos os campos do cartão de crédito.');
                    return;
                }

                // Gerar token do cartão
                generateCardToken();
            } else {
                // Para boleto ou PIX, apenas enviar o formulário
                paymentForm.submit();
            }
        });

        // Inicializações
        toggleCardDetails();
    });
</script>
@endpush
@endsection
