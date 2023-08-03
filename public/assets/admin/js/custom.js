// //Show Message
// function showMessage(type,message){
//     //$.NotificationApp.send("Status", message, 'top-right', '#da8609', type);
//     $.notify({
//         message:message
//      },
//      {
//         type:type,
//         allow_dismiss:true,
//         newest_on_top:false ,
//         mouse_over:false,
//         showProgressbar:false,
//         spacing:10,
//         timer:1000,
//         placement:{
//           from:'top',
//           align:'right'
//         },
//         offset:{
//           x:10,
//           y:10
//         },
//         delay:1000 ,
//         z_index:10000,
//         animate:{
//           enter:'animated swing',
//           exit:'animated bounce'
//       }
//     });
// }

//Preview Image
function previewImage(input, image,field) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(image).attr('src', e.target.result);
            $(field).attr('value', e.target.result);
            //$(field).attr('data-input-image', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// //Mask
$('#telephone').mask('(00)00000-0000');
$('#telephone2').mask('(00)0000-0000');
$('#celular').mask('(00)00000-00000');
$('#whatsapp').mask('(00)00000-0000');
$('#cpf').mask('000.000.000-00', {reverse: true});
$('#cnpj').mask('00.000.000/0000-00', {reverse: true});
$('.money').mask('000.000.000.000.000,00', {reverse: true});
$('#cep').mask('00000-000');
var options = {
    onKeyPress : function(cpfcnpj, e, field, options) {
        var masks = ['000.000.000-000', '00.000.000/0000-00'];
        var mask = (cpfcnpj.length > 14) ? masks[1] : masks[0];
        $('#cnpjcpf').mask(mask, options);
    }
};
$('#cnpjcpf').mask('000.000.000-000', options);
// $('#atendimento_segunda').mask('00:00 - 00:00');
// $('#atendimento_terca').mask('00:00 - 00:00');
// $('#atendimento_quarta').mask('00:00 - 00:00');
// $('#atendimento_quinta').mask('00:00 - 00:00');
// $('#atendimento_sexta').mask('00:00 - 00:00');
// $('#atendimento_sabado').mask('00:00 - 00:00');
// $('#atendimento_domingo').mask('00:00 - 00:00');


//Cep
$("#cep").blur(function () {
    var cep = $(this).val().replace(/\D/g, '');
    if (cep != "") {
        var validacep = /^[0-9]{8}$/;
        if (validacep.test(cep)) {
            $("#address").val("...");
            $("#district").val("...");
            $("#city").val("...");
            $("#state").val("...");
            //Consulta o webservice viacep.com.br/
            $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                if (!("erro" in dados)) {
                    $("#address").val(dados.logradouro);
                    $("#district").val(dados.bairro);
                    $("#city").val(dados.localidade);
                    $("#state").val(dados.uf);
                    $("#number").focus();
                } else {
                    $("#address").val("");
                    $("#district").val("");
                    $("#city").val("");
                    $("#state").val("");
                    showMessage('danger','Cep não encontrado!');
                }
            });
        } else {
            $("#address").val("");
            $("#district").val("");
            $("#city").val("");
            $("#state").val("");
            showMessage('danger','Cep inválido!');
        }
    } else {
        //cep sem valor, limpa formulário.
        $("#address").val("");
        $("#district").val("");
        $("#city").val("");
        $("#state").val("");
    }
});


//check all checkbox
$('#selectAllChekBox').click(function () {
    $('input:checkbox').not(this).prop('checked', this.checked);
});



var editor_config = {
    path_absolute : "/",
    selector: 'textarea.my-editor',
    relative_urls: false,
    plugins: [
      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table directionality",
      "emoticons template paste textpattern"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
    file_picker_callback : function(callback, value, meta) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?editor=' + meta.fieldname;
      if (meta.filetype == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
      }

      tinyMCE.activeEditor.windowManager.openUrl({
        url : cmsURL,
        title : 'Filemanager',
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no",
        onMessage: (api, message) => {
          callback(message.content);
        }
      });
    }
  };


