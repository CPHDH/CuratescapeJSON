<?php

// Add enumarations of the ordered items in this tour.
$items = array();
foreach( $tour->Items as $item )
{
   set_current_record( 'item', $item );

   $item_metadata = array(
      'id'          => $item->id,
      'title'       => metadata( 'item', array( 'Dublin Core', 'Title' ) )   
   );

   $location = plugin_is_active('Geolocation','2.0') ? get_db()->getTable('Location')->findLocationByItem($item, true) : false;
   if($location){
	   array_push($item_metadata, array(
	   	'latitude' => $location['latitude'],
	   	'longitude' => $location['longitude']
	   	));
   }

   array_push( $items, $item_metadata );
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
