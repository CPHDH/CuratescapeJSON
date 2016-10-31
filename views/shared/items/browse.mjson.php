<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
foreach( loop( 'item' ) as $item )
{
	if($itemMetadata = $this->itemJsonifier( $item , false)){
		array_push( $multipleItemMetadata, $itemMetadata );
	}
	
}

$metadata = array(
	'items'        => $multipleItemMetadata,
	'total_items'  => count( $multipleItemMetadata )
);

echo Zend_Json_Encoder::encode( $metadata );