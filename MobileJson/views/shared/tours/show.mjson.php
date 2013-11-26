<?php

// Add enumarations of the ordered items in this tour.
$items = array();
foreach( $tour->Items as $item )
{
   set_current_record( 'item', $item );
   $location = get_db()->getTable('Location')->findLocationByItem($item, true);
   
   // If it has a location, we'll build the itemMetadata array and push it to items
   if($location){
   $item_metadata = array(
      'id'          => $item->id,
      'title'       => metadata( 'item', array( 'Dublin Core', 'Title' ) ),
	  'latitude' 	=> $location['latitude'],
	  'longitude' 	=> $location['longitude']      
   );
   array_push( $items, $item_metadata );
   }
  
}

// Create the array of data
$tour_metadata = array(
   'id'           => tour( 'id' ),
   'title'        => tour( 'title' ),
   'description'  => tour( 'description' ),
   'items'        => $items,
);

// Encode the array we've created.
echo Zend_Json_Encoder::encode( $tour_metadata );
