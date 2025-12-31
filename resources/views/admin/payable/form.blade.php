    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="payable" id="payable" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="supplier_id" value="{{ $supplier_id }}">

                <div class="form-group col-md-6 col-sm-12">
                    <label style="color: #E5E7EB;">Fornecedor <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="supplier_id" id="supplier_id" required style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                        <option value="">Selecione um fornecedor</option>
                        @foreach($suppliers as $supplier)
                            <option {{ isset($data->supplier_id) && $data->supplier_id == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label style="color: #E5E7EB;">Descrição <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="description" id="description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #E5E7EB;">Valor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #E5E7EB;">Tipo de Conta <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="type" id="type" required {{ isset($data) ? 'disabled' : '' }} style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                        <option value="">Selecione o tipo</option>
                        <option {{ isset($data->type) && $data->type === 'Fixa' ? 'selected' : '' }} value="Fixa">Fixa</option>
                        <option {{ isset($data->type) && $data->type === 'Recorrente' ? 'selected' : '' }} value="Recorrente">Recorrente</option>
                        <option {{ isset($data->type) && $data->type === 'Parcelada' ? 'selected' : '' }} value="Parcelada">Parcelada</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #E5E7EB;">Forma de Pagamento</label>
                    <select class="form-control custom-select" name="payment_method" id="payment_method" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                        <option value="">Selecione a Forma de Pagamento</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Depósito' ? 'selected' : '' }} value="Depósito">Depósito</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Dinheiro' ? 'selected' : '' }} value="Dinheiro">Dinheiro</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Cartão' ? 'selected' : '' }} value="Cartão">Cartão</option>
                        <option {{ isset($data->payment_method) && $data->payment_method === 'Transferência' ? 'selected' : '' }} value="Transferência">Transferência</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #E5E7EB;">Data de vencimento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" min="{{ date('Y-m-d') }}" name="date_due" id="date_due" autocomplete="off" required value="{{isset($data->date_due) ? $data->date_due : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #E5E7EB;">Data de pagamento</label>
                    <input type="date" class="form-control" name="date_payment" id="date_payment" autocomplete="off" value="{{isset($data->date_payment) ? $data->date_payment : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                </div>

                <!-- Campos para conta recorrente -->
                <div class="form-group col-md-12 col-sm-12" id="recurrence-fields" style="display: none;">
                    <div class="card" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="card-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <h3 class="card-title" style="color: #FFBD59;">Configurações de Recorrência</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label style="color: #E5E7EB;">Período de Recorrência</label>
                                    <select class="form-control custom-select" name="recurrence_period" id="recurrence_period" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                        <option value="">Selecione o período</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Semanal' ? 'selected' : '' }} value="Semanal">Semanal</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Quinzenal' ? 'selected' : '' }} value="Quinzenal">Quinzenal</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Mensal' ? 'selected' : '' }} value="Mensal">Mensal</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Bimestral' ? 'selected' : '' }} value="Bimestral">Bimestral</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Trimestral' ? 'selected' : '' }} value="Trimestral">Trimestral</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Semestral' ? 'selected' : '' }} value="Semestral">Semestral</option>
                                        <option {{ isset($data->recurrence_period) && $data->recurrence_period === 'Anual' ? 'selected' : '' }} value="Anual">Anual</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4 col-sm-12">
                                    <label style="color: #E5E7EB;">Dia da Recorrência</label>
                                    <input type="number" class="form-control" name="recurrence_day" id="recurrence_day" min="1" max="31" value="{{isset($data->recurrence_day) ? $data->recurrence_day : ''}}" placeholder="Dia do mês (1-31)" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                </div>

                                <div class="form-group col-md-4 col-sm-12">
                                    <label style="color: #E5E7EB;">Data de Término</label>
                                    <input type="date" class="form-control" name="recurrence_end" id="recurrence_end" value="{{isset($data->recurrence_end) ? $data->recurrence_end : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos para conta parcelada -->
                <div class="form-group col-md-12 col-sm-12" id="installment-fields" style="display: none;">
                    <div class="card" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="card-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <h3 class="card-title" style="color: #FFBD59;">Configurações de Parcelamento</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label style="color: #E5E7EB;">Número de Parcelas</label>
                                    <input type="number" class="form-control" name="installments" id="installments" min="2" max="60" value="{{isset($data->installments) ? $data->installments : '1'}}" placeholder="Número de parcelas" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                    <small class="form-text" style="color: #9CA3AF;">O valor será dividido igualmente entre as parcelas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    </div>

    <style>
        select.form-control,
        select.custom-select {
            color: #E5E7EB !important;
            background-color: #0F172A !important;
        }

        select.form-control:focus,
        select.custom-select:focus {
            border-color: #FFBD59 !important;
            box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
            outline: none;
            background-color: #0F172A !important;
            color: #E5E7EB !important;
        }

        select.form-control option,
        select.custom-select option {
            background-color: #111827 !important;
            color: #E5E7EB !important;
        }

        select.form-control option:checked,
        select.custom-select option:checked {
            background-color: #1E293B !important;
            color: #E5E7EB !important;
        }

        .form-control:focus {
            border-color: #FFBD59 !important;
            box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
            outline: none;
            background-color: #0F172A !important;
            color: #E5E7EB !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 0.7;
        }

        /* Força a cor branca em todos os estados do select */
        select {
            color: #E5E7EB !important;
        }

        select:focus {
            color: #E5E7EB !important;
        }

        select option {
            color: #E5E7EB !important;
        }
    </style>
    <script>
        $(document).ready(function(){
            // Mostrar/ocultar campos baseado no tipo selecionado
            $('#type').on('change', function(){
                var type = $(this).val();

                // Ocultar todos os campos específicos
                $('#recurrence-fields').hide();
                $('#installment-fields').hide();

                // Mostrar campos específicos baseado no tipo
                if(type == 'Recorrente'){
                    $('#recurrence-fields').show();
                } else if(type == 'Parcelada'){
                    $('#installment-fields').show();
                }
            });

            // Trigger inicial se já houver um tipo selecionado
            if($('#type').val() != ''){
                $('#type').trigger('change');
            }
        });
    </script>
