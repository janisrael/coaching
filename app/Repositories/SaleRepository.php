<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SaleRepositoryInterface;
use Faker\Factory;
use learntotrade\salesforce\Sale;
use learntotrade\salesforce\fields\SaleFields;

class SaleRepository implements SaleRepositoryInterface
{
    private $result = [];

    private $api_options = [];

    public function all(): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live();
        }

        return [
            'sales' => $this->result['data']
        ];
    }

    public function live(): void
    {
        $data = [];
        $options = [];

        $sf = resolve(Sale::class)->query(
            array_values(config('api.sf_sale')),
            ['Customer_Name__c = \''.auth()->guard('portal')->user()->salesforce_token.'\'']
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
            'data' => $data
        ];
    }

    public function dummy(): void
    {
        $this->result = [
            'data' => [],
        ];
    }

    public function getResult(): array
    {
        return $this->result;
    }
}