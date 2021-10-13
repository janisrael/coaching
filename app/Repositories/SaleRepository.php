<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SaleRepositoryInterface;
use learntotrade\salesforce\Sale;
use learntotrade\salesforce\fields\SaleFields;

class SaleRepository implements SaleRepositoryInterface
{
    private $result = [];

    public function all(): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live();
        }

        return $this->result;
    }

    public function computedCredits(array $data): array
    {
        $total = collect($data);

        return [
            'total_credits' => $total->sum('sessions'),
            'total_available' => $total->where('sessions_expiry', '>', now())->sum('sessions_remaining'),
            'total_expired' => $total->where('sessions_expiry', '<', now())->sum('sessions_remaining'),
            'total_refunded' => $total->sum('sessions_recredited'),
            'total_cancelled' => $total->sum('sessions_cancelled'),
        ];
    }

    public function live(): void
    {
        $portalUser = auth()->guard('portal')->user();
        $data = [];

        $sfCustomer = auth()->guard('portal')->check() ? 
                      [SaleFields::CUSTOMER.' = \''.$portalUser->salesforce_token.'\''] : 
                      [SaleFields::DATE.' >= '.date('Y-m-d')];

        $sf = resolve(Sale::class)->query(
            array_values(config('api.sf_sale')),
            $sfCustomer
        );

        if (count($sf)) {
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_sale') as $key => $val) {
                    if (isset($value[$val])) {
                        $data[$field][$key] = $value[$val];
                    }
                }
            }
        }

        $this->result = [
            'sales' => $data,
            'portal_user' => $portalUser,
        ];
    }

    public function dummy(): void
    {
        $this->result = [
            'sales' => [],
        ];
    }
}