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

    public function live(): void
    {
        $data = [];

        $sfCustomer = auth()->guard('portal')->check() ? 
                      [SaleFields::CUSTOMER.' = \''.auth()->guard('portal')->user()->salesforce_token.'\''] : 
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
        ];
    }

    public function dummy(): void
    {
        $this->result = [
            'sales' => [],
        ];
    }
}