    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="customer_service_id" id="customer_service_id" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                <div class="form-group col-md-4 col-sm-12">
                    <label>Serviço</label>
                    <select class="form-control custom-select" name="service_id" id="service_id">
                        <option value="">Selecione um serviço</option>
                        @foreach($services as $service)
                            <option {{ isset($data->service_id) && $data->service_id == $service->id ? 'selected' : '' }} value="{{ $service->id }}" data-price={{ $service->price }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Descrição</label>
                    <input type="text" class="form-control" name="description" id="description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}">
                </div>

                <div class="form-group col-md-2 col-sm-12">
                    <label>Status</label>
                    <select class="form-control custom-select" name="status" id="status">
                        <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                        <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                    </select>
                </div>


                <div class="form-group col-md-4 col-sm-12">
                    <label>Dia Vencimento</label>
                    <select class="form-control custom-select" name="day_due" id="day_due">
                        <option {{ isset($data->day_due) && $data->day_due === 5 ? 'selected' : '' }} value="5">5</option>
                        <option {{ isset($data->day_due) && $data->day_due === 7 ? 'selected' : '' }} value="7">7</option>
                        <option {{ isset($data->day_due) && $data->day_due === 10 ? 'selected' : '' }} value="10">10</option>
                        <option {{ isset($data->day_due) && $data->day_due === 12 ? 'selected' : '' }} value="12">12</option>
                        <option {{ isset($data->day_due) && $data->day_due === 15 ? 'selected' : '' }} value="15">15</option>
                        <option {{ isset($data->day_due) && $data->day_due === 17 ? 'selected' : '' }} value="17">17</option>
                        <option {{ isset($data->day_due) && $data->day_due === 20 ? 'selected' : '' }} value="20">20</option>
                        <option {{ isset($data->day_due) && $data->day_due === 25 ? 'selected' : '' }} value="25">25</option>
                        <option {{ isset($data->day_due) && $data->day_due === 28 ? 'selected' : '' }} value="28">28</option>

                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Preço</label>
                    <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label>Período</label>
                    <select class="form-control custom-select" name="period" id="period">
                        <option value="">Selecione o Período</option>
                        <option {{ isset($data->period) && $data->period === 'Recorrente' ? 'selected' : '' }} value="Recorrente">Recorrente</option>
                        <option {{ isset($data->period) && $data->period === 'Único' ? 'selected' : '' }} value="Único">Único</option>
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Gateway Pagamento</label>
                    <select class="form-control custom-select" name="gateway_payment" id="gateway_payment">
                        <option value="">Selecione o Gateway de Pagamento</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Estabelecimento' ? 'selected' : '' }} value="Estabelecimento">Estabelecimento</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Pag Hiper' ? 'selected' : '' }} value="Pag Hiper">Pag Hiper</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Mercado Pago' ? 'selected' : '' }} value="Mercado Pago">Mercado Pago</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Intermedium' ? 'selected' : '' }} value="Intermedium">Intermedium</option>
                        <option {{ isset($data->gateway_payment) && $data->gateway_payment === 'Cora' ? 'selected' : '' }} value="Cora">Cora(em breve)</option>
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label>Forma de Pagamento</label>
                    <select class="form-control custom-select" name="payment_method" id="payment_method">
                        <option value="">Selecione a Forma de Pagamento</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Depósito' ? 'selected' : '' }} value="Depósito">Depósito</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Dinheiro' ? 'selected' : '' }} value="Dinheiro">Dinheiro</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Cartão' ? 'selected' : '' }} value="Cartão">Cartão</option>
                    </select>
                </div>



                @if(!isset($data))
                <div class="form-group col-md-12 col-sm-12">

                    <div id="group-generate-invoice">
                    <div class="form-row">
                      <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="generate_invoice" id="generate_invoice" value="1">
                        <label class="form-check-label" for="generate_invoice">Marque para <b>Gerar Fatura</b> do serviço</label>
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
                @endif

            </div>
        </div>


    </div>
    </div>
