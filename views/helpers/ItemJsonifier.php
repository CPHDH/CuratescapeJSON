<?php

class CuratescapeJSON_View_Helper_ItemJsonifier extends Zend_View_Helper_Abstract
{
	public function __construct()
	{
		// Determine if the item type schemas have custom elements
		$this->hasStory = element_exists('Item Type Metadata','Story');
		$this->hasSubtitle = element_exists('Item Type Metadata','Subtitle');
		$this->hasSponsor = element_exists('Item Type Metadata','Sponsor');
		$this->hasLede = element_exists('Item Type Metadata','Lede');
		$this->hasAccessInfo = element_exists('Item Type Metadata','Access Information');
		$this->hasWebsite = element_exists('Item Type Metadata','Official Website');
		$this->hasStreetAddress = element_exists('Item Type Metadata','Street Address');
		$this->hasFactoid = element_exists('Item Type Metadata','Factoid');
		$this->hasRelatedResources = element_exists('Item Type Metadata','Related Resources');

		$this->storage = Zend_Registry::get('storage');
	}

	private static function getDublinText( $element, $formatted = false )
	{
		$raw = metadata( 'item', array( 'Dublin Core', $element ) );
		if( ! $formatted )
			$raw = strip_formatting( $raw );

		return html_entity_decode( $raw );
	}

	private static function getItemTypeText( $element, $formatted = false )
	{
		$raw = metadata( 'item', array( 'Item Type Metadata', $element ) );
		if( ! $formatted )
			$raw = strip_formatting( $raw );

		return html_entity_decode( $raw );
	}

	public function itemJsonifier( $item, $isExtended = false )
	{
		/* Core metadata */

		$location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );

		$titles = metadata( 'item', array( 'Dublin Core', 'Title' ), array( 'all' => true ) );

		$itemMetadata = array(
			'id'          => $item->id,
			'featured'    => $item->featured,
			'latitude'    => $location[ 'latitude' ],
			'longitude'   => $location[ 'longitude' ],
			'title'       => html_entity_decode( strip_formatting( $titles[0] ) ),
		);


		if( $this->hasStreetAddress){
			$itemMetadata['address']=self::getItemTypeText('Street Address',true);
		}

		if(metadata($item, 'has thumbnail')){
			$itemMetadata[ 'thumbnail' ] = (preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('square_thumbnail'), $result)) ? array_pop($result) : null;
		}

		/* Extended Metadata */

		if($isExtended){

			$itemMetadata['modified']=$item->modified;
			

			$authors = metadata( 'item', array( 'Dublin Core', 'Creator' ),array( 'all' => true ) );
			$authorsStripped = array();
			foreach( $authors as $auth )
			{
				$authorsStripped[] = html_entity_decode( strip_formatting( $auth ) );
			}
			
			$itemMetadata['creator']=$authorsStripped;


			if( $this->hasStory)
			{
				$itemMetadata['description']=self::getItemTypeText('Story',true);
			}

			if(!$itemMetadata['description'])
			{
				$itemMetadata['description']=self::getDublinText( 'Description', true );
			}

			if( $this->hasLede )
			{
				$itemMetadata['lede']=self::getItemTypeText('Lede');
			}

			if( $this->hasSponsor )
			{
				$itemMetadata['sponsor']=self::getItemTypeText('Sponsor');;
			}

			if( $this->hasSubtitle )
			{
				$itemMetadata['subtitle'] = self::getItemTypeText('Subtitle');
			}

			if( !$itemMetadata['subtitle'] && count( $titles ) > 1 )
			{
				$itemMetadata['subtitle'] = html_entity_decode( strip_formatting( $titles[1] ) );
			}

			if( $this->hasAccessInfo )
			{
				$itemMetadata[ 'accessinfo' ] = self::getItemTypeText('Access Information');
			}

			if( $this->hasWebsite )
			{
				$itemMetadata[ 'website' ] = self::getItemTypeText('Official Website',true);
			}

			if( $this->hasRelatedResources )
			{
				$itemMetadata[ 'related-resources' ] = self::getItemTypeText('Related Resources',true);
			}

			if( $this->hasFactoid )
			{
				$itemMetadata[ 'factoid' ] = self::getItemTypeText('Factoid',true);
			}

			// Add files
			$files = array();
			foreach( $item->Files as $file )
			{
				
				$mimetype = metadata( $file, 'MIME Type' );

				$filedata = array(
					'id'        => $file->id,
					'mime-type' => $mimetype,
					'modified'  => $file->modified );

				$title = metadata( $file, array( 'Dublin Core', 'Title' ) );
				if( $title )
				{
					$filedata[ 'title' ] = strip_formatting( $title );
				}

				if( $file->hasThumbnail() )
				{
					$filedata[ 'thumbnail' ] = $file->getWebPath( 'square_thumbnail' );
				}

				if( strpos( $mimetype, 'image/' ) === 0 )
				{
					$path = $file->getWebPath( 'fullsize' );
					list( $width, $height ) = getimagesize( $path );
					$filedata[ 'width' ] = $width;
					$filedata[ 'height' ] = $height;
				}else{
					$path = $file->getWebPath( 'original' );
				}

				$caption = array();
				$description = metadata( $file, array( 'Dublin Core', 'Description' ) );
				if( $description )
				{
					$caption[] = $description;
				}

				$source = metadata( $file, array( 'Dublin Core', 'Source' ) );
				if( $source )
				{
					$caption[] = $source;
				}

				$creator = metadata( $file, array( 'Dublin Core', 'Creator' ) );
				if( $creator )
				{
					$caption[] = $creator;
				}

				if( count( $caption ) )
				{
					$filedata[ 'description' ] = implode( " | ", $caption );
				}

				$files[ $path ] = $filedata;

			}

			if( count( $files ) > 0 )
			{
				$itemMetadata[ 'files' ] = $files;
			}
		}

		return $itemMetadata;
	}
}