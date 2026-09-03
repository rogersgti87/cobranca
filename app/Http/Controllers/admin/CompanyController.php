<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\IntegreAi\IntegreAiWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $field = $request->input('field', 'name');
        $operator = $request->input('operator', 'like');
        $value = $request->input('value', '');

        $query = Auth::user()->companies()->with('users');

        if (!empty($value)) {
            $searchValue = $operator === 'like' ? "%{$value}%" : $value;
            if ($field === 'status') {
                $query->where('status', $value);
            } else {
                $query->where($field, $operator, $searchValue);
            }
        }

        $companies = $query->orderBy('name')->paginate(15);
        $currentCompany = Auth::user()->currentCompany;

        return view('admin.companies.index', compact('companies', 'currentCompany'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'type' => 'required|in:Física,Jurídica',
            'document' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => ['nullable', 'string', $this->whatsappValidationRule()],
            'cep' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'logo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:Ativo,Inativo',
            'day_generate_invoice' => 'nullable|integer|min:1|max:31',
            'send_generate_invoice' => 'nullable|in:Não,Sim',
        ]);

        $data = $this->normalizeWhatsappInput($data);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $data['status'] = $request->input('status', 'Ativo');
        $data['day_generate_invoice'] = $data['day_generate_invoice'] ?? 1;
        $data['send_generate_invoice'] = $data['send_generate_invoice'] ?? 'Sim';
        
        $company = Company::create($data);
        
        // Vincular usuário atual como owner
        $company->users()->attach(Auth::id(), ['role' => 'owner']);
        
        // Definir como empresa ativa se for a primeira
        if (!Auth::user()->current_company_id) {
            Auth::user()->update(['current_company_id' => $company->id]);
        }
        
        return redirect()->route('companies.index')
            ->with('success', 'Empresa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        // Verificar se usuário tem acesso
        if (!$company->hasUser(Auth::id())) {
            abort(403, 'Você não tem acesso a esta empresa');
        }
        
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        // Verificar se usuário tem acesso
        if (!$company->hasUser(Auth::id())) {
            abort(403, 'Você não tem acesso a esta empresa');
        }
        
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        // Verificar se usuário é admin ou owner
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para editar esta empresa');
        }
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'type' => 'required|in:Física,Jurídica',
            'document' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => ['nullable', 'string', $this->whatsappValidationRule()],
            'cep' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'logo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:Ativo,Inativo',
            'day_generate_invoice' => 'nullable|integer|min:1|max:31',
            'send_generate_invoice' => 'nullable|in:Não,Sim',
            
            // Configurações de integrações
            'chave_pix' => 'nullable|string',
            'token_paghiper' => 'nullable|string',
            'key_paghiper' => 'nullable|string',
            'access_token_mp' => 'nullable|string',
            'inter_client_id' => 'nullable|string',
            'inter_client_secret' => 'nullable|string',
            'inter_chave_pix' => 'nullable|string',
            'environment_asaas' => 'nullable|in:Teste,Produção',
            'at_asaas_prod' => 'nullable|string',
            'at_asaas_test' => 'nullable|string',
            'api_session_whatsapp' => 'nullable|string',
            'api_status_whatsapp' => 'nullable|string',
        ]);

        $data = $this->normalizeWhatsappInput($data);

        // Upload logo se fornecido
        if ($request->hasFile('logo')) {
            // Deletar logo antiga se existir
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }
        
        $company->update($data);
        
        return redirect()->route('companies.index')
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // Verificar se usuário é owner
        if (!$company->isOwner(Auth::id())) {
            abort(403, 'Apenas o proprietário pode excluir a empresa');
        }
        
        // Verificar se não é a última empresa do usuário
        if (Auth::user()->companies()->count() <= 1) {
            return redirect()->route('companies.index')
                ->with('error', 'Você não pode excluir sua única empresa');
        }
        
        // Se for a empresa ativa, trocar para outra
        if (Auth::user()->current_company_id == $company->id) {
            $newCompany = Auth::user()->companies()->where('id', '!=', $company->id)->first();
            Auth::user()->update(['current_company_id' => $newCompany->id]);
        }
        
        // Deletar logo se existir
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        
        $company->delete();
        
        return redirect()->route('companies.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }

    /**
     * Bulk destroy companies
     */
    public function bulkDestroy(Request $request)
    {
        $selected = $request->input('selected', []);

        if (empty($selected)) {
            return response()->json('Selecione ao menos uma empresa', 422);
        }

        $userCompanies = Auth::user()->companies();
        $totalCompanies = $userCompanies->count();

        if ($totalCompanies <= count($selected)) {
            return response()->json('Você não pode excluir todas as suas empresas', 422);
        }

        foreach ($selected as $id) {
            $company = Company::find($id);
            if ($company && $company->isOwner(Auth::id())) {
                if (Auth::user()->current_company_id == $company->id) {
                    $newCompany = Auth::user()->companies()->where('id', '!=', $company->id)->first();
                    if ($newCompany) {
                        Auth::user()->update(['current_company_id' => $newCompany->id]);
                    }
                }
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                $company->delete();
            }
        }

        return response()->json(true, 200);
    }
    
    /**
     * Switch to another company
     */
    public function switch(Company $company)
    {
        if (!$company->hasUser(Auth::id())) {
            abort(403, 'Você não tem acesso a esta empresa');
        }
        
        Auth::user()->update(['current_company_id' => $company->id]);
        
        return redirect()->back()
            ->with('success', "Empresa alterada para: {$company->name}");
    }
    
    /**
     * Show integrations form
     */
    public function integrations(Company $company)
    {
        // Verificar se usuário é admin ou owner
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }
        
        // Obter informações do certificado Inter
        $certInfo = $company->getInterCertificateInfo();
        
        return view('admin.companies.integrations', compact('company', 'certInfo'));
    }
    
    /**
     * Update integrations
     */
    public function updateIntegrations(Request $request, Company $company)
    {
        // Verificar se usuário é admin ou owner
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }
        
        $data = $request->validate([
            // PIX
            'chave_pix' => 'nullable|string',
            
            // PagHiper
            'token_paghiper' => 'nullable|string',
            'key_paghiper' => 'nullable|string',
            
            // Mercado Pago
            'access_token_mp' => 'nullable|string',
            
            // Banco Inter
            'inter_host' => 'nullable|string',
            'inter_client_id' => 'nullable|string',
            'inter_client_secret' => 'nullable|string',
            'inter_scope' => 'nullable|string',
            'inter_chave_pix' => 'nullable|string',
            'inter_webhook_url_billet' => 'nullable|string',
            'inter_webhook_url_pix' => 'nullable|string',
            'inter_crt_file' => 'nullable|file',
            'inter_key_file' => 'nullable|file',
            
            // Asaas
            'environment_asaas' => 'nullable|in:Teste,Produção',
            'at_asaas_prod' => 'nullable|string',
            'at_asaas_test' => 'nullable|string',
            
            // WhatsApp (IntegreAI — tenant externo)
            'whatsapp' => ['nullable', 'string', $this->whatsappValidationRule()],
            'api_session_whatsapp' => 'nullable|string',
            'api_status_whatsapp' => 'nullable|string',
            'whatsapp_provider' => 'nullable|in:evogo,ycloud',
            'integreai_instance_id' => 'nullable|integer|min:1',
            'typebot_id' => 'nullable|string',
            'typebot_enable' => 'nullable|in:s,n',
        ]);

        $data = $this->normalizeWhatsappInput($data);
        if ($request->hasFile('inter_crt_file')) {
            $data['inter_crt_file'] = $request->file('inter_crt_file')->store('companies/certificates', 'local');
        }
        
        if ($request->hasFile('inter_key_file')) {
            $data['inter_key_file'] = $request->file('inter_key_file')->store('companies/certificates', 'local');
        }

        unset($data['api_session_whatsapp'], $data['api_status_whatsapp'], $data['api_token_whatsapp']);

        $company->update($data);
        
        return redirect()->route('companies.integrations', $company)
            ->with('success', 'Integrações atualizadas com sucesso!');
    }
    
    /**
     * Check WhatsApp connection status (IntegreAI API M2M)
     */
    public function whatsappStatus(Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        try {
            $result = $whatsApp->getStatus($company);

            if (! $result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erro ao verificar status',
                ], empty($company->integreai_instance_id) ? 400 : 500);
            }

            $company->refresh();

            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'provider' => $result['provider'] ?? $whatsApp->resolveProvider($company),
                'message' => $result['message'],
                'external_tenant_id' => $result['external_tenant_id'] ?? $whatsApp->externalTenantId($company),
                'session_name' => $company->api_session_whatsapp,
                'integreai_instance_id' => $company->integreai_instance_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lookup WhatsApp in IntegreAI CRM by number and optionally connect
     */
    public function whatsappLookup(Request $request, Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        $data = $request->validate([
            'whatsapp' => ['required', 'string', $this->whatsappValidationRule()],
            'auto_connect' => 'nullable|boolean',
        ]);

        try {
            $result = $whatsApp->lookupByWhatsapp(
                $company,
                $data['whatsapp'],
                (bool) ($data['auto_connect'] ?? false)
            );

            return response()->json([
                'success' => $result['success'] ?? false,
                'found' => $result['found'] ?? false,
                'status' => $result['status'] ?? $company->fresh()->api_status_whatsapp,
                'provider' => $result['provider'] ?? $whatsApp->resolveProvider($company),
                'supports_qrcode' => $result['supports_qrcode'] ?? $whatsApp->supportsQrCode($company),
                'message' => $result['message'] ?? '',
                'instance' => $result['instance'] ?? null,
                'instances' => $result['instances'] ?? [],
                'whatsapp' => $result['whatsapp'] ?? normalizeBrazilWhatsapp($data['whatsapp']),
                'external_tenant_id' => $result['external_tenant_id'] ?? $whatsApp->externalTenantId($company->fresh()),
                'session_name' => $result['session_name'] ?? $company->fresh()->api_session_whatsapp,
            ], ($result['success'] ?? false) ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List WhatsApp instances available in IntegreAI CRM (M2M)
     */
    public function whatsappInstances(Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        try {
            $result = $whatsApp->listAvailableInstances($company);

            return response()->json($result, ($result['success'] ?? false) ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar instâncias: ' . $e->getMessage(),
                'instances' => [],
            ], 500);
        }
    }

    /**
     * Provision and link WhatsApp tenant (IntegreAI API M2M)
     */
    public function whatsappConnect(Request $request, Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        $data = $request->validate([
            'whatsapp' => ['nullable', 'string', $this->whatsappValidationRule()],
            'integreai_instance_id' => 'nullable|integer|min:1',
            'create_new' => 'nullable|boolean',
        ]);

        if (! empty($data['whatsapp'])) {
            $company->update(['whatsapp' => normalizeBrazilWhatsapp($data['whatsapp'])]);
            $company->refresh();
        }

        if (! empty($data['integreai_instance_id'])) {
            $company->update(['integreai_instance_id' => $data['integreai_instance_id']]);
            $company->refresh();
        }

        try {
            $result = $whatsApp->connect(
                $company,
                $data['integreai_instance_id'] ?? null,
                (bool) ($data['create_new'] ?? false)
            );

            return response()->json([
                'success' => $result['success'],
                'status' => $result['status'] ?? $company->fresh()->api_status_whatsapp,
                'provider' => $result['provider'] ?? $whatsApp->resolveProvider($company),
                'supports_qrcode' => $result['supports_qrcode'] ?? $whatsApp->supportsQrCode($company),
                'message' => $result['message'],
                'instances' => $result['instances'] ?? [],
                'instance' => $result['instance'] ?? null,
                'external_tenant_id' => $whatsApp->externalTenantId($company->fresh()),
                'session_name' => $company->fresh()->api_session_whatsapp,
            ], $result['success'] ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get WhatsApp QR Code (IntegreAI API M2M)
     */
    public function whatsappQrCode(Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        try {
            $result = $whatsApp->getQrCode($company);

            if (! $result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'qrcode' => $result['qrcode'],
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Send a test WhatsApp message through IntegreAI M2M API
     */
    public function whatsappTestSend(Request $request, Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (! $company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        $data = $request->validate([
            'number' => ['required', 'string', $this->whatsappValidationRule()],
            'text' => 'nullable|string|max:1000',
        ]);

        $number = normalizeBrazilWhatsapp($data['number']);
        if (! $number) {
            return response()->json([
                'success' => false,
                'message' => 'Número de destino inválido. Use +55, DDD e número com 8 ou 9 dígitos.',
            ], 422);
        }

        $text = trim((string) ($data['text'] ?? ''));
        if ($text === '') {
            $text = '*Teste Cobrança Segura*' . "\n\n"
                . 'Mensagem de teste enviada em ' . now()->format('d/m/Y H:i')
                . ' pela integração WhatsApp da empresa *' . ($company->trade_name ?: $company->name) . '*.';
        }

        try {
            $result = $whatsApp->sendText($company, $number, $text);

            return response()->json([
                'success' => (bool) ($result['success'] ?? false),
                'message' => $result['message'] ?? (($result['success'] ?? false) ? 'Mensagem enviada com sucesso.' : 'Erro ao enviar mensagem.'),
                'response' => $result['response'] ?? [],
            ], ($result['success'] ?? false) ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar teste: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect WhatsApp (IntegreAI API M2M — desvincula tenant por padrão)
     */
    public function whatsappDisconnect(Request $request, Company $company, IntegreAiWhatsAppService $whatsApp)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }

        try {
            $forceLocal = (bool) $request->boolean('force_local');
            $result = $forceLocal
                ? $whatsApp->resetWhatsappLink($company)
                : $whatsApp->disconnect($company);

            if (! $result['success'] && ! $forceLocal) {
                $result = $whatsApp->resetWhatsappLink($company);
            }

            if (! $result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], empty($company->integreai_instance_id) ? 400 : 500);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desconectar: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function whatsappValidationRule(): \Closure
    {
        return function ($attribute, $value, $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            if (! isValidBrazilWhatsapp((string) $value)) {
                $fail('WhatsApp inválido. Use +55, DDD (2 dígitos) e número com 8 ou 9 dígitos.');
            }
        };
    }

    protected function normalizeWhatsappInput(array $data): array
    {
        if (! empty($data['whatsapp'])) {
            $data['whatsapp'] = normalizeBrazilWhatsapp($data['whatsapp']);
        }

        return $data;
    }
}
