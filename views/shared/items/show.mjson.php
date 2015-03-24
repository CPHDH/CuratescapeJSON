<?php

// Get the basic data
$itemMetadata = $this->itemJsonifier( $item , true);

// I've heard that the Zend JSON encoder is really slow,
// if this becomes a problem, use the second line.
echo Zend_Json_Encoder::encode( $itemMetadata );
//echo json_encode( $itemMetadata );
