<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate as FacadesGate;

class ApiController extends Controller 
{
    use ApiResponser;
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function allowedAdminAction()
    {
        if(FacadesGate::denies('admin-action')){
            throw new AuthorizationException('this action is unauthorized');
        }
    }
}
