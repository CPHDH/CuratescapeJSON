<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();
$searchRecordTypes = get_search_record_types();

// Just get the items that can be mapped...
foreach( loop('search_texts') as $searchText )
{
   // If it doesn't have location data, we're not interested.
   
   $isItem= ($searchRecordTypes[$searchText['record_type']]=='Item') ? true : false;
   if($isItem)
   {
		// do something...
		$id=$searchText->record_id;
		$item=get_record_by_id('item',$id); 
		if($location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true ))
		{
			$itemMetadata = array(
		         'id'          => $item->id,		
		         'latitude'    => $location[ 'latitude' ],
		         'longitude'   => $location[ 'longitude' ],
		         'title'       => html_entity_decode( strip_formatting( metadata($item,array('Dublin Core','Title'),false) ) ),
			);
			
			array_push( $multipleItemMetadata, $itemMetadata );
		}
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
