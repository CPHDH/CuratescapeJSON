<?php

class CuratescapeJSON_View_Helper_SearchJsonifier extends Zend_View_Helper_Abstract
{
	public function __construct(){ }
	
	
	public function searchJsonifier( $searchText )
	{
		$type=$searchText->record_type;
		$id = $searchText->record_id;
		$result = get_record_by_id($type, $id );
		
		if($type=='Item'){
			
			if(metadata($result, 'has thumbnail')){
				$imgTag=item_image('square_thumbnail',array(),0,$result);
				if(preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $imgTag, $src)){
					$thumbSrc = array_pop($src);
				};
			}

			$itemMetadata=array(
				'result_id'=>$id,
				'result_type'=>$type,
				'result_title'=>$searchText->title,
				'result_thumbnail'=>$thumbSrc ? $thumbSrc : null,
			);
			
			return $itemMetadata;
			
		}elseif($type=='Tour'){

			$itemMetadata=array(
				'result_id'=>$id,
				'result_type'=>$type,
				'result_title'=>$searchText->title
			);
			
			return $itemMetadata;			
			
		}elseif($type=='File'){
			
			$thumbSrc=metadata($result,'has_derivative_image') ? file_display_url($result,'square_thumbnail') : null;
			$subtype=metadata($result,'mime_type');
			$subtype=explode('/',$subtype);

			$itemMetadata=array(
				'result_id'=>$id,
				'result_type'=>$type,
				'result_title'=>$searchText->title,
				'result_thumbnail'=>$thumbSrc ? $thumbSrc : null,
				'result_subtype'=>isset($subtype[0]) ? $subtype[0] : null,
			);
			
			return $itemMetadata;			
			
		}else{
			return false;
		}
	}
}