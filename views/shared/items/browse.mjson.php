<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
foreach( loop( 'item' ) as $item )

function getDublinText( $element, $formatted = false )
{
   $raw = metadata( 'item', array( 'Dublin Core', $element ) );
   if( ! $formatted )
      $raw = strip_formatting( $raw );

   return html_entity_decode( $raw );
}

function has_element( $name )
{
   return count( get_records( 'Element', array( 'element_name' => $name ) ) ) > 0;
}


   // If it doesn't have location data, we're not interested.
   $location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
   if( $location )
   {
   
	$itemMetadata = array();
	
	// Add the item ID and title
	$itemMetadata['id'] = $item->id;
	$itemMetadata['title'] = html_entity_decode(
	strip_formatting( metadata( 'item', array( 'Dublin Core', 'Title' ) ) ) );
	
	// Add the description
	$itemMetadata['description'] = html_entity_decode(
	strip_formatting( metadata( 'item', array( 'Dublin Core', 'Description' ) ) ) );   
	
	// Add the location
	$itemMetadata['latitude'] = $location['latitude'];
	$itemMetadata['longitude'] = $location['longitude'];


   array_push( $multipleItemMetadata, $itemMetadata );
   
   }

   
}

$metadata = array(
   'items'        => $multipleItemMetadata,
   'total_items'  => count( $multipleItemMetadata )
);

// I've heard that the Zend JSON encoder is really slow,
// if this becomes a problem, use the second line.
echo Zend_Json_Encoder::encode( $metadata );
//echo json_encode( $metadata );
