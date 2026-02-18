<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
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
            'whatsapp' => 'nullable|string|max:20',
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

        // Upload logo se fornecido
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
            'whatsapp' => 'nullable|string|max:20',
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
            'api_token_whatsapp' => 'nullable|string',
        ]);

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
        
        return view('admin.companies.integrations', compact('company'));
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
            
            // WhatsApp
            'api_session_whatsapp' => 'nullable|string',
            'api_token_whatsapp' => 'nullable|string',
            'typebot_id' => 'nullable|string',
            'typebot_enable' => 'nullable|in:s,n',
        ]);
        
        // Upload certificados Inter se fornecidos
        if ($request->hasFile('inter_crt_file')) {
            $data['inter_crt_file'] = $request->file('inter_crt_file')->store('companies/certificates', 'local');
        }
        
        if ($request->hasFile('inter_key_file')) {
            $data['inter_key_file'] = $request->file('inter_key_file')->store('companies/certificates', 'local');
        }
        
        $company->update($data);
        
        return redirect()->route('companies.integrations', $company)
            ->with('success', 'Integrações atualizadas com sucesso!');
    }
    
    /**
     * Check WhatsApp connection status (Evolution API v2)
     */
    public function whatsappStatus(Company $company)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }
        
        if (!$company->api_session_whatsapp || !$company->api_token_whatsapp) {
            return response()->json([
                'success' => false,
                'message' => 'Configurações do WhatsApp não estão completas'
            ], 400);
        }
        
        try {
            // Evolution API v2: GET /instance/connectionState/{instance}
            $apiUrl = rtrim(env('API_URL_EVOLUTION', 'https://evolution.api.com'), '/');
            
            $response = \Http::withHeaders([
                'apikey' => $company->api_token_whatsapp
            ])->get($apiUrl . '/instance/connectionState/' . $company->api_session_whatsapp);
            
            if ($response->successful()) {
                $data = $response->json();
                $state = $data['instance']['state'] ?? 'unknown';
                
                // Estados possíveis: open, close, connecting
                $isConnected = $state === 'open';
                $statusText = $isConnected ? 'open' : $state;
                
                $company->update(['api_status_whatsapp' => $statusText]);
                
                $statusMessages = [
                    'open' => 'WhatsApp Conectado! ✓',
                    'close' => 'WhatsApp Desconectado',
                    'connecting' => 'WhatsApp está conectando...',
                ];
                
                return response()->json([
                    'success' => true,
                    'status' => $statusText,
                    'message' => $statusMessages[$state] ?? "Status: {$state}"
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status: ' . $response->body()
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get WhatsApp QR Code (Evolution API v2)
     */
    public function whatsappQrCode(Company $company)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }
        
        if (!$company->api_session_whatsapp || !$company->api_token_whatsapp) {
            return response()->json([
                'success' => false,
                'message' => 'Configurações do WhatsApp não estão completas'
            ], 400);
        }
        
        try {
            // Evolution API v2: GET /instance/connect/{instance}
            $apiUrl = rtrim(env('API_URL_EVOLUTION', 'https://evolution.api.com'), '/');
            
            $response = \Http::withHeaders([
                'apikey' => $company->api_token_whatsapp
            ])->get($apiUrl . '/instance/connect/' . $company->api_session_whatsapp);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // O QR Code pode vir em base64 ou como URL
                $qrcode = $data['base64'] ?? $data['qrcode']['base64'] ?? $data['code'] ?? null;
                
                if ($qrcode) {
                    // Se não tiver o prefixo data:image, adicionar
                    if (!str_starts_with($qrcode, 'data:image')) {
                        $qrcode = 'data:image/png;base64,' . $qrcode;
                    }
                    
                    return response()->json([
                        'success' => true,
                        'qrcode' => $qrcode,
                        'message' => 'QR Code obtido com sucesso! Escaneie com seu WhatsApp.'
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code não disponível. A instância pode já estar conectada.'
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter QR Code: ' . $response->body()
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Disconnect WhatsApp (Evolution API v2)
     */
    public function whatsappDisconnect(Company $company)
    {
        if (!$company->isAdminOrOwner(Auth::id())) {
            abort(403, 'Você não tem permissão para gerenciar integrações');
        }
        
        if (!$company->api_session_whatsapp || !$company->api_token_whatsapp) {
            return response()->json([
                'success' => false,
                'message' => 'Configurações do WhatsApp não estão completas'
            ], 400);
        }
        
        try {
            // Evolution API v2: DELETE /instance/logout/{instance}
            $apiUrl = rtrim(env('API_URL_EVOLUTION', 'https://evolution.api.com'), '/');
            
            $response = \Http::withHeaders([
                'apikey' => $company->api_token_whatsapp
            ])->delete($apiUrl . '/instance/logout/' . $company->api_session_whatsapp);
            
            if ($response->successful()) {
                $company->update(['api_status_whatsapp' => 'close']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp desconectado com sucesso!'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desconectar: ' . $response->body()
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar com a API: ' . $e->getMessage()
            ], 500);
        }
    }
}
