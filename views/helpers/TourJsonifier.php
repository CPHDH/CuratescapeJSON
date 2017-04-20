<?php

class CuratescapeJSON_View_Helper_TourJsonifier extends Zend_View_Helper_Abstract
{
	public function __construct()
	{

	}

	public function tourJsonifier( $tour )
	{
		// Add enumarations of the ordered items in this tour.
		$items = array();
		foreach( $tour->Items as $item )
		{
			if($item->public){
				set_current_record( 'item', $item );
				$location = get_db()->getTable('Location')->findLocationByItem($item, true);
	
				// If it has a location, we'll build the itemMetadata array and push it to items
				if($location){
					$item_metadata = array(
						'id'          => $item->id,
						'title'       => trim( html_entity_decode( strip_formatting( metadata( 'item', array( 'Dublin Core', 'Title' ) ) ) ) ),
						'latitude'  => $location['latitude'],
						'longitude'  => $location['longitude']
					);
	
					if( element_exists('Item Type Metadata','Street Address') )
					{
						$address=metadata( 'item', array( 'Item Type Metadata', 'Street Address' ) );
						if($address){
							$item_metadata['address']=trim( html_entity_decode( strip_formatting( $address ) ) );
						}
					}
	
					array_push( $items, $item_metadata );
				}
			}

		}

		// Create the array of data
		$tour_metadata = array(
			'id'           => $tour->id,
			'title'        => $tour->title,
			'creator'      => $tour->credits,
			'description'  => nl2p($tour->description),
			'postscript_text' => $tour->postscript_text,
			'items'        => $items );


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