<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();
$searchRecordTypes = get_search_record_types();

// Just get the items that can be mapped...
foreach( loop('search_texts') as $searchText )
{
	$searchTextsMetadata = $this->searchJsonifier( $searchText );
	if( $searchText[ 'record_type' ] == 'Item' ){
		// check to see if item has location
		if(get_db()->getTable( 'Location' )->findLocationByItem( $searchText->record_id, true )){
			array_push( $multipleItemMetadata, $searchTextsMetadata );
		}
	}elseif( $searchText[ 'record_type' ] == 'File' ){
		// check to see if PARENT item has location
		$parent_id=$searchTextsMetadata['result_parent_id'];
		if( $parent_id && get_db()->getTable( 'Location' )->findLocationByItem( $parent_id, true ) ){
			array_push( $multipleItemMetadata, $searchTextsMetadata );
		}
	}else{
		array_push( $multipleItemMetadata, $searchTextsMetadata );
	}
}

$metadata = array(
	'items' => $multipleItemMetadata,
	'total_items' => count( $multipleItemMetadata )
);

echo json_encode( $metadata );