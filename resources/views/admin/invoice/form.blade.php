    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="invoice" id="invoice" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                {{-- <div class="form-group col-md-6 col-sm-12">
                    <label>Serviço</label>
                    <select class="form-control custom-select" name="customer_service_id" id="customer_service_id" {{ isset($data)  ? 'disabled' : '' }}>
                        <option value="">Selecione um serviço</option>
                        @foreach($customer_services as $service)
                            <option {{ isset($data->customer_service_id) && $data->customer_service_id == $service->id ? 'selected' : '' }} value="{{ $service->id }}" data-price={{ $service->price }} data-description={{ $service->description }}>{{ $service->description }}</option>
                        @endforeach
                    </select>
                </div> --}}

                <div class="form-group col-md-12 col-sm-12">
                    <label>Descrição</label>
                    <input type="text" class="form-control" name="description" id="invoice_description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}" {{ isset($data) ? 'disabled' : '' }}>
                </div>


                <div class="form-group col-md-4 col-sm-12">
                    <label>Preço</label>
                    <input type="text" class="form-control money" name="price" id="invoice_price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}" {{ isset($data) && $data->status != 'Erro' ? 'disabled' : '' }}>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Gateway Pagamento</label>
                    @if(isset($data))
                        <select class="form-control custom-select" name="gateway_payment" id="gateway_payment" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                    @else
                        <select class="form-control custom-select" name="gateway_payment" id="gateway_payment">
                    @endif

                        <option value="">Selecione o Gateway de Pagamento</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Estabelecimento' ? 'selected' : '' }} value="Estabelecimento">Estabelecimento</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Pag Hiper' ? 'selected' : '' }} value="Pag Hiper">Pag Hiper</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Mercado Pago' ? 'selected' : '' }} value="Mercado Pago">Mercado Pago</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Intermedium' ? 'selected' : '' }} value="Intermedium">Intermedium</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Asaas' ? 'selected' : '' }} value="Asaas">Asaas</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Forma de Pagamento</label>
                    @if(isset($data))
                    <select class="form-control custom-select" name="payment_method" id="payment_method" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                    @else
                    <select class="form-control custom-select" name="payment_method" id="payment_method">
                    @endif
                        <option value="">Selecione a Forma de Pagamento</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'BoletoPix' ? 'selected' : '' }} value="BoletoPix">BoletoPix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Depósito' ? 'selected' : '' }} value="Depósito">Depósito</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Dinheiro' ? 'selected' : '' }} value="Dinheiro">Dinheiro</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Cartão' ? 'selected' : '' }} value="Cartão">Cartão</option>
                    </select>
                </div>



                <div class="form-group col-md-4 col-sm-12">
                    <label>Data da fatura</label>
                    <input type="date" class="form-control datepicker" name="date_invoice" id="date_invoice" autocomplete="off" required value="{{isset($data->date_invoice) ? $data->date_invoice : ''}}" {{ isset($data) ? 'disabled' : '' }}>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data de vencimento</label>
                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="date_due" id="date_due" autocomplete="off" required value="{{isset($data->date_due) ? $data->date_due : ''}}" {{ isset($data) && $data->status != 'Erro' ? 'disabled' : '' }}>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data de pagamento</label>
                    <input type="date" class="form-control" name="date_payment" id="date_payment" autocomplete="off" required value="{{isset($data->date_payment) ? $data->date_payment : ''}}">
                </div>

                @if($data->gateway_payment == 'Estabelecimento')
                    <div class="form-group col-md-4 col-sm-12">
                        <label>Boleto / <a href="{{$data->billet_url}}" target="_blank">Ver boleto</a></label>
                        <input type="file" class="form-control" name="billet_file" id="billet_file" autocomplete="off" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                    </div>

                    <div class="form-group col-md-8 col-sm-12">
                        <label>Linha digitavel Boleto</label>
                        <input type="text" class="form-control" name="billet_digitable" id="billet_digitable" autocomplete="off" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Pix / <a href="{{$data->billet_url}}" target="_blank">Ver Pix</a></label>
                        <input type="file" class="form-control" name="pix_file" id="pix_file" autocomplete="off" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                    </div>

                    <div class="form-group col-md-8 col-sm-12">
                        <label>Linha digitavel Pix</label>
                        <input type="text" class="form-control" name="pix_digitable" id="pix_digitable" autocomplete="off" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                    </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Status</label>
                    <select class="form-control custom-select" name="status" id="status" {{ $data->status != 'Pendente' && $data->status != 'Erro' ? 'disabled' : ''}}>
                        <option value="">Selecione o status do Pagamento</option>
                        <option value="Pendente"  {{ isset($data->status) && $data->status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        {{-- <option value="Processamento">Processamento</option> --}}
                        <option value="Pago" {{ isset($data->status) && $data->status === 'Pago' ? 'selected' : '' }}>Pago</option>
                        {{-- <option value="Cancelado">Cancelado</option> --}}
                    </select>
                </div>

                @endif

                {{-- @if(!isset($data)) --}}
                <div class="form-group col-md-12 col-sm-12">
                    <div id="group-generate-invoice">
                        @if(isset($data) && $data->status == 'Erro')
                            <div class="form-row">
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="generate_invoice" id="generate_invoice" value="1">
                                        <label class="form-check-label" for="generate_invoice">Marque para <b>Gerar Fatura</b> do serviço</label>
                                    </div>
                            </div>
                        @endif
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
                {{-- @endif --}}

            </div>
        </div>


    </div>
    </div>

    <script>
        $(document).ready(function(){

            @if(!isset($data))
                const checkbox = document.getElementById("generate_invoice");
                checkbox.addEventListener("click", function() {
                if (checkbox.checked) {
                    $('#date_due').show();
                }else{
                    $('#date_due').hide();
                }
                });
            @endif

        $('#gateway_payment').change(function(){
            var escolha = $(this).val();
            console.log(escolha);
            $('#payment_method').empty();
            var payment_method = "{{ isset($data->payment_method) ? $data->payment_method : '' }}";
            // Preenche o segundo select com base na escolha do primeiro select
            if(escolha === 'Estabelecimento'){
                $('#payment_method').append(`<option value="Boleto" ${payment_method == 'Boleto' ? 'selected' : ''}>Boleto</option>`);
                $('#payment_method').append(`<option value="Pix" ${payment_method == 'Pix' ? 'selected' : ''}>Pix</option>`);
                $('#payment_method').append(`<option value="Dinheiro" ${payment_method == 'Dinheiro' ? 'selected' : ''}>Dinheiro</option>`);
                $('#payment_method').append(`<option value="Cartão" ${payment_method == 'Cartão' ? 'selected' : ''}>Cartão</option>`);
            }
            else if(escolha === 'Pag Hiper'){
                $('#payment_method').append(`<option value="Boleto" ${payment_method == 'Boleto' ? 'selected' : ''}>Boleto</option>`);
                $('#payment_method').append(`<option value="Pix" ${payment_method == 'Pix' ? 'selected' : ''}>Pix</option>`);
            }
            else if(escolha === 'Mercado Pago'){
                $('#payment_method').append(`<option value="Pix" ${payment_method == 'Pix' ? 'selected' : ''}>Pix</option>`);
            }
            else if(escolha === 'Intermedium'){
                $('#payment_method').append(`<option value="Boleto" ${payment_method == 'Boleto' ? 'selected' : ''}>Boleto</option>`);
                $('#payment_method').append(`<option value="BoletoPix" ${payment_method == 'BoletoPix' ? 'selected' : ''}>BoletoPix</option>`);
                $('#payment_method').append(`<option value="Pix" ${payment_method == 'Pix' ? 'selected' : ''}>Pix</option>`);
            }
            else if(escolha === 'Asaas'){
                $('#payment_method').append(`<option value="Boleto" ${payment_method == 'Boleto' ? 'selected' : ''}>Boleto</option>`);
                $('#payment_method').append(`<option value="Pix" ${payment_method == 'Pix' ? 'selected' : ''}>Pix</option>`);
            }

        });

        $('#gateway_payment').trigger('change');
    });

        </script>
