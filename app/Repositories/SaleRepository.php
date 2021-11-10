<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SaleRepositoryInterface;
use learntotrade\salesforce\Sale;
use learntotrade\salesforce\fields\SaleFields;
use learntotrade\salesforce\Person;
use learntotrade\salesforce\fields\PersonFields;

class SaleRepository implements SaleRepositoryInterface
{
    private $result = [];

    public function all($resource=''): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live($resource);
        }

        return $this->result;
    }

    public function computedCredits(array $data): array
    {
        $total = collect($data);

        $totalAvailable = $total->where('sessions_expiry', '!=', null);
        $totalAvailable = $totalAvailable->count() > 0
                            ? $totalAvailable->where('sessions_expiry', '>', now()->format('Y-m-d'))->sum('sessions_remaining') 
                            : $total->sum('sessions_remaining');

        $totalExpiry = $total->where('sessions_expiry', '!=', null);
        $totalExpiry = $totalExpiry->count() > 0
                            ? $totalExpiry->where('sessions_expiry', '<', now()->format('Y-m-d'))->sum('sessions_remaining') 
                            : 0.0;
        return [
            'total_credits' => $total->sum('sessions'),
            'total_available' => $totalAvailable,
            'total_expired' => $totalExpiry,
            'total_refunded' => $total->sum('sessions_recredited'),
            'total_cancelled' => $total->sum('sessions_cancelled'),
        ];
    }

    public function live($resource=''): void
    {
        $portalUser = auth()->guard('portal')->user();
        $data = [];

        $sfCustomer = auth()->guard('portal')->check() ? 
                      [SaleFields::CUSTOMER.' = \''.$portalUser->salesforce_token.'\''] : 
                      [SaleFields::DATE.' >= '.date('Y-m-d')];

        $sfCustomer[] = SaleFields::RECORD_TYPE_ID. ' IN (\'' . implode('\',\'', config('app.sf_sale_record_type_id')) . '\')';
        $sf = resolve(Sale::class)->query(
            array_values(config('api.sf_sale')),
            $sfCustomer
        );

        if (count($sf)) {
            foreach ($sf['records'] as $field => $value) {
                foreach (config('api.sf_sale') as $key => $val) {
                    if (in_array($val, array_keys($value))) {
                        $data[$field][$key] = $value[$val];
                    }
                }
            }
        }

        $this->result = ['sales' => $data];

        if ($resource == '') {
            $portal_user = $portalUser->toArray();
            $person = resolve(Person::class)->get(auth()->guard('portal')->user()->salesforce_token);
            $portal_user['customer_group'] = $person[PersonFields::CUSTOMER_GROUP] ? $person[PersonFields::CUSTOMER_GROUP] : "SC2";
            $portal_user['customer_region'] = $person[PersonFields::REGION];
            $portal_user['customer_type'] = $person[PersonFields::CUSTOMER_TYPE] ? $person[PersonFields::CUSTOMER_TYPE] : "Front End";
            
            $this->result['portal_user'] = collect($portal_user)->except(['id', 'password']);
        }
    }

    public function dummy(): void
    {
        $this->result = [
            'sales' => [],
        ];
    }
}