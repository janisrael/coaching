<?php

namespace App\Http\Controllers\Coaching\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PortalLogin;
use App\Http\Resources\AccountResource;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\SaleRepositoryInterface;
use App\Http\Resources\SaleResource;

class AccountController extends Controller
{
    private $portalLogin;
    private $saleRepository;

    public function __construct(PortalLogin $portalLogin, SaleRepositoryInterface $saleRepository)
    {
        $this->portalLogin = $portalLogin;
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

        $total = collect($data['sales']);

        $data['computed_credits'] = [
            'total_credits' => $total->sum('sessions'),
            'total_available' => $total->where('sessions_expiry', '>', now())->sum('sessions_remaining'),
            'total_expired' => $total->where('sessions_expiry', '<', now())->sum('sessions_remaining'),
            'total_refunded' => $total->sum('sessions_recredited'),
            'total_cancelled' => $total->sum('sessions_cancelled'),
        ];

        return SaleResource::collection(collect($data));
    }
}
