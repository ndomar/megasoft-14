<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OfferController
 *
 * @author sak
 */
class OfferController extends Controller {

    public function acceptOfferAction(Request $request) {
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        //$this->verify($offerId);
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    
}
