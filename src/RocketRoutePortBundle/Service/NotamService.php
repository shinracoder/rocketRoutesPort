<?php

namespace RocketRoutePortBundle\Service;


use Symfony\Component\Routing\Exception\InvalidParameterException;

class NotamService {

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var \SimpleXMLElement
	 */
	private $xmlRequest;

	/**
	 * @var \SoapClient
	 */
	private $client;

	/**
	 * @var NotamDecoderService
	 */
	private $notamDecoder;


	public function __construct(
		$url,
		$username,
		$password
	)
	{



		if (empty($url) || empty($password) || empty($username))
			throw new InvalidParameterException('A valid username and password must be set to use RocketRouteNotam Api ');

		$this->url = $url;

		$this->client = new \SoapClient($this->url, array('trace' => TRUE));

		$this->username = $username;
		$this->password = md5($password);

	}
	

	public function getNotamResults($icaoCode){
		

		$this->resetXmlRequest();


		//foreach ($notams as $notam){

		$this->xmlRequest->addChild('ICAO', $icaoCode);

		//}
		
		$request = $this->xmlRequest->asXML();



		$xml = simplexml_load_string($this->client->getNotam($request));
		$response = json_encode($xml);
		$response = json_decode($response, true);



		if (!empty($response['RESULT'])){

			return !empty($response['MESSAGE']) ? $response['MESSAGE'] : 'Something has gone wrong with your API request!';

		}

		return $this->formatArrayResponse($response);

	}

	public function resetXmlRequest(){

		$this->xmlRequest = new \SimpleXMLElement('<REQNOTAM/>');
		$this->xmlRequest->addChild('USR', $this->username);
		$this->xmlRequest->addChild('PASSWD', $this->password);

	}

	public function formatArrayResponse($response){

		$notamsArray = [];

		if (!empty($response['NOTAMSET'])) {

			$notamSet = $response['NOTAMSET'];

			foreach ($notamSet as $key => $notams){

				if (!empty($notams['ICAO'])){

					$notamKey = $notams['ICAO'];

				}

				if ($key == 'NOTAM'){

					$notamsArray[$notamKey] = $notams;

				}

			}

		}

		return $notamsArray;

	}

	public function decodeNotams(array $notams){

		$notamDecoded = [];


		foreach ($notams as $icao => $notam) {


			if (!empty($notam['@attributes'])){

				$notamDecoded[ $icao ][ 0 ] = $this->notamDecoder->decodeNotam( $notam );

			}else {

				foreach ( $notam as $key => $notamsArray ) {

					if ( $key == '@attributes' ) {
						continue;
					}

					$notamDecoded[ $icao ][ $key ] = $this->notamDecoder->decodeNotam( $notamsArray );

				}

			}

		}


		return $notamDecoded;

	}

	public function setNodtamDecoder(NotamDecoderService $notamDecoderService){

		$this->notamDecoder = $notamDecoderService;

	}


}