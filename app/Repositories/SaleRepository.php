<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SaleRepositoryInterface;
use learntotrade\salesforce\Sale;
use learntotrade\salesforce\fields\SaleFields;
use learntotrade\salesforce\Person;
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
        $sales = [];
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $value['is_child_sale'] = Str::contains($value['record_type_id'], config('app.sf_child_sale_id'));
                $sales[] = $value;
            } 
        }

        $total = collect($sales);
        $sessionsExpiry = $total->where('sessions_expiry', '!=', null)
                                ->where('is_child_sale', '=', false);

        $childSaleSession = $total->where('is_child_sale', '=', true);
        $totalChildSaleSession = $childSaleSession->count() > 0 
                            ? $childSaleSession->sum('sessions_remaining') 
                            : 0.0;

        $totalAvailable = $sessionsExpiry->count() > 0
                            ? ($sessionsExpiry->where('sessions_expiry', '>', now()->format('Y-m-d'))->sum('sessions_remaining') + $totalChildSaleSession) 
                            : $total->sum('sessions_remaining');

        $totalExpiry = $sessionsExpiry->count() > 0
                            ? $sessionsExpiry->where('sessions_expiry', '<', now()->format('Y-m-d'))->sum('sessions_remaining') 
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