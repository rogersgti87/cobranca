    <div class="col-md-12">
    <div class="form-row">

        <div class="col-md-12">
            <br>
            <div class="form-row">
                <input type="hidden" name="category" id="category" value="{{ isset($data) ? $data->id : '' }}">

                <div class="form-group col-md-8 col-sm-12">
                    <label style="color: #E5E7EB;">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB;">
                </div>

                <div class="form-group col-md-4 col-sm-12">
                    <label style="color: #E5E7EB;">Cor <span class="text-danger">*</span></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" class="form-control" name="color" id="color" required value="{{isset($data->color) ? $data->color : '#FFBD59'}}" style="width: 80px; height: 38px; background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; cursor: pointer;">
                        <input type="text" class="form-control" id="color-hex" value="{{isset($data->color) ? $data->color : '#FFBD59'}}" readonly style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); color: #E5E7EB; flex: 1;">
                    </div>
                    <small class="form-text" style="color: #9CA3AF; margin-top: 5px;">A cor será usada para identificar a categoria nas contas a pagar</small>
                </div>

                <div class="form-group col-md-12 col-sm-12">
                    <label style="color: #E5E7EB;">Preview</label>
                    <div style="background-color: #1E293B; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; text-align: center;">
                        <span id="preview-badge" style="background-color: {{isset($data->color) ? $data->color : '#FFBD59'}}; color: #FFFFFF; padding: 8px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: {{isset($data->color) ? $data->color : '#FFBD59'}}; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%;"></span>
                            <span id="preview-name">{{isset($data->name) ? $data->name : 'Nome da Categoria'}}</span>
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>
    </div>

    <style>
        .form-control:focus {
            border-color: #FFBD59 !important;
            box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
            outline: none;
            background-color: #0F172A !important;
            color: #E5E7EB !important;
        }

        input[type="color"] {
            -webkit-appearance: none;
            border: none;
            cursor: pointer;
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
        }
    </style>

    <script>
        $(document).ready(function(){
            // Atualizar preview quando nome ou cor mudar
            $('#name').on('input', function(){
                var name = $(this).val() || 'Nome da Categoria';
                $('#preview-name').text(name);
            });

            $('#color').on('input', function(){
                var color = $(this).val();
                $('#color-hex').val(color);
                $('#preview-badge').css('background-color', color);
                $('#preview-badge span:first').css('background-color', color);
            });

            // Atualizar cor quando hex mudar manualmente
            $('#color-hex').on('input', function(){
                var hex = $(this).val();
                if(/^#[0-9A-F]{6}$/i.test(hex)){
                    $('#color').val(hex);
                    $('#preview-badge').css('background-color', hex);
                    $('#preview-badge span:first').css('background-color', hex);
                }
            });
        });
    </script>

