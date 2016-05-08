<?php

namespace RocketRoutePortBundle\Controller;

use RocketRoutePortBundle\Service\NotamDecoderService;
use RocketRoutePortBundle\Service\NotamService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotamController extends Controller
{

    /**
     * @var \RocketRoutePortBundle\Service\RocketRouteNotamService
     */
    private $notamService;

    /**
     * @var \RocketRoutePortBundle\Service\NotamDecoderService
     */
    private $notamDecoderService;

    public function __construct(NotamService $notamService, NotamDecoderService $notamDecoderService) {

        $this->notamService = $notamService;
        $this->notamDecoderService = $notamDecoderService;

    }


    public function indexAction()
    {

        return $this->render('RocketRoutePortBundle:Notam:index.html.twig', array(
            // ...
        ));
    }


    public function ajaxAction(Request $request){


        $response = new JsonResponse();

        if ($request->isMethod('POST')) {

            if ($request->get('icaoSearch')){

                $icaoCode = $request->get('icaoCode');

                if (!empty($icaoCode) && preg_match('/^[A-Za-z0-9]+$/',$icaoCode)) {

                    $notamsResponse = $this->notamService->getNotamResults( $icaoCode );
                    
                    $notams = $this->notamService->decodeNotams($notamsResponse);

                    $response->setData($notams);

                    return $response;

                }


            }


        }

        return $response;

        
        
    }

}
