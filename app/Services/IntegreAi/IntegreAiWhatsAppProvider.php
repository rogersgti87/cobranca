<?php

namespace App\Services\IntegreAi;

class IntegreAiWhatsAppProvider
{
    public const EVOGO = 'evogo';

    public const YCLOUD = 'ycloud';

    public static function supported(): array
    {
        return [self::EVOGO, self::YCLOUD];
    }

    public static function normalize(?string $provider): string
    {
        $provider = strtolower(trim((string) $provider));

        return in_array($provider, self::supported(), true)
            ? $provider
            : self::default();
    }

    public static function default(): string
    {
        return self::normalize(config('services.integreai.whatsapp_provider'));
    }

    public static function label(string $provider): string
    {
        return match (self::normalize($provider)) {
            self::YCLOUD => 'YCloud (WhatsApp Oficial)',
            default => 'EVOGO (QR Code / sessão)',
        };
    }

    public static function supportsQrCode(string $provider): bool
    {
        return self::normalize($provider) === self::EVOGO;
    }

    public static function description(string $provider): string
    {
        return match (self::normalize($provider)) {
            self::YCLOUD => 'WhatsApp Business API oficial via YCloud. A instância é configurada no painel IntegreAI (BYOC). Não usa QR Code neste sistema.',
            default => 'Conexão por QR Code via EVOGO. Ideal para pareamento rápido com o app WhatsApp.',
        };
    }
}
