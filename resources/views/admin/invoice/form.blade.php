    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="invoice" id="invoice" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                <div class="form-group col-md-6 col-sm-12">
                    <label>Serviço</label>
                    <select class="form-control custom-select" name="customer_service_id" id="customer_service_id" {{ isset($data) ? 'disabled' : '' }}>
                        <option value="">Selecione um serviço</option>
                        @foreach($customer_services as $service)
                            <option {{ isset($data->customer_service_id) && $data->customer_service_id == $service->id ? 'selected' : '' }} value="{{ $service->id }}" data-price={{ $service->price }} data-description={{ $service->description }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Descrição</label>
                    <input type="text" class="form-control" name="description" id="invoice_description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}" {{ isset($data) ? 'disabled' : '' }}>
                </div>


                <div class="form-group col-md-4 col-sm-12">
                    <label>Preço</label>
                    <input type="text" class="form-control money" name="price" id="invoice_price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}" {{ isset($data) ? 'disabled' : '' }}>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Gateway Pagamento</label>
                    @if(isset($data))
                        <select class="form-control custom-select" name="gateway_payment" id="gateway_payment" {{ $data->status != 'Pendente' ? 'disabled' : ''}}>
                    @else
                        <select class="form-control custom-select" name="gateway_payment" id="gateway_payment">
                    @endif

                        <option value="">Selecione o Gateway de Pagamento</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Estabelecimento' ? 'selected' : '' }} value="Estabelecimento">Estabelecimento</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Pag Hiper' ? 'selected' : '' }} value="Pag Hiper">Pag Hiper</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Mercado Pago' ? 'selected' : '' }} value="Mercado Pago">Mercado Pago</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Intermedium' ? 'selected' : '' }} value="Intermedium">Intermedium</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Cora' ? 'selected' : '' }} value="Cora">Cora(em breve)</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Forma de Pagamento</label>
                    @if(isset($data))
                    <select class="form-control custom-select" name="payment_method" id="payment_method" {{ $data->status != 'Pendente' ? 'disabled' : ''}}>
                    @else
                    <select class="form-control custom-select" name="payment_method" id="payment_method">
                    @endif
                        <option value="">Selecione a Forma de Pagamento</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
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
                    <input type="date" class="form-control" name="date_due" id="date_due" autocomplete="off" required value="{{isset($data->date_due) ? $data->date_due : ''}}" {{ isset($data) ? 'disabled' : '' }}>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data de pagamento</label>
                    <input type="date" class="form-control" name="date_payment" id="date_payment" autocomplete="off" required value="{{isset($data->date_payment) ? $data->date_payment : ''}}">
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Status</label>
                    <select class="form-control custom-select" name="status" id="status">
                        <option value="">Selecione o status do Pagamento</option>
                        <option value="Pendente">Pendente</option>
                        <option value="Processamento">Processamento</option>
                        <option value="Pago">Pago</option>
                        <option value="Cancelado">Cancelado</option>

                        {{-- <option {{ isset($data->status) && $data->status === 'Pendente' ? 'selected' : '' }} value="Pendente">Pendente</option>
                        <option {{ isset($data->status) && $data->status === 'Processamento' ? 'selected' : '' }} value="Processamento">Processamento</option>
                        <option {{ isset($data->status) && $data->status === 'Pago' ? 'selected' : '' }} value="Pago">Pago</option>
                        <option {{ isset($data->status) && $data->status === 'Cancelado' ? 'selected' : '' }} value="Cancelado">Cancelado</option> --}}
                    </select>

                </div>

                {{-- @if(!isset($data)) --}}
                <div class="form-group col-md-12 col-sm-12">
                    <div id="group-generate-invoice">
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
