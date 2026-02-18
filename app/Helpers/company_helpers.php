<?php

if (!function_exists('currentCompanyId')) {
    /**
     * Retorna o ID da empresa atual do usuário autenticado
     *
     * @return int|null
     */
    function currentCompanyId()
    {
        if (auth()->check()) {
            return auth()->user()->current_company_id;
        }
        
        return null;
    }
}

if (!function_exists('currentCompany')) {
    /**
     * Retorna a empresa atual do usuário autenticado
     *
     * @return \App\Models\Company|null
     */
    function currentCompany()
    {
        if (auth()->check() && auth()->user()->current_company_id) {
            return \App\Models\Company::find(auth()->user()->current_company_id);
        }
        
        return null;
    }
}

if (!function_exists('hasCompanyAccess')) {
    /**
     * Verifica se o usuário autenticado tem acesso à empresa especificada
     *
     * @param int $companyId
     * @return bool
     */
    function hasCompanyAccess($companyId)
    {
        if (auth()->check()) {
            return auth()->user()->belongsToCompany($companyId);
        }
        
        return false;
    }
}

if (!function_exists('switchCompany')) {
    /**
     * Troca a empresa atual do usuário autenticado
     *
     * @param int $companyId
     * @return bool
     */
    function switchCompany($companyId)
    {
        if (auth()->check()) {
            return auth()->user()->switchCompany($companyId);
        }
        
        return false;
    }
}
