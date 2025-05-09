    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="customer_service_id" id="customer_service_id" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="customer_id" value="{{ $customer_id }}">

                <div class="form-group col-md-8 col-sm-12">
                    <label>Descrição</label>
                    <input type="text" class="form-control" name="description" id="description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Status</label>
                    <select class="form-control custom-select" name="status" id="status">
                        <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                        <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                    </select>
                </div>

                <div class="form-group col-md-2 col-sm-12">
                    <label>Dia Vencimento</label>
                    <select class="form-control custom-select" name="day_due" id="day_due">
                        <option {{ isset($data->day_due) && $data->day_due === 1 ? 'selected' : '' }} value="1">1</option>
                        <option {{ isset($data->day_due) && $data->day_due === 2 ? 'selected' : '' }} value="2">2</option>
                        <option {{ isset($data->day_due) && $data->day_due === 3 ? 'selected' : '' }} value="3">3</option>
                        <option {{ isset($data->day_due) && $data->day_due === 4 ? 'selected' : '' }} value="4">4</option>
                        <option {{ isset($data->day_due) && $data->day_due === 5 ? 'selected' : '' }} value="5">5</option>
                        <option {{ isset($data->day_due) && $data->day_due === 6 ? 'selected' : '' }} value="6">6</option>
                        <option {{ isset($data->day_due) && $data->day_due === 7 ? 'selected' : '' }} value="7">7</option>
                        <option {{ isset($data->day_due) && $data->day_due === 8 ? 'selected' : '' }} value="8">8</option>
                        <option {{ isset($data->day_due) && $data->day_due === 9 ? 'selected' : '' }} value="9">9</option>
                        <option {{ isset($data->day_due) && $data->day_due === 10 ? 'selected' : '' }} value="10">10</option>
                        <option {{ isset($data->day_due) && $data->day_due === 11 ? 'selected' : '' }} value="11">11</option>
                        <option {{ isset($data->day_due) && $data->day_due === 12 ? 'selected' : '' }} value="12">12</option>
                        <option {{ isset($data->day_due) && $data->day_due === 13 ? 'selected' : '' }} value="13">13</option>
                        <option {{ isset($data->day_due) && $data->day_due === 14 ? 'selected' : '' }} value="14">14</option>
                        <option {{ isset($data->day_due) && $data->day_due === 15 ? 'selected' : '' }} value="15">15</option>
                        <option {{ isset($data->day_due) && $data->day_due === 16 ? 'selected' : '' }} value="16">16</option>
                        <option {{ isset($data->day_due) && $data->day_due === 17 ? 'selected' : '' }} value="17">17</option>
                        <option {{ isset($data->day_due) && $data->day_due === 18 ? 'selected' : '' }} value="18">18</option>
                        <option {{ isset($data->day_due) && $data->day_due === 19 ? 'selected' : '' }} value="19">19</option>
                        <option {{ isset($data->day_due) && $data->day_due === 20 ? 'selected' : '' }} value="20">20</option>
                        <option {{ isset($data->day_due) && $data->day_due === 21 ? 'selected' : '' }} value="21">21</option>
                        <option {{ isset($data->day_due) && $data->day_due === 22 ? 'selected' : '' }} value="22">22</option>
                        <option {{ isset($data->day_due) && $data->day_due === 23 ? 'selected' : '' }} value="23">23</option>
                        <option {{ isset($data->day_due) && $data->day_due === 24 ? 'selected' : '' }} value="24">24</option>
                        <option {{ isset($data->day_due) && $data->day_due === 25 ? 'selected' : '' }} value="25">25</option>
                        <option {{ isset($data->day_due) && $data->day_due === 26 ? 'selected' : '' }} value="26">26</option>
                        <option {{ isset($data->day_due) && $data->day_due === 27 ? 'selected' : '' }} value="27">27</option>
                        <option {{ isset($data->day_due) && $data->day_due === 28 ? 'selected' : '' }} value="28">28</option>
                        <option {{ isset($data->day_due) && $data->day_due === 29 ? 'selected' : '' }} value="29">29</option>
                        <option {{ isset($data->day_due) && $data->day_due === 30 ? 'selected' : '' }} value="30">30</option>
                        <option {{ isset($data->day_due) && $data->day_due === 31 ? 'selected' : '' }} value="31">31</option>

                    </select>
                </div>

                <div class="form-group col-md-2 col-sm-12">
                    <label>Valor</label>
                    <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Gateway Pagamento</label>
                    <select class="form-control custom-select" name="gateway_payment" id="gateway_payment">
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
                    <select class="form-control custom-select" name="payment_method" id="payment_method">
                        <option value="">Selecione a Forma de Pagamento</option>
                        {{-- <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'BoletoPix' ? 'selected' : '' }} value="BoletoPix">BoletoPix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Depósito' ? 'selected' : '' }} value="Depósito">Depósito</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Dinheiro' ? 'selected' : '' }} value="Dinheiro">Dinheiro</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Cartão' ? 'selected' : '' }} value="Cartão">Cartão</option> --}}
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data Inicio Cobrança <i class="far fa-question-circle" data-original-title="Gerar cobrança a partir da data definida." data-placement="right" data-tt="tooltip"></i></label>
                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="start_billing" id="start_billing" autocomplete="off" value="{{isset($data->start_billing) ? $data->start_billing : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data Fim Cobrança <i class="far fa-question-circle" data-original-title="Gerar cobrança até a data definida." data-placement="right" data-tt="tooltip"></i></label>
                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="end_billing" id="end_billing" autocomplete="off" value="{{isset($data->end_billing) ? $data->end_billing : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Período</label>
                    <select class="form-control custom-select" name="period" id="period">
                        <option value="">Selecione o Período</option>
                        <option {{ isset($data->period) && $data->period === 'Recorrente' ? 'selected' : '' }} value="Recorrente">Recorrente</option>
                        <option {{ isset($data->period) && $data->period === 'Único' ? 'selected' : '' }} value="Único">Único</option>
                    </select>
                </div>



                {{-- @if(!isset($data)) --}}
                <div class="form-group col-md-12 col-sm-12">

                    <div id="group-generate-invoice">
                    <div class="form-row">
                      <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="generate_invoice" id="generate_invoice" value="1">
                        <label class="form-check-label" for="generate_invoice">Marque para <b>Gerar Fatura</b> do serviço</label>
                      </div>
                      <div class="form-group col-md-12 col-sm-12" id="date_due" style="display: none;">
                        <div class="form-group col-md-4 col-sm-12">
                        <label>Data de vencimento</label>
                        <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="date_due" id="date_due" autocomplete="off"  value="">
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
                {{-- @endif --}}

            </div>
        </div>


    </div>
    </div>

    <script>
    $(document).ready(function(){


            const checkbox = document.getElementById("generate_invoice");
            checkbox.addEventListener("click", function() {
            if (checkbox.checked) {
                $('#date_due').show();
            }else{
                $('#date_due').hide();
            }
            });


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
