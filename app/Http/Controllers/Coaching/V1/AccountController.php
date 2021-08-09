<?php

namespace App\Http\Controllers\Coaching\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Http\Resources\SaleResource;

class AccountController extends Controller
{
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
    public function person()
    {
        return new AccountResource(Auth::guard('portal')->user());
    }
    
    /**
     * Get All Customer Credits
     * 
     * @return json
     */
    public function sales()
    {
        $data = $this->saleRepository->all();

        $data['computed_credits'] = $this->saleRepository->computedCredits($data['sales']);

        return SaleResource::collection(collect($data));
    }
}
