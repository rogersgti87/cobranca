<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Se não houver empresa atual selecionada, selecionar a primeira
            if (!$user->current_company_id) {
                $firstCompany = $user->companies()->first();
                
                if ($firstCompany) {
                    $user->current_company_id = $firstCompany->id;
                    $user->save();
                } else {
                    // Usuário sem empresa vinculada
                    // Pode redirecionar para página de cadastro de empresa ou mostrar mensagem
                    if (!$request->is('admin/companies*')) {
                        return redirect()->route('companies.index')
                            ->with('warning', 'Você precisa estar vinculado a uma empresa para continuar.');
                    }
                }
            }
            
            // Disponibilizar a empresa atual no request
            if ($user->current_company_id) {
                $currentCompany = $user->companies()->where('companies.id', $user->current_company_id)->first();
                
                if ($currentCompany) {
                    $request->merge(['current_company' => $currentCompany]);
                    view()->share('currentCompany', $currentCompany);
                } else {
                    // Empresa atual não existe mais ou usuário não tem mais acesso
                    $firstCompany = $user->companies()->first();
                    
                    if ($firstCompany) {
                        $user->current_company_id = $firstCompany->id;
                        $user->save();
                        $request->merge(['current_company' => $firstCompany]);
                        view()->share('currentCompany', $firstCompany);
                    }
                }
            }
        }
        
        return $next($request);
    }
}
