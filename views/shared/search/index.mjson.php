<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();
$searchRecordTypes = get_search_record_types();

// Just get the items that can be mapped...
foreach( loop('search_texts') as $searchText )
{
	$isItem = $searchRecordTypes[ $searchText[ 'record_type' ] ] == 'Item';
	if( $isItem )
	{
		// do something...
		$id = $searchText->record_id;
		$item = get_record_by_id( 'item', $id );

		// If it doesn't have location data, we're not interested.
		$hasLocation = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
		if( $hasLocation )
		{
			$itemMetadata = $this->itemJsonifier( $item );
			array_push( $multipleItemMetadata, $itemMetadata );
		}
	}

}

$metadata = array(
	'items'        => $multipleItemMetadata,
	'total_items'  => count( $multipleItemMetadata )
);

echo Zend_Json_Encoder::encode( $metadata );