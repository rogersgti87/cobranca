<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QueueWorkCompanies extends Command
{
    protected $signature = 'queue:work-companies
                            {--sleep=3 : Segundos de espera quando não houver jobs}
                            {--tries=3 : Tentativas por job}
                            {--max-time=0 : Tempo máximo de execução do worker (0 = ilimitado)}
                            {--memory=128 : Limite de memória em MB}
                            {--timeout=60 : Tempo máximo por job em segundos}
                            {--rest=0 : Pausa entre jobs em segundos}
                            {--force : Forçar execução em produção}';

    protected $description = 'Processa filas default e company_{id} de todas as empresas ativas';

    public function handle(): int
    {
        $queues = $this->resolveQueues();

        Log::info('queue:work-companies filas resolvidas', [
            'count' => count($queues),
            'queues' => $queues,
        ]);

        $params = [
            '--queue' => implode(',', $queues),
            '--sleep' => $this->option('sleep'),
            '--tries' => $this->option('tries'),
            '--memory' => $this->option('memory'),
            '--timeout' => $this->option('timeout'),
            '--rest' => $this->option('rest'),
        ];

        if ((int) $this->option('max-time') > 0) {
            $params['--max-time'] = $this->option('max-time');
        }

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        return $this->call('queue:work', $params);
    }

    /**
     * @return array<int, string>
     */
    protected function resolveQueues(): array
    {
        $companyQueues = Company::query()
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => 'company_' . $id)
            ->all();

        return array_values(array_unique(array_merge(['default'], $companyQueues)));
    }
}
