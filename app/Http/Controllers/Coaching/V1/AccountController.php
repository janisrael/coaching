<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Http\Resources\SaleResource;
use App\Traits\ValidateSessionTrait;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use ValidateSessionTrait;
    
    private $saleRepository;

    public function __construct(SaleRepositoryInterface $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /**
     * Get Person Account
     * 
     * @return json
     */
    public function person(Request $request)
    {
        $this->validateSession($request);
        
        return new AccountResource(session('portal_user'));
    }
    
    /**
     * Get All Customer Credits
     * 
     * @return json
     */
    public function sales(Request $request)
    {
        $this->validateSession($request);
        $this->setLog('SALES: REQUEST', $request->all());
        
        $data = $this->saleRepository->all();

        $data['computed_credits'] = $this->saleRepository->computedCredits($data['sales']);

        foreach ($data['sales'] as $key => $sale) {
            if ($sale['is_child_sale'] == true AND $sale['date_fully_paid_not_null_parent'] == null) {
                unset($data['sales'][$key]);
            }
        }
        
        $this->setLog('SALES:', $data['sales']);
        $this->setLog('SALES: PORTAL_USER', $data['portal_user']->except('portal_user_details'));
        $this->setLog('SALES: COMPUTED_CREDITS', $data['computed_credits']);

        return SaleResource::collection(collect($data));
    }
}
