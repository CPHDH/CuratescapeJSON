<?php
// Get the basic data
$itemMetadata = $this->itemJsonifier( $item );
echo Zend_Json_Encoder::encode( $itemMetadata );