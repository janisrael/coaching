<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use learntotrade\salesforce\Exceptions\SalesforceException;
use App\ErrorCodes;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Convenience wrapper for the data we need to return when receiving a salesforce exception
     * @param  \learntotrade\salesforce\Exceptions\SalesforceExceptionSalesforceException $e
     * @return array
     */
    public function catchSalesforceException(SalesforceException $e)
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }

        return $e->getMessage();
    }
}
