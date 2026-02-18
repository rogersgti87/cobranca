<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Payable;
use DB;

class GenerateRecurringPayables extends Command
{
    protected $signature = 'generate:recurring-payables';

    protected $description = 'Gerar contas a pagar recorrentes automaticamente';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Iniciando geração de contas recorrentes...');

        // Buscar todas as contas recorrentes ativas
        // Buscar apenas as contas originais (primeira de cada recorrência)
        $recurringPayables = Payable::where('type', 'Recorrente')
            ->where('status', '!=', 'Cancelado')
            ->whereNotNull('recurrence_period')
            ->whereNotNull('company_id')
            ->get()
            ->filter(function($payable) {
                // Verificar se é a conta original (primeira da recorrência)
                // A original é aquela que não tem outra conta com mesma descrição e data anterior
                $hasEarlier = Payable::where('user_id', $payable->user_id)
                    ->where('supplier_id', $payable->supplier_id)
                    ->where('description', $payable->description)
                    ->where('type', 'Recorrente')
                    ->where('date_due', '<', $payable->date_due)
                    ->whereNotNull('company_id')
                    ->exists();

                return !$hasEarlier;
            });

        $created = 0;

        foreach($recurringPayables as $payable) {
            try {
                // Verificar se a conta original está cancelada (para a recorrência)
                if($payable->status == 'Cancelado') {
                    continue;
                }

                // Verificar se já passou da data de término
                if($payable->recurrence_end && Carbon::parse($payable->recurrence_end)->isPast()) {
                    continue;
                }

                // Buscar a última conta gerada desta recorrência (maior data de vencimento)
                // Excluir a conta original da busca para verificar se já foram geradas filhas
                $lastPayable = Payable::where('user_id', $payable->user_id)
                    ->where('supplier_id', $payable->supplier_id)
                    ->where('description', $payable->description)
                    ->where('type', 'Recorrente')
                    ->where('recurrence_period', $payable->recurrence_period)
                    ->where('id', '!=', $payable->id) // Excluir a conta original
                    ->whereNotNull('company_id')
                    ->orderBy('date_due', 'DESC')
                    ->first();

                // Se não há contas filhas geradas, usar a conta original
                // Se há contas filhas, usar a última gerada
                $today = Carbon::today();
                $maxFutureDate = $today->copy()->addMonths(3);
                $hasChildren = $lastPayable !== null;
                $originalDueDate = Carbon::parse($payable->date_due);

                // Se a última conta gerada está muito no futuro (mais de 3 meses), ignorar e começar da original
                if($hasChildren) {
                    $lastDueDate = Carbon::parse($lastPayable->date_due);
                    if($lastDueDate->gt($maxFutureDate)) {
                        // Última conta está muito no futuro, ignorar e começar da original
                        $hasChildren = false;
                        $lastPayable = null;
                    }
                }

                $basePayable = $lastPayable ?: $payable;
                $lastDueDate = Carbon::parse($basePayable->date_due);

                // Verificar se deve criar baseado na última conta gerada
                // Gerar 10 dias antes do vencimento ou se já venceu
                $shouldCreate = false;
                $daysBefore = 10; // Gerar 10 dias antes do vencimento

                if(!$hasChildren) {
                    // Primeira geração: verificar se a conta original está 10 dias antes ou já venceu
                    $shouldCreate = $originalDueDate->isPast() || $originalDueDate->copy()->subDays($daysBefore)->lte($today);
                } else {
                    // Se já tem filhas, verificar se a última está 10 dias antes ou já venceu
                    $shouldCreate = $lastDueDate->isPast() || $lastDueDate->copy()->subDays($daysBefore)->lte($today);
                }

                if(!$shouldCreate) {
                    continue;
                }

                // Gerar todas as contas que faltam (apenas as que já venceram ou estão próximas)
                $maxIterations = 50; // Limite de segurança
                $iterations = 0;
                $currentBasePayable = $basePayable;

                while($iterations < $maxIterations) {
                    $iterations++;

                    // Calcular próxima data de vencimento baseada no período
                    $nextDueDate = $this->calculateNextDueDate($currentBasePayable);

                    if(!$nextDueDate) {
                        break;
                    }

                    // Gerar se está 10 dias antes do vencimento ou se já venceu
                    // Não gerar se está mais de 10 dias no futuro
                    $daysBefore = 10;
                    if($nextDueDate->copy()->subDays($daysBefore)->gt($today)) {
                        // Ainda está mais de 10 dias no futuro, parar
                        break;
                    }

                    // Verificar se já existe uma conta para esta data (com lock para evitar race condition)
                    // Usar transação com lockForUpdate para garantir atomicidade e evitar duplicação
                    DB::beginTransaction();
                    try {
                        // Usar first() com lockForUpdate para garantir que apenas uma execução por vez processe
                        $existingPayable = Payable::where('user_id', $payable->user_id)
                            ->where('supplier_id', $payable->supplier_id)
                            ->where('description', $payable->description)
                            ->where('date_due', $nextDueDate->format('Y-m-d'))
                            ->where('type', 'Recorrente')
                            ->whereNotNull('company_id')
                            ->lockForUpdate() // Lock para evitar duplicação em execuções simultâneas
                            ->first();

                        if($existingPayable) {
                            // Se já existe, usar essa conta como base para a próxima iteração
                            $currentBasePayable = $existingPayable;
                            DB::commit();
                            continue;
                        }

                        // Se chegou aqui, a conta não existe e a data já venceu, então criar

                        // Criar nova conta recorrente
                        $newPayable = new Payable();
                        $newPayable->company_id = $payable->company_id;
                        $newPayable->user_id = $payable->user_id;
                        $newPayable->supplier_id = $payable->supplier_id;
                        $newPayable->category_id = $payable->category_id;
                        $newPayable->description = $payable->description;
                        $newPayable->price = $payable->price;
                        $newPayable->type = 'Recorrente';
                        $newPayable->payment_method = $payable->payment_method;
                        $newPayable->date_due = $nextDueDate->format('Y-m-d');
                        $newPayable->date_payment = null;
                        $newPayable->status = 'Pendente';
                        $newPayable->recurrence_period = $payable->recurrence_period;
                        $newPayable->recurrence_day = $payable->recurrence_day;
                        $newPayable->recurrence_end = $payable->recurrence_end;
                        $newPayable->save();

                        DB::commit();
                        $created++;
                        $this->info("Conta criada: {$newPayable->description} - Vencimento: {$nextDueDate->format('d/m/Y')}");

                        // Atualizar basePayable para a próxima iteração
                        $currentBasePayable = $newPayable;
                    } catch(\Exception $e) {
                        DB::rollBack();
                        // Se der erro de duplicação (unique constraint), apenas continuar
                        if(strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'duplicate') !== false) {
                            // Conta já foi criada por outra execução, continuar
                            $existingPayable = Payable::where('user_id', $payable->user_id)
                                ->where('supplier_id', $payable->supplier_id)
                                ->where('description', $payable->description)
                                ->where('date_due', $nextDueDate->format('Y-m-d'))
                                ->where('type', 'Recorrente')
                                ->whereNotNull('company_id')
                                ->first();
                            if($existingPayable) {
                                $currentBasePayable = $existingPayable;
                            }
                            continue;
                        }
                        throw $e; // Re-lançar se for outro tipo de erro
                    }
                }

            } catch(\Exception $e) {
                \Log::error("Erro ao criar conta recorrente ID {$payable->id}: " . $e->getMessage());
                $this->error("Erro ao processar conta ID {$payable->id}: " . $e->getMessage());
            }
        }

        $this->info("Processamento concluído. {$created} conta(s) criada(s).");
    }

    private function calculateNextDueDate($payable)
    {
        $lastDueDate = Carbon::parse($payable->date_due);
        $nextDueDate = null;

        switch($payable->recurrence_period) {
            case 'Semanal':
                // Próxima data será 7 dias após a última data de vencimento
                $nextDueDate = $lastDueDate->copy()->addWeek();
                break;

            case 'Quinzenal':
                // Próxima data será 15 dias após a última data de vencimento
                $nextDueDate = $lastDueDate->copy()->addDays(15);
                break;

            case 'Mensal':
                // Próxima data será no mesmo dia do próximo mês
                $nextDueDate = $lastDueDate->copy()->addMonth();
                // Se tiver recurrence_day, usar esse dia do mês
                if($payable->recurrence_day) {
                    $nextDueDate->day($payable->recurrence_day);
                }
                break;

            case 'Bimestral':
                $nextDueDate = $lastDueDate->copy()->addMonths(2);
                if($payable->recurrence_day) {
                    $nextDueDate->day($payable->recurrence_day);
                }
                break;

            case 'Trimestral':
                $nextDueDate = $lastDueDate->copy()->addMonths(3);
                if($payable->recurrence_day) {
                    $nextDueDate->day($payable->recurrence_day);
                }
                break;

            case 'Semestral':
                $nextDueDate = $lastDueDate->copy()->addMonths(6);
                if($payable->recurrence_day) {
                    $nextDueDate->day($payable->recurrence_day);
                }
                break;

            case 'Anual':
                $nextDueDate = $lastDueDate->copy()->addYear();
                if($payable->recurrence_day) {
                    $nextDueDate->day($payable->recurrence_day);
                }
                break;

            default:
                return null;
        }

        // Verificar se não passou da data de término
        if($payable->recurrence_end && $nextDueDate->gt(Carbon::parse($payable->recurrence_end))) {
            return null;
        }

        return $nextDueDate;
    }
}

