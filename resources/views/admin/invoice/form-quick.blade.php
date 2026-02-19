<div class="col-md-12">
    <div class="form-row">
        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="date_invoice" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="status_invoice" value="Pendente">

                <div class="form-group col-md-6 col-sm-12">
                    <label>Empresa Emissora <span class="text-danger">*</span></label>
                    <select class="form-control compact-input" name="company_id" id="company_id" required>
                        <option value="">Selecione a empresa emissora...</option>
                        @foreach($companies ?? [] as $comp)
                            <option value="{{ $comp->id }}" {{ auth()->user()->current_company_id == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Empresa que irá emitir a fatura</small>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Cliente (Empresa a ser Cobrada) <span class="text-danger">*</span></label>
                    <select class="form-control compact-input" name="customer_id" id="customer_id_select" required>
                        <option value="">Selecione o cliente...</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Cliente que receberá a cobrança</small>
                </div>

                <div class="form-group col-md-12 col-sm-12">
                    <div class="alert alert-info" style="padding: 10px; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> Um novo serviço será criado para este cliente. 
                        Se marcar "Gerar Fatura", a fatura será criada imediatamente. Caso contrário, será gerada automaticamente no período configurado.
                    </div>
                </div>

                <div class="form-group col-md-8 col-sm-12">
                    <label>Descrição (Serviço) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control compact-input" name="description" id="description" autocomplete="off" required>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Status</label>
                    <select class="form-control custom-select compact-input" name="status" id="status">
                        <option value="Ativo" selected>Ativo</option>
                        <option value="Inativo">Inativo</option>
                    </select>
                </div>

                <div class="form-group col-md-2 col-sm-12">
                    <label>Dia Vencimento</label>
                    <select class="form-control custom-select" name="day_due" id="day_due">
                        @for($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group col-md-2 col-sm-12">
                    <label>Valor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Gateway Pagamento <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="gateway_payment" id="gateway_payment" required>
                        <option value="">Selecione o Gateway de Pagamento</option>
                        <option value="Estabelecimento">Estabelecimento</option>
                        <option value="Pag Hiper">Pag Hiper</option>
                        <option value="Mercado Pago">Mercado Pago</option>
                        <option value="Intermedium">Intermedium</option>
                        <option value="Asaas">Asaas</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Forma de Pagamento <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="payment_method" id="payment_method" required>
                        <option value="">Selecione a Forma de Pagamento</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data Inicio Cobrança <i class="far fa-question-circle" data-original-title="Gerar cobrança a partir da data definida." data-placement="right" data-tt="tooltip"></i></label>
                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="start_billing" id="start_billing" autocomplete="off">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data Fim Cobrança <i class="far fa-question-circle" data-original-title="Gerar cobrança até a data definida." data-placement="right" data-tt="tooltip"></i></label>
                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="end_billing" id="end_billing" autocomplete="off">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Período</label>
                    <select class="form-control custom-select" name="period" id="period">
                        <option value="">Selecione o Período</option>
                        <option value="Recorrente">Recorrente</option>
                        <option value="Único">Único</option>
                    </select>
                </div>

                <!-- Opções de Gerar Fatura -->
                <div class="form-group col-md-12 col-sm-12">
                    <div id="group-generate-invoice">
                        <div class="form-row">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="generate_invoice" id="generate_invoice" value="1">
                                <label class="form-check-label" for="generate_invoice">Marque para <b>Gerar Fatura</b> do serviço</label>
                            </div>
                            <div class="form-group col-md-12 col-sm-12" id="date_due_group" style="display: none;">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>Data de vencimento</label>
                                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="date_due" id="date_due" autocomplete="off" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="send_invoice_email" id="send_invoice_email" value="1">
                                <label class="form-check-label" for="send_invoice_email">Marque para <b>Enviar Fatura</b> por email</label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="send_invoice_whatsapp" id="send_invoice_whatsapp" value="1">
                                <label class="form-check-label" for="send_invoice_whatsapp">Marque para <b>Enviar Fatura</b> por whatsapp</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Executar código imediatamente (não precisa esperar document ready pois é carregado via AJAX)
(function(){
    
    // Aplicar máscara de moeda
    if(typeof $.fn.mask !== 'undefined'){
        $('.money').mask('000.000.000.000.000,00', {reverse: true});
    }
    
    // Mostrar campo de data de vencimento ao marcar gerar fatura
    $('#generate_invoice').change(function(){
        if($(this).is(':checked')){
            $('#date_due_group').show();
            $('#date_due').prop('required', true);
        } else {
            $('#date_due_group').hide();
            $('#date_due').prop('required', false);
        }
    });
    
    // Gateway payment change
    $('#gateway_payment').change(function(){
        var escolha = $(this).val();
        $('#payment_method').empty();
        
        if(escolha === 'Estabelecimento'){
            $('#payment_method').append('<option value="Boleto">Boleto</option>');
            $('#payment_method').append('<option value="Pix">Pix</option>');
            $('#payment_method').append('<option value="Dinheiro">Dinheiro</option>');
            $('#payment_method').append('<option value="Cartão">Cartão</option>');
        }
        else if(escolha === 'Pag Hiper'){
            $('#payment_method').append('<option value="Boleto">Boleto</option>');
            $('#payment_method').append('<option value="Pix">Pix</option>');
        }
        else if(escolha === 'Mercado Pago'){
            $('#payment_method').append('<option value="Pix">Pix</option>');
        }
        else if(escolha === 'Intermedium'){
            $('#payment_method').append('<option value="Boleto">Boleto</option>');
            $('#payment_method').append('<option value="BoletoPix">BoletoPix</option>');
            $('#payment_method').append('<option value="Pix">Pix</option>');
        }
        else if(escolha === 'Asaas'){
            $('#payment_method').append('<option value="Boleto">Boleto</option>');
            $('#payment_method').append('<option value="Pix">Pix</option>');
        }
    });
    
    // Trigger inicial para carregar as opções de pagamento
    setTimeout(function(){
        $('#gateway_payment').trigger('change');
    }, 100);
    
})(); // Fim da função auto-executável
</script>
