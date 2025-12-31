    <div class="col-md-12">
    <div class="form-row">
        <input type="hidden" name="supplier_id" id="supplier_id" value="{{ isset($data) ? $data->id : '' }}">
        
        <div class="col-md-12">
            <div class="form-row">

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label style="color: #E5E7EB;">Tipo</label>
                                                    <select class="form-control custom-select" name="type" id="type" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                        <option {{ isset($data->type) && $data->type === 'Física' ? 'selected' : '' }} value="Física">Física</option>
                                                        <option {{ isset($data->type) && $data->type === 'Jurídica' ? 'selected' : '' }} value="Jurídica">Jurídica</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label style="color: #E5E7EB;">CPF/CNPJ</label>
                                                    <input type="text" class="form-control" name="document" id="document" autocomplete="off" value="{{isset($data->document) ? $data->document : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label style="color: #E5E7EB;">Nome <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label style="color: #E5E7EB;">Empresa</label>
                                                    <input type="text" class="form-control" name="company" id="company" autocomplete="off" value="{{isset($data->company) ? $data->company : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-5 col-sm-12">
                                                    <label style="color: #E5E7EB;">E-mail</label>
                                                    <input type="email" class="form-control" name="email" id="email" autocomplete="off" value="{{isset($data->email) ? $data->email : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-5 col-sm-12">
                                                    <label style="color: #E5E7EB;">E-mail 2</label>
                                                    <input type="email" class="form-control" name="email2" id="email2" autocomplete="off" value="{{isset($data->email2) ? $data->email2 : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label style="color: #E5E7EB;">Cep</label>
                                                    <input type="text" class="form-control" name="cep" id="cep" autocomplete="off" value="{{isset($data->cep) ? $data->cep : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-10 col-sm-12">
                                                    <label style="color: #E5E7EB;">Endereço</label>
                                                    <input type="text" class="form-control" name="address" id="address" autocomplete="off" value="{{isset($data->address) ? $data->address : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label style="color: #E5E7EB;">Número</label>
                                                    <input type="text" class="form-control" name="number" id="number" autocomplete="off" value="{{isset($data->number) ? $data->number : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-3 col-sm-12">
                                                    <label style="color: #E5E7EB;">Bairro</label>
                                                    <input type="text" class="form-control" name="district" id="district" autocomplete="off" value="{{isset($data->district) ? $data->district : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-3 col-sm-12">
                                                    <label style="color: #E5E7EB;">Cidade</label>
                                                    <input type="text" class="form-control" name="city" id="city" autocomplete="off" value="{{isset($data->city) ? $data->city : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label style="color: #E5E7EB;">Estado</label>
                                                    <input type="text" class="form-control" name="state" id="state" autocomplete="off" maxlength="2" value="{{isset($data->state) ? $data->state : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label style="color: #E5E7EB;">Complemento</label>
                                                    <input type="text" class="form-control" name="complement" id="complement" autocomplete="off" value="{{isset($data->complement) ? $data->complement : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label style="color: #E5E7EB;">Telefone</label>
                                                    <input type="text" class="form-control" name="phone" id="phone" autocomplete="off" value="{{isset($data->phone) ? $data->phone : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label style="color: #E5E7EB;">Whatsapp</label>
                                                    <input type="text" class="form-control" name="whatsapp" id="whatsapp" autocomplete="off" value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label style="color: #E5E7EB;">Status</label>
                                                    <select class="form-control custom-select" name="status" id="status" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                                                        <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                                                        <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-12 col-sm-12">
                                                    <label style="color: #E5E7EB;">Observação</label>
                                                    <textarea class="form-control" name="obs" id="obs" rows="6" autocomplete="off" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">{{isset($data->obs) ? $data->obs : ''}}</textarea>
                                                </div>

                                            </div>
        </div>
    </div>
    </div>

