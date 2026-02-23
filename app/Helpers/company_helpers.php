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

if (!function_exists('userCompanyIds')) {
    /**
     * Retorna todos os IDs de empresas que o usuário autenticado tem acesso
     *
     * @return array
     */
    function userCompanyIds()
    {
        if (auth()->check()) {
            return auth()->user()->companies()->pluck('companies.id')->toArray();
        }
        
        return [];
    }
}

if (!function_exists('applyCompanyFilter')) {
    /**
     * Aplica o filtro de empresa(s) adequado baseado no usuário autenticado
     * Se o usuário tem acesso a múltiplas empresas, filtra por todas elas
     * Caso contrário, filtra apenas pela empresa atual
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $columnName Nome da coluna company_id (padrão: 'company_id')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function applyCompanyFilter($query, $columnName = 'company_id')
    {
        if (!auth()->check()) {
            return $query->whereRaw('1 = 0');
        }
        
        $companyIds = userCompanyIds();
        
        if (empty($companyIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return $query->whereIn($columnName, $companyIds);
    }
}
