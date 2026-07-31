<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QueueWorkCompanies extends Command
{
    protected $signature = 'queue:work-companies
                            {connection? : Conexão da fila}
                            {--name=default : Nome do worker}
                            {--sleep=3 : Segundos de espera quando não houver jobs}
                            {--tries=3 : Tentativas por job}
                            {--max-time=0 : Tempo máximo de execução do worker (0 = ilimitado)}
                            {--max-jobs=0 : Jobs processados antes de reiniciar o worker (0 = ilimitado)}
                            {--memory=128 : Limite de memória em MB}
                            {--timeout=180 : Tempo máximo por job em segundos}
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
            '--name' => $this->option('name'),
            '--sleep' => $this->option('sleep'),
            '--tries' => $this->option('tries'),
            '--memory' => $this->option('memory'),
            '--timeout' => $this->option('timeout'),
            '--rest' => $this->option('rest'),
        ];

        if ((int) $this->option('max-time') > 0) {
            $params['--max-time'] = $this->option('max-time');
        }

        if ((int) $this->option('max-jobs') > 0) {
            $params['--max-jobs'] = $this->option('max-jobs');
        }

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        if ($connection = $this->argument('connection')) {
            $params['connection'] = $connection;
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
