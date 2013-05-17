<?php 

// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
while( loop_items() ) {
   $item = get_current_item();
   $location = get_db()->getTable('Location' )->findLocationByItem( $item, true );
   
   if((item('public'))&&($location)){ // make sure the item is public and has location data
   
	   $itemMetadata = array();
	
	   $itemMetadata['id'] = item( 'id' );
	   $itemMetadata['title'] = html_entity_decode(
	      strip_formatting( item( 'Dublin Core', 'Title' ) ) );
	
	      $itemLatitude = $location['latitude'];
	      $itemLongitude = $location['longitude'];
	
	      $itemMetadata = array_merge( $itemMetadata,
	         array(
	            'latitude' => $itemLatitude,
	            'longitude' => $itemLongitude,
	         )
	      );
	  
	   array_push($multipleItemMetadata, $itemMetadata);
   
   }
}

$metadata = array(
   'items'        => $multipleItemMetadata,
   'total_items'  => total_results(),
);

// I've heard that the Zend JSON encoder is really slow,
// if this becomes a problem, use the second line.
echo Zend_Json_Encoder::encode( $metadata );
//echo json_encode( $metadata );
