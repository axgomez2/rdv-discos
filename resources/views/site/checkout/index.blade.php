<x-app-layout>




<div class="mx-auto max-w-screen-xl px-4 2xl:px-0 py-8">
    <h2 class="text-xl font-semibold text-gray-900 sm:text-2xl">Finalizar Compra</h1>

    @if(session('error'))
        <div class="mt-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
            {{ session('error') }}
        </div>
    @endif

    <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
        <!-- Resumo do Carrinho -->
        <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full lg:max-w-sm">
            <h4 class="d-flex justify-content-between align-items-center mb-4">
                <span>Seu Carrinho</span>
                <span class="badge badge-secondary badge-pill">{{ $cart->items->count() }}</span>
            </h4>
            <ul class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                @foreach($cart->items as $item)
                <li class="flex items-center justify-between pt-3 lh-condensed">
                    <div>
                        <h6 class="my-0">{{ $item->product->name }}</h6>
                        <small class="text-sm text-gray-500">Quantidade: {{ $item->quantity }}</small>
                    </div>
                    <span class="text-sm text-gray-500">R$ {{ number_format($item->quantity * $item->product->price, 2, ',', '.') }}</span>
                </li>
                @endforeach

                <li class="flex items-center justify-between pt-3">
                    <span>Subtotal</span>
                    <strong>R$ {{ number_format($subtotal, 2, ',', '.') }}</strong>
                </li>
                <li class="flex items-center justify-between pt-3">
                    <span>Frete</span>
                    <strong class="shipping-cost">R$ {{ number_format($shippingCost, 2, ',', '.') }}</strong>
                </li>
                <li class="flex items-center justify-between border-t border-gray-200 pt-3">
                    <span class="text-lg font-semibold text-emerald-600">Total</span>
                    <strong class="order-total text-lg font-semibold text-emerald-600">R$ {{ number_format($total, 2, ',', '.') }}</strong>
                </li>
            </ul>
        </div>

        <!-- Formulário de Checkout -->
        <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-3xl">
            <form id="payment-form" action="{{ route('site.checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="card_token" id="card_token" value="">
                <input type="hidden" name="sender_hash" id="sender_hash" value="">
                <input type="hidden" name="shipping_option" id="shipping_option" value="{{ session('shipping_method') }}">

                <!-- Informações de Entrega -->
                <h4 class="mb-4">Informações de Entrega</h4>
                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="col-md-8 mb-4">
                        <label for="endereco">Endereço</label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="endereco" name="endereco" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="numero">Número</label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="numero" name="numero" required>
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="col-md-6 mb-4">
                        <label for="complemento">Complemento</label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="complemento" name="complemento">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="bairro">Bairro</label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="bairro" name="bairro" required>
                    </div>
                </div>

                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="col-md-6 mb-4">
                        <label for="cidade">Cidade</label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cidade" name="cidade" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="estado">Estado</label>
                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="estado" name="estado" required>
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

                <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                    <div class="col-md-6 mb-4">
                        <label for="cep">CEP</label>
                        <div class="input-group">
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cep" name="cep" placeholder="00000-000" required>
                            <div class="input-group-append">
                                <button type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2" id="calcular-frete">Calcular Frete</button>
                            </div>
                        </div>
                        <small class="text-sm text-gray-500">Digite apenas números</small>
                    </div>
                </div>

                <!-- Opções de Envio -->
                <div class="mb-4" id="opcoes-frete">
                    <h4 class="mb-4">Opções de Envio</h4>
                    <div class="mt-4 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50" id="calculando-frete">
                        <i class="fas fa-spinner fa-spin"></i> Calculando opções de frete...
                    </div>
                    <div class="mt-4 p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 d-none" id="erro-frete">
                        Não foi possível calcular o frete. Verifique se o CEP está correto.
                    </div>
                    
                    <!-- As opções de frete serão carregadas aqui via JavaScript -->
                    <div id="lista-opcoes-frete" class="d-none">
                        <!-- As opções de frete serão exibidas aqui -->
                    </div>
                </div>

                <!-- Método de Pagamento -->
                <h4 class="text-xl font-semibold text-gray-900 mb-4">Método de Pagamento</h4>
                
                <!-- Gateways de Pagamento -->
                <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <p class="text-lg font-medium text-gray-900">Selecione um método de pagamento</p>
                    
                    <!-- Container para gateways e métodos -->
                    <div class="space-y-6">
                        @if(isset($paymentGateways['pagseguro']))
                        <div class="border rounded-lg overflow-hidden" id="pagseguro-gateway">
                            <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer" onclick="toggleGatewayMethods('pagseguro')">
                                <div class="flex items-center">
                                    <img src="{{ asset('images/pagseguro-logo.png') }}" alt="PagSeguro" class="h-8 w-auto">
                                </div>
                                <div class="gateway-toggle">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="gateway-methods p-4 space-y-3" id="pagseguro-methods">
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input id="pagseguro_credit_card" name="payment_method" type="radio" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" value="pagseguro_credit_card" data-gateway="pagseguro" data-method="credit_card">
                                    <label for="pagseguro_credit_card" class="ml-3 flex flex-col cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Cartão de Crédito</span>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">Parcele em até 12x</span>
                                    </label>
                                </div>
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input id="pagseguro_boleto" name="payment_method" type="radio" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" value="pagseguro_boleto" data-gateway="pagseguro" data-method="boleto">
                                    <label for="pagseguro_boleto" class="ml-3 flex flex-col cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Boleto Bancário</span>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">Vencimento em 3 dias úteis</span>
                                    </label>
                                </div>
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input id="pagseguro_pix" name="payment_method" type="radio" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" value="pagseguro_pix" data-gateway="pagseguro" data-method="pix">
                                    <label for="pagseguro_pix" class="ml-3 flex flex-col cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">PIX</span>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">Pagamento instantâneo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($paymentGateways['mercadopago']))
                        <div class="border rounded-lg overflow-hidden" id="mercadopago-gateway">
                            <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer" onclick="toggleGatewayMethods('mercadopago')">
                                <div class="flex items-center">
                                    <img src="{{ asset('images/mercadopago-logo.png') }}" alt="MercadoPago" class="h-8 w-auto">
                                </div>
                                <div class="gateway-toggle">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="gateway-methods p-4 space-y-3" id="mercadopago-methods">
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input id="mercadopago_credit_card" name="payment_method" type="radio" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" value="mercadopago_credit_card" data-gateway="mercadopago" data-method="credit_card">
                                    <label for="mercadopago_credit_card" class="ml-3 flex flex-col cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Cartão de Crédito</span>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">Parcele em até 12x</span>
                                    </label>
                                </div>
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input id="mercadopago_boleto" name="payment_method" type="radio" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" value="mercadopago_boleto" data-gateway="mercadopago" data-method="boleto">
                                    <label for="mercadopago_boleto" class="ml-3 flex flex-col cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">Boleto Bancário</span>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">Vencimento em 3 dias úteis</span>
                                    </label>
                                </div>
                                <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <input id="mercadopago_pix" name="payment_method" type="radio" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" value="mercadopago_pix" data-gateway="mercadopago" data-method="pix">
                                    <label for="mercadopago_pix" class="ml-3 flex flex-col cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                            </svg>
                                            <span class="font-medium text-gray-900">PIX</span>
                                        </div>
                                        <span class="text-sm text-gray-500 mt-1">Pagamento instantâneo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <input type="hidden" name="payment_gateway" id="payment_gateway" value="">

                <!-- Informações do Cartão (exibido condicionalmente via JavaScript) -->
                <div id="credit-card-details">
                    <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                        <div class="col-md-6 mb-4">
                            <label for="cc-name">Nome no cartão</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-name" placeholder="">
                            <small class="text-sm text-gray-500">Nome completo como mostrado no cartão</small>
                            <div class="invalid-feedback">
                                Nome no cartão é obrigatório
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="cc-cpf">CPF do titular</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-cpf" placeholder="123.456.789-00">
                            <div class="invalid-feedback">
                                CPF é obrigatório
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                        <div class="col-md-6 mb-4">
                            <label for="cc-birth-date">Data de Nascimento</label>
                            <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-birth-date">
                            <div class="invalid-feedback">
                                Data de nascimento é obrigatória
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="cc-number">Número do cartão</label>
                            <div class="input-group">
                                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-number" placeholder="1234 1234 1234 1234" required>
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
                    <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
                        <div class="col-md-3 mb-4">
                            <label for="cc-expiration-month">Mês</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-expiration-month" required>
                                <option value="">Mês</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">
                                Mês de expiração obrigatório
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="cc-expiration-year">Ano</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-expiration-year" required>
                                <option value="">Ano</option>
                                @for ($i = date('Y'); $i <= date('Y') + 15; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">
                                Ano de expiração obrigatório
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="cc-cvv">CVV</label>
                            <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="cc-cvv" placeholder="123" required>
                            <div class="invalid-feedback">
                                Código de segurança obrigatório
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="installments">Parcelas</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" id="installments" name="installments">
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
                <button class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center btn-lg btn-block" type="submit" id="checkout-button">Finalizar Compra</button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos para opções de frete */
    #lista-opcoes-frete {
        margin-top: 15px;
    }
    
    .shipping-option {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
    }
    
    .shipping-option:hover {
        border-color: #adb5bd;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .shipping-option.selected {
        border-color: #28a745;
        background-color: #f0fff4;
        box-shadow: 0 3px 10px rgba(40,167,69,0.2);
    }
    
    .shipping-option-logo {
        width: 60px;
        margin-right: 15px;
        text-align: center;
    }
    
    .shipping-option-logo i {
        font-size: 28px;
        color: #495057;
    }
    
    .shipping-option-details {
        flex-gmt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8: 1;
    }
    
    .shipping-option-title {
        font-weight: 600;
        margin-bottom: 3px;
    }
    
    .shipping-option-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .shipping-option-price {
        font-weight: 700;
        font-size: 1.1rem;
        color: #212529;
        margin-left: 20px;
        white-space: nowrap;
    }
    
    /* Estilos para métodos de pagamento */
    .payment-methods-container {
        border-radius: 6px;
        overflow: hidden;
    }
    
    .payment-gateway-card {
        border-bottom: 1px solid #e9ecef;
    }
    
    .payment-gateway-card:last-child {
        border-bottom: none;
    }
    
    .gateway-header {
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .gateway-header:hover {
        background-color: #f8f9fa;
    }
    
    .gateway-logo {
        display: flex;
        align-items: center;
    }
    
    .gateway-methods {
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background-color: #f8f9fa;
    }
    
    .gateway-methods.expanded {
        max-height: 500px;
        padding: 10px 15px;
    }
    
    .gateway-method-option {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 8px;
        background-color: #fff;
        transition: all 0.3s;
        position: relative;
    }
    
    .gateway-method-option:hover {
        background-color: #f0f0f0;
    }
    
    .gateway-method-option.selected {
        background-color: #f0fff4;
        border: 1px solid #28a745;
    }
    
    .gateway-method-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .method-label {
        display: flex;
        align-items: center;
        margin: 0;
        cursor: pointer;
        width: 100%;
    }
    
    .method-icon {
        margin-right: 15px;
        font-size: 1.5rem;
        color: #6c757d;
        width: 35px;
        text-align: center;
    }
    
    .method-name {
        font-weight: 600;
        margin-right: 15px;
    }
    
    .method-desc {
        color: #6c757d;
        margin-left: auto;
    }
</style>
@endpush

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
                        const total = subtotal + option.price;
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
                        <span class="text-sm text-gray-500">Entrega em ${option.delivery_time} dia(s) úteis</span>
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
</x-app-layout>
