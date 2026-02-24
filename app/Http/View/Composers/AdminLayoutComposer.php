<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class AdminLayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (auth()->check() && auth()->user()->companies) {
            $certificatesWarnings = [];
            
            foreach (auth()->user()->companies as $company) {
                $certInfo = $company->getInterCertificateInfo();
                
                // Adiciona aviso se o certificado está expirado ou expira em breve
                if ($certInfo['exists'] && ($certInfo['expired'] || $certInfo['expires_soon'])) {
                    $certificatesWarnings[] = [
                        'company' => $company,
                        'cert_info' => $certInfo,
                    ];
                }
            }
            
            $view->with('certificatesWarnings', $certificatesWarnings);
        }
    }
}
