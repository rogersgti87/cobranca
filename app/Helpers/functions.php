<?php

function moeda($get_valor) {

    $source = array('.', ',','R$');
    $replace = array('', '.','');
    $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
    return $valor; //retorna o valor formatado para gravar no banco
}

function whatsappBold($string) {

    $source = array('<b>', '</b>');
    $replace = array('*', '*');
    $valor = str_replace($source, $replace, $string);
    return $valor;
}

function removeEspeciais($get_valor) {

    $source = array('.', ',',' ','(',')','-','/');
    $replace = array('', '','','','','','');
    $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
    return $valor; //retorna o valor formatado para gravar no banco
}

function Mask($mask,$str){

    $str = str_replace(" ","",$str);

    for($i=0;$i<strlen($str);$i++){
        $mask[strpos($mask,"#")] = $str[$i];
    }

    return $mask;

}

function validateDate($date, $format = 'd/m/Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}


function check_base64_image($data) {



    if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
        return 'Imagem base64 inválida.';
    }else{

    $data = explode('/', explode(':', substr($data, 0, strpos($data, ';')))[1])[1];

    $ext = ['png','jpg','gif','jpeg'];

    if (in_array($data, $ext)) {
        return [true,uniqid(date('HisYmd')).'.'.$data];
    } else {
        return [false,"Extensões permitidas: 'png','jpg','gif','jpeg' "];
    }
}

    //return $data;

}


function getBase64ImageSize($base64Image){

        $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
        $size_in_kb    = $size_in_bytes / 1024;
        $size_in_mb    = $size_in_kb / 1024;

        if($size_in_mb < 3){
            return true;
        }else{
            return 'A imagem não pode ser maior que 3MB.';
        }

}

function limitarTexto($string, $word_limit) {
    $string = strip_tags($string);
    $words = explode(' ', strip_tags($string));
    $return = trim(implode(' ', array_slice($words, 0, $word_limit)));
    if(strlen($return) < strlen($string)){
        $return .= '...';
    }
    return $return;
}

function limitarLetra($texto, $limite){
    $len=strlen($texto);

    if ($len>$limite) {
        $texto=substr($texto,0,41).'...';
    }

    return $texto;
}

function revertSlug($string){

    return ucwords(str_replace('-', ' ', $string));
}

function removerDiv($string){
    return preg_replace("/<\/?(div)[^>]*\>/i", "", $string);
}

function replaceTags($string){
    return str_replace('</b></u></i></font><font color="#000000"></center></div>', '', $string);
}

function tempo_corrido($time) {

    $now = strtotime(date('m/d/Y H:i:s'));
    $time = strtotime($time);
    $diff = $now - $time;

    $seconds = $diff;
    $minutes = round($diff / 60);
    $hours = round($diff / 3600);
    $days = round($diff / 86400);
    $weeks = round($diff / 604800);
    $months = round($diff / 2419200);
    $years = round($diff / 29030400);

    if ($seconds <= 60) return"1 min atrás";
    else if ($minutes <= 60) return $minutes==1 ?'1 min':$minutes.' min';
    else if ($hours <= 24) return $hours==1 ?'1 hrs':$hours.' hrs';
    else if ($days <= 7) return $days==1 ?'1 dia':$days.' dias';
    else if ($weeks <= 4) return $weeks==1 ?'1 semana':$weeks.' semanas';
    else if ($months <= 12) return $months == 1 ?'1 mês':$months.' meses';
    else return $years == 1 ? 'um ano':$years.' anos';
}

function isJSON($string){
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function convertYoutube($string) {
    return preg_replace(
        "/[a-zA-Z\/\/:\.]*youtu(?:be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)(?:[&?\/]t=)?(\d*)(?:[a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "<iframe width=\"420\" height=\"315\" src=\"https://www.youtube.com/embed/$1?start=$2\" allowfullscreen></iframe>",
        $string
    );
}

function formatPhone($numero){
    if(strlen($numero) == 10){
        $novo = substr_replace($numero, '(', 0, 0);
        $novo = substr_replace($novo, '9', 3, 0);
        $novo = substr_replace($novo, ')', 3, 0);
    }else{
        $novo = substr_replace($numero, '(', 0, 0);
        $novo = substr_replace($novo, ')', 3, 0);
    }
    return $novo;
}


function generateUniqueId($minLength = 26, $maxLength = 30) {
    $uniqueId = uniqid();
    $randomChars = md5(uniqid(rand(), true)); // Gere uma sequência de caracteres aleatórios

    // Combine o resultado do uniqid com os caracteres aleatórios
    $combinedId = $uniqueId . $randomChars;

    // Defina um comprimento mínimo para o ID
    $combinedId = str_pad($combinedId, $minLength, '0', STR_PAD_RIGHT);

    // Certifique-se de que o ID final não seja mais longo que o comprimento máximo
    $uniqueId = substr($combinedId, 0, $maxLength);

    return $uniqueId;
}


function validarDocumento($document) {
    // Remove qualquer caractere não numérico
    $document = preg_replace('/\D/', '', $document);

    if (strlen($document) == 11) {
        return validarCPF($document);
    } elseif (strlen($document) == 14) {
        return validarCNPJ($document);
    } else {
        return false;
    }
}

function validarCPF($cpf) {
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

function validarCNPJ($cnpj) {
    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    for ($t = 12; $t < 14; $t++) {
        for ($d = 0, $p = 5, $c = 0; $c < $t; $c++) {
            $d += $cnpj[$c] * $p;
            $p = ($p == 2) ? 9 : $p - 1;
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cnpj[$c] != $d) {
            return false;
        }
    }
    return true;
}

function crc16($string) {
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($string); $i++) {
        $crc ^= ord($string[$i]) << 8;
        for ($j = 0; $j < 8; $j++) {
            $crc = ($crc & 0x8000) ? ($crc << 1) ^ 0x1021 : ($crc << 1);
        }
    }
    return $crc & 0xFFFF;
}

function gerarPayload(array $data): string {
    $result = '';
    foreach ($data as $id => $value) {
        if (is_array($value)) {
            $subPayload = gerarPayload($value);
            $length = str_pad(strlen($subPayload), 2, '0', STR_PAD_LEFT);
            $result .= $id . $length . $subPayload;
        } else {
            $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
            $result .= $id . $length . $value;
        }
    }
    return $result;
}

function gerarCodigoPix($pixKey, $amount, $descricao = 'Pagamento Pix', $txid = '***') {
    $merchantAccount = 'br.gov.bcb.pix';

    // Montagem dos campos obrigatórios
    $payload = [
        '00' => '01',
        '26' => [
            '00' => $merchantAccount,
            '01' => $pixKey,
        ],
        '52' => '0000',
        '53' => '986',
        '54' => number_format($amount, 2, '.', ''),
        '58' => 'BR',
        '59' => 'NAO INFORMADO',
        '60' => 'NAOINFORMADO',
        '62' => [
            '05' => $txid,
        ],
    ];

    // Gera o payload formatado
    $payloadStr = gerarPayload($payload);
    $payloadStr .= '6304'; // CRC placeholder
    $payloadStr .= strtoupper(dechex(crc16($payloadStr)));

    return $payloadStr;
}

