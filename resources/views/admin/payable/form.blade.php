    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="payable" id="payable" value="{{ isset($data) ? $data->id : '' }}">
                <input type="hidden" name="supplier_id" value="{{ $supplier_id }}">

                <div class="form-group col-md-6 col-sm-12">
                    <label style="color: #1F2937;">Fornecedor <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="supplier_id" id="supplier_id" required style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                        <option value="">Selecione um fornecedor</option>
                        @foreach($suppliers as $supplier)
                            <option {{ isset($data->supplier_id) && $data->supplier_id == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6 col-sm-12">
                    <label style="color: #1F2937;">Categoria <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="category_id" id="category_id" required style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                        <option value="">Selecione uma categoria</option>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $category)
                                <option {{ isset($data->category_id) && $data->category_id == $category->id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group col-md-12 col-sm-12">
                    <label style="color: #1F2937;">Descrição <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="description" id="description" autocomplete="off" required value="{{isset($data->description) ? $data->description : ''}}" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #1F2937;">Valor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? number_format($data->price,2,',','.') : ''}}" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #1F2937;">Tipo de Conta <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="type" id="type" required {{ isset($data) ? 'disabled' : '' }} style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                        <option value="">Selecione o tipo</option>
                        <option {{ isset($data->type) && $data->type === 'Fixa' ? 'selected' : '' }} value="Fixa">Fixa</option>
                        <option {{ isset($data->type) && $data->type === 'Recorrente' ? 'selected' : '' }} value="Recorrente">Recorrente</option>
                        <option {{ isset($data->type) && $data->type === 'Parcelada' ? 'selected' : '' }} value="Parcelada">Parcelada</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #1F2937;">Forma de Pagamento</label>
                    <select class="form-control custom-select" name="payment_method" id="payment_method" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
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
                    <label style="color: #1F2937;">Data de vencimento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="date_due" id="date_due" autocomplete="off" required value="{{isset($data->date_due) ? $data->date_due : ''}}" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #1F2937;">Data de pagamento</label>
                    <input type="date" class="form-control" name="date_payment" id="date_payment" autocomplete="off" value="{{isset($data->date_payment) ? $data->date_payment : ''}}" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                </div>

                <!-- Campos para conta recorrente -->
                <div class="form-group col-md-12 col-sm-12" id="recurrence-fields" style="display: none;">
                    <div class="card" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
                        <div class="card-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                            <h3 class="card-title" style="color: #FFBD59;">Configurações de Recorrência</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label style="color: #1F2937;">Período de Recorrência</label>
                                    <select class="form-control custom-select" name="recurrence_period" id="recurrence_period" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
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
                                    <label style="color: #1F2937;">Dia da Recorrência</label>
                                    <input type="number" class="form-control" name="recurrence_day" id="recurrence_day" min="1" max="31" value="{{isset($data->recurrence_day) ? $data->recurrence_day : ''}}" placeholder="Dia do mês (1-31)" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                                </div>

                                <div class="form-group col-md-4 col-sm-12">
                                    <label style="color: #1F2937;">Data de Término</label>
                                    <input type="date" class="form-control" name="recurrence_end" id="recurrence_end" value="{{isset($data->recurrence_end) ? $data->recurrence_end : ''}}" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                                    <small class="form-text" style="color: #6B7280;">
                                        <i class="fas fa-info-circle"></i> Defina uma data para parar a geração automática. Deixe em branco para continuar indefinidamente.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos para conta parcelada -->
                <div class="form-group col-md-12 col-sm-12" id="installment-fields" style="display: none;">
                    <div class="card" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
                        <div class="card-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                            <h3 class="card-title" style="color: #FFBD59;">Configurações de Parcelamento</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label style="color: #1F2937;">Número de Parcelas</label>
                                    <input type="number" class="form-control" name="installments" id="installments" min="2" max="60" value="{{isset($data->installments) ? $data->installments : '1'}}" placeholder="Número de parcelas" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); color: #1F2937;">
                                    <small class="form-text" style="color: #6B7280;">O valor será dividido igualmente entre as parcelas</small>
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
        select.custom-select,
        #supplier_id,
        #category_id,
        #type,
        #payment_method,
        #recurrence_period {
            color: #1F2937 !important;
            background-color: #FFFFFF !important;
            background-image: none !important;
        }

        select.form-control:focus,
        select.custom-select:focus,
        #supplier_id:focus,
        #category_id:focus,
        #type:focus,
        #payment_method:focus,
        #recurrence_period:focus {
            border-color: #FFBD59 !important;
            box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
            outline: none;
            background-color: #FFFFFF !important;
            color: #1F2937 !important;
        }

        select.form-control option,
        select.custom-select option,
        #supplier_id option,
        #category_id option,
        #type option,
        #payment_method option,
        #recurrence_period option {
            background-color: #FFFFFF !important;
            color: #1F2937 !important;
        }

        select.form-control option:checked,
        select.custom-select option:checked,
        #supplier_id option:checked,
        #category_id option:checked,
        #type option:checked,
        #payment_method option:checked,
        #recurrence_period option:checked {
            background-color: #F5F5DC !important;
            color: #1F2937 !important;
        }

        .form-control:focus,
        input:focus,
        textarea:focus {
            border-color: #FFBD59 !important;
            box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
            outline: none;
            background-color: #FFFFFF !important;
            color: #1F2937 !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0);
            cursor: pointer;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 0.7;
        }

        /* Força a cor escura e fundo branco em todos os estados do select */
        select {
            color: #1F2937 !important;
            background-color: #FFFFFF !important;
        }

        select:focus {
            color: #1F2937 !important;
            background-color: #FFFFFF !important;
        }

        select option {
            color: #1F2937 !important;
            background-color: #FFFFFF !important;
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
