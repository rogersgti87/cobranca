<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Company;
use App\Services\IntegreAi\IntegreAiWhatsAppService;

$companyId = (int) ($argv[1] ?? 1);
$destination = $argv[2] ?? null;

$company = Company::find($companyId);
if (! $company) {
    fwrite(STDERR, "Company {$companyId} not found\n");
    exit(1);
}

echo "Company: {$company->id} - {$company->name}\n";
echo "WhatsApp: {$company->whatsapp}\n";
echo "Session: {$company->api_session_whatsapp}\n";
echo "Status: {$company->api_status_whatsapp}\n";
echo "Instance: {$company->integreai_instance_id}\n\n";

$service = app(IntegreAiWhatsAppService::class);

echo "=== getStatus ===\n";
$status = $service->getStatus($company);
echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

if (($status['status'] ?? '') !== 'open') {
    fwrite(STDERR, "WhatsApp not open. Aborting send test.\n");
    exit(2);
}

$number = $destination ?: $company->whatsapp;
if (! $number) {
    fwrite(STDERR, "No destination number. Pass as 2nd argument.\n");
    exit(3);
}

$text = '*Teste Cobrança Segura*' . "\n\nMensagem de teste em " . now()->format('d/m/Y H:i');

echo "=== sendText to {$number} ===\n";
$result = $service->sendText($company, $number, $text);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

exit(($result['success'] ?? false) ? 0 : 4);
