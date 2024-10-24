<?php
class CuratescapeJSON_View_Helper_TourJsonifier extends Zend_View_Helper_Abstract
{
	public function __construct(){

	}

	public function tourJsonifier( $tour, $isExtended = false ){
		$items = array();
		foreach( $tour->Items as $item ){

			if($item->public){
				set_current_record( 'item', $item );

				$location = get_db()->getTable('Location')->findLocationByItem($item, true);
				if($location){

					$item_metadata = array(
						'id' => $item->id,
						'title' => trim( html_entity_decode( strip_formatting( metadata( 'item', array( 'Dublin Core', 'Title' ) ) ) ) ),
						'latitude' => $location['latitude'],
						'longitude' => $location['longitude']
					);

					if(metadata($item, 'has thumbnail')){
						$item_metadata[ 'thumbnail' ] = (preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('square_thumbnail'), $result)) ? array_pop($result) : '';
						$item_metadata[ 'fullsize' ] = (preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('fullsize'), $result)) ? array_pop($result) : '';
					}
					
					if( element_exists('Item Type Metadata','Subtitle') ){
						$subtitle=metadata( 'item', array( 'Item Type Metadata', 'Subtitle' ) );
						if($subtitle){
							$item_metadata['subtitle']=trim( html_entity_decode( strip_formatting( $subtitle ) ) );
						}
					}

					if( element_exists('Item Type Metadata','Street Address') ){
						$address=metadata( 'item', array( 'Item Type Metadata', 'Street Address' ) );
						if($address){
							$item_metadata['address']=trim( html_entity_decode( strip_formatting( $address ) ) );
						}
					}
					
					if($isExtended && method_exists($tour,'getTourItem')){
						$c=$tour->getTourItem($item->id);
						$item_metadata['custom']['subtitle']=$c['subtitle'];
						$item_metadata['custom']['text']=$c['text'];
					}

					array_push( $items, $item_metadata );
				}
			}

		}

		// Create the array of data
		$tour_metadata = array(
			'id' => $tour->id,
			'ordinal' => isset($tour->ordinal) ? $tour->ordinal : 0,
			'featured' => isset($tour->featured) ? $tour->featured : 0,
			'title' => $tour->title,
			'creator' => $tour->credits,
			'description' => nl2p($tour->description),
			'postscript_text' => $tour->postscript_text,
			'tour_img' => isset($items[0]['fullsize']) ? $items[0]['fullsize'] : '',
			'items' => $items 
		);

		return $tour_metadata;
	}
}

function nl2p($string)
{
	$paragraphs = '';
	foreach (explode("\n", $string) as $line) {
		if (trim($line)) {
			$paragraphs .= $line . '<br><br>';
		}
	}
	return $paragraphs;
}