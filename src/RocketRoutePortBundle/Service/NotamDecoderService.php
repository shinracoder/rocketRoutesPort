<?php
/**
 * Created by PhpStorm.
 * User: peteratkins
 * Date: 08/05/2016
 * Time: 08:45
 */

namespace RocketRoutePortBundle\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;

class NotamDecoderService {

	/**
	 * @var \RocketRoutePortBundle\Service\ContainerInterfacen
	 */
	protected $container;

	public function __construct(ContainerInterface $container) {

		$this->container = $container;

	}

	public function decodeNotam($notam){

		$notamArray = [];



		if (isset( $notam['ItemQ'] )) {

			$notamArray['itemQ'] = $this->decodeItemQ( $notam['ItemQ'] );

		}

		if (isset( $notam['ItemA'] )) {

			$notamArray['itemA'] = $notam['ItemA'];

		}

		if (isset( $notam['ItemB'] )) {

			$notamArray['itemB'] = $notam['ItemB'];

		}

		if (isset( $notam['ItemC'] )) {

			$notamArray['itemC'] = $notam['ItemC'];

		}

		if (isset( $notam['ItemD'] )) {

			$notamArray['itemD'] = $notam['ItemD'];

		}

		if (isset( $notam['ItemE'] )) {

			$notamArray['itemE'] = $notam['ItemE'];

		}

		return $notamArray;

		
	}


	public function decodeItemQ($itemQ){

		$itemQArray = [];

		if (!empty($itemQ)) {

			$items = explode( '/', $itemQ );

			if (count($items)) {

				$itemQArray = $geoLocation = $this->equateGeoLocation( array_pop( $items ) );
				//TODO decode remaining values and assigned then to $itemQArray

			}
		}

		return $itemQArray;
		

	}



	/**
	 *
	 * @param $geoLocation e.g. 5050N00018W005
	 *
	 * @return string
	 *
	 */
	public function equateGeoLocation($geoLocation){

		$returnArray = [];
		$originalGeoLocation = $geoLocation;

		if (!empty($geoLocation)) {


			$degree1 = mb_substr($geoLocation , 0 , 2);
			$hour1 = mb_substr($geoLocation , 2 , 2 );

			$geoLocation = substr( $geoLocation, 4 );

			if ( is_numeric( mb_substr( $geoLocation, - 3 ) ) ) {

				$geoLocation = substr( $geoLocation, 0, - 3 );

			}

			$hour2 = substr(substr( $geoLocation, - 3 ), 0 , -1);

			$geoLocation = substr(substr( $geoLocation, 0, - 3 ), 1 , 3);

			$degree2 = $geoLocation;

			//Equate Longitude and Latitude
			$latitude = $degree1 + $hour1 / 60;
			$longitude = $degree2 + $hour2 / 60;

			$longitude = strstr($originalGeoLocation, 'W')? '-'.$longitude : $longitude;
			$latitude =  strstr($originalGeoLocation, 'S')? '-'.$latitude : $latitude;

			$returnArray = [
				'longitude' => $longitude,
				'latitude' => $latitude,
				'location' => $originalGeoLocation,
			];


			return $returnArray;

		}

		
	}


	
}