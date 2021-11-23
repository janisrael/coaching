<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SaleRepositoryInterface;
use learntotrade\salesforce\Sale;
use learntotrade\salesforce\fields\SaleFields;
use learntotrade\salesforce\fields\PersonFields;
use Illuminate\Support\Str;

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

        $emptyExpiry = $total->where('sessions_expiry', '=', null);

        $fullyPaid = $total->where('sessions_expiry', '!=', null)
                           ->where('payment_schedule', '=', 'Fully Paid');
        
        $childSale = $total->where('sessions_expiry', '!=', null)
                           ->where('is_child_sale', '=', true)
                           ->where('payment_schedule', '=', null);

        $totalChildSale = $childSale->count() > 0
                            ? $childSale->where('sessions_expiry', '>', now()->format('Y-m-d'))->sum('sessions_remaining')
                            : 0.0;

        $totalAvailable = $fullyPaid->count() > 0
                            ? $fullyPaid->where('sessions_expiry', '>', now()->format('Y-m-d'))->sum('sessions_remaining') 
                            : 0.0;

        $totalExpiry = $fullyPaid->count() > 0
                            ? $fullyPaid->where('sessions_expiry', '<', now()->format('Y-m-d'))->sum('sessions_remaining') 
                            : 0.0;

        $totalEmptyExpiry = $emptyExpiry->count() > 0
                            ? $emptyExpiry->sum('sessions_remaining')
                            : 0.0;

        return [
            'total_credits' => $total->sum('sessions'),
            'total_available' => ($totalAvailable + $totalChildSale),
            'total_expired' => ($totalExpiry + $totalEmptyExpiry),
            'total_refunded' => $total->sum('sessions_recredited'),
            'total_cancelled' => $total->sum('sessions_cancelled'),
        ];
    }

    public function live($resource=''): void
    {
        $portalUser = session('portal_user');
        $data = [];

        $sfCustomer = [
            SaleFields::CUSTOMER.' = \''.$portalUser->salesforce_token.'\''
        ];

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
                        if ($key == 'record_type_id') {
                            $data[$field]['is_child_sale'] = Str::contains($value[$val], config('app.sf_child_sale_id'));
                        }
                    }
                }
            }
        }

        $this->result = ['sales' => $data];

        if ($resource == '') {
            $portal_user = $portalUser->toArray();
            $person = session('sf_customer');
            $portal_user['customer_group'] = $person[PersonFields::CUSTOMER_GROUP] ?: config('app.customer_default.group');
            $portal_user['customer_region'] = $person[PersonFields::REGION] ?: config('app.customer_default.region');
            $portal_user['customer_type'] = $person[PersonFields::CUSTOMER_TYPE] ?: config('app.customer_default.type');
            
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