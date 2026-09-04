<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = (int) ($argv[1] ?? 4293);
$invoice = App\Models\Invoice::with(['customerService.customer', 'company'])->find($id);

if (! $invoice) {
    echo "Invoice {$id} not found\n";
    exit(1);
}

echo json_encode([
    'id' => $invoice->id,
    'status' => $invoice->status,
    'date_invoice' => $invoice->date_invoice,
    'date_due' => $invoice->date_due,
    'date_payment' => $invoice->date_payment,
    'gateway_payment' => $invoice->gateway_payment,
    'payment_method' => $invoice->payment_method,
    'transaction_id' => $invoice->transaction_id,
    'updated_at' => (string) $invoice->updated_at,
    'customer' => $invoice->customerService?->customer?->name,
    'notification_whatsapp' => $invoice->customerService?->customer?->notification_whatsapp,
    'notification_email' => $invoice->customerService?->customer?->notification_email,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$notifications = DB::table('invoice_notifications')
    ->where('invoice_id', $id)
    ->orderByDesc('date')
    ->get(['id', 'type_send', 'subject', 'status', 'date', 'created_at']);

echo "Notifications ({$notifications->count()}):\n";
echo json_encode($notifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
