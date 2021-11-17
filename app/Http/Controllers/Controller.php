<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use learntotrade\salesforce\Exceptions\SalesforceException;
use App\ErrorCodes;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $prependCustomerLog = true;

    private $benchMarkLog = '';

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

    public function setLog($title='', $data='', $type='debug')
    {
        if (in_array(class_basename($this), config('app.enable_log'))) {
            if ($this->prependCustomerLog and session()->has('portal_user')) {
                $this->benchMarkLog = strtoupper('REF#'.Str::random(5));
                Log::{$type}('['.$this->benchMarkLog.'] CUSTOMER');
                Log::{$type}(json_encode(session('portal_user')->only('salesforce_token', 'email')));
                Log::{$type}('-------');
                $this->prependCustomerLog = false;
            }

            Log::{$type}('['.$this->benchMarkLog.'] '.$title);
            Log::{$type}(json_encode($data));
            Log::{$type}('-------');
        }
    }
}
