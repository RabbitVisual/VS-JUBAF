<?php

namespace Modules\Marketplace\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CorreiosShippingService
{
    /**
     * Calcula opções de frete usando a API dos Correios (ou serviço equivalente)
     * e cacheia o resultado para reduzir latência e custo.
     *
     * @return array<int, array{service:string,label:string,price:float,deadline:string|null}>
     */
    public function calculate(string $cepOrigin, string $cepDest, float $weightKg, array $dimensionsCm = []): array
    {
        $cepOrigin = preg_replace('/\D/', '', $cepOrigin);
        $cepDest = preg_replace('/\D/', '', $cepDest);

        if (strlen($cepOrigin) !== 8 || strlen($cepDest) !== 8) {
            return [];
        }

        $length = (float) ($dimensionsCm['length'] ?? ($dimensionsCm[0] ?? 16));
        $width = (float) ($dimensionsCm['width'] ?? ($dimensionsCm[1] ?? 11));
        $height = (float) ($dimensionsCm['height'] ?? ($dimensionsCm[2] ?? 2));

        $weightKg = max(0.01, $weightKg);

        $cacheKey = sprintf(
            'marketplace_freight:%s:%s:%.3f:%.1f:%.1f:%.1f',
            $cepOrigin,
            $cepDest,
            $weightKg,
            $length,
            $width,
            $height
        );

        return Cache::remember($cacheKey, now()->addHour(), function () use ($cepOrigin, $cepDest, $weightKg, $length, $width, $height) {
            $baseUrl = Config::get('services.correios.base_url');
            $companyCode = Config::get('services.correios.company_code');
            $password = Config::get('services.correios.password');
            $services = Config::get('services.correios.services', [
                '04014' => 'SEDEX',
                '04510' => 'PAC',
            ]);

            if (empty($baseUrl)) {
                return [
                    [
                        'service' => 'fixed_sedex',
                        'label' => 'SEDEX (estimado)',
                        'price' => round(25 + $weightKg * 5, 2),
                        'deadline' => null,
                    ],
                    [
                        'service' => 'fixed_pac',
                        'label' => 'PAC (estimado)',
                        'price' => round(18 + $weightKg * 3, 2),
                        'deadline' => null,
                    ],
                ];
            }

            $results = [];

            foreach ($services as $code => $label) {
                try {
                    $response = Http::timeout(8)->get($baseUrl, [
                        'nCdEmpresa' => $companyCode,
                        'sDsSenha' => $password,
                        'nCdServico' => $code,
                        'sCepOrigem' => $cepOrigin,
                        'sCepDestino' => $cepDest,
                        'nVlPeso' => max(1, ceil($weightKg * 1000) / 1000),
                        'nCdFormato' => 1,
                        'nVlComprimento' => max(16, $length),
                        'nVlAltura' => max(2, $height),
                        'nVlLargura' => max(11, $width),
                        'nVlDiametro' => 0,
                        'sCdMaoPropria' => 'N',
                        'nVlValorDeclarado' => 0,
                        'sCdAvisoRecebimento' => 'N',
                        'StrRetorno' => 'json',
                    ]);

                    if (! $response->ok()) {
                        continue;
                    }

                    $payload = $response->json();

                    $price = null;
                    $deadline = null;

                    if (is_array($payload)) {
                        if (isset($payload['cServico'])) {
                            $serviceData = $payload['cServico'];
                            $price = (float) str_replace(',', '.', $serviceData['Valor'] ?? '0');
                            $deadline = $serviceData['PrazoEntrega'] ?? null;
                        } elseif (isset($payload['price'])) {
                            $price = (float) $payload['price'];
                            $deadline = $payload['deadline'] ?? null;
                        }
                    }

                    if ($price === null || $price <= 0) {
                        continue;
                    }

                    $results[] = [
                        'service' => (string) $code,
                        'label' => $label,
                        'price' => $price,
                        'deadline' => $deadline,
                    ];
                } catch (\Throwable $e) {
                    continue;
                }
            }

            if (empty($results)) {
                return [
                    [
                        'service' => 'fallback',
                        'label' => 'Entrega padrão (estimada)',
                        'price' => round(20 + $weightKg * 4, 2),
                        'deadline' => null,
                    ],
                ];
            }

            usort($results, fn ($a, $b) => $a['price'] <=> $b['price']);

            return $results;
        });
    }
}

