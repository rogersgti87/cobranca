    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="invoice" id="invoice" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                <div class="form-group col-md-6 col-sm-12">
                    <label>Serviço</label>
                    <select class="form-control custom-select" name="customer_service_id" id="customer_service_id">
                        <option value="">Selecione um serviço</option>
                        @foreach($customer_services as $service)
                            <option {{ isset($data->customer_service_id) && $data->customer_service_id == $service->id ? 'selected' : '' }} value="{{ $service->id }}" data-price={{ $service->price }} data-description={{ $service->description }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Descrição</label>
                    <input type="text" class="form-control" name="description" id="description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}">
                </div>


                <div class="form-group col-md-4 col-sm-12">
                    <label>Preço</label>
                    <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Forma de Pagamento</label>
                    <select class="form-control custom-select" name="payment_method" id="payment_method">
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Depósito' ? 'selected' : '' }} value="Depósito">Depósito</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Dinheiro' ? 'selected' : '' }} value="Dinheiro">Dinheiro</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Cartão' ? 'selected' : '' }} value="Cartão">Cartão</option>
                    </select>
                </div>


                <div class="form-group col-md-4 col-sm-12">
                    <label>Status</label>
                    <select class="form-control custom-select" name="status" id="status">
                        <option {{ isset($data->status) && $data->status === 'Pendente' ? 'selected' : '' }} value="Pendente">Pendente</option>
                        <option {{ isset($data->status) && $data->status === 'Processamento' ? 'selected' : '' }} value="Processamento">Processamento</option>
                        <option {{ isset($data->status) && $data->status === 'Pago' ? 'selected' : '' }} value="Pago">Pago</option>
                        <option {{ isset($data->status) && $data->status === 'Cancelado' ? 'selected' : '' }} value="Cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data da fatura</label>
                    <input type="date" class="form-control datepicker" name="date_invoice" id="date_invoice" autocomplete="off" required value="{{isset($data->date_invoice) ? $data->date_invoice : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data de vencimento</label>
                    <input type="date" class="form-control" name="date_due" id="date_due" autocomplete="off" required value="{{isset($data->date_due) ? $data->date_due : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Data de pagamento</label>
                    <input type="date" class="form-control" name="date_payment" id="date_payment" autocomplete="off" required value="{{isset($data->date_payment) ? $data->date_payment : ''}}">
                </div>


                @if(!isset($data))
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
                @endif

            </div>
        </div>


    </div>
    </div>
