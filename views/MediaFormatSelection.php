<?php

abstract class MediaFormatSelection
{
	
	public static function PrintMediaFormatSelection($media)
	{
		//var_dump($media);
		$media->GetFormatTable($header, $body);
		
		echo '
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">'.$header['title'].'</h3></div>
						<div class="panel-body">';
		
		MediaFormatSelection::PrintHeaderInfos($media, $header);
		MediaFormatSelection::ListFormatForStream($media, $body, StreamEnum::Both);
		MediaFormatSelection::ListFormatForStream($media, $body, StreamEnum::Video_only);
		MediaFormatSelection::ListFormatForStream($media, $body, StreamEnum::Audio_only);
		
		echo '
						</div>
					</div>';
	}
	
	public static function PrintHeaderInfos($media, $header)
	{
		echo '
							<div class="row">
								<div class="col-lg-3">
									<div class="list-group-item" style="text-align: center; text-transform: uppercase;">
										<a href="'.$media->data->webpage_url.'" style="text-decoration:none;">
											<img class="h1"'.(empty($media->data->thumbnail) ? '' : ' src="'.$media->data->thumbnail.'"').' alt="'.$media->data->extractor.'" style="max-width: 100%; height: auto; line-height:2em; margin-top:0; margin-bottom:0;" />
										</a>
									</div>
								</div>
								<div class="col-lg-2">
									<div class="list-group">';
		$cnt = 0;
		
		if(!empty($media->data->creator))
			$cnt += MediaFormatSelection::PrintHeaderMiddleInfo('Creator', $media->data->creator);
		
		if(!empty($media->data->uploader))
			$cnt += MediaFormatSelection::PrintHeaderMiddleInfo('Uploader', $media->data->uploader);
		
		if(!empty($media->data->duration))
			$cnt += MediaFormatSelection::PrintHeaderMiddleInfo('Duration', date('i:s', $media->data->duration));
		
		if(!empty($media->data->upload_date) && $cnt < 3)
			$cnt += MediaFormatSelection::PrintHeaderMiddleInfo('Upload date', $media->data->upload_date);

		echo '
									</div>
								</div>
								<div class="col-lg-7">';
		
		if(!empty($media->data->description))
		{
			echo '
									<div class="list-group-item">
								  		<h4 class="list-group-item-heading">Description</h4>
								  		<p class="list-group-item-text" style="max-height:145px; overflow:auto; white-space: pre-line;">'.$media->data->description.'</p>
									</div>';
		}
		
		echo '
								</div>
							</div>';
	}
	
	private static function PrintHeaderMiddleInfo($heading, $text)
	{
		echo '
										<div class="list-group-item">
											<h4 class="list-group-item-heading">'.$heading.'</h4>
											<p class="list-group-item-text text-right">'.$text.'</p>
										</div>';
		
		return true;
	}
	
	public static function ListFormatForStream($media, $body, $stream)
	{
		if(!array_key_exists($stream, $body) || count($body[$stream]) == 0)
			return false;

		echo '
							<fieldset>
								<legend>'.StreamEnum::$names[$stream].'</legend>
								<table class="table table-striped table-hover ">
									<thead>
										<tr>
											<th>Download?</th>';
		
		foreach(Media::$format_table_header[$stream] as $head)
		{
			echo PHP_EOL.'											<th>'.$head.'</th>';
		}
			
		echo '
										</tr>
									</thead>
									<tbody>';
		
		foreach($body[$stream] as $format)
		{
			echo '
										<tr>
											<td><input type="checkbox" name="formats['.md5($media->data->webpage_url).'][]" value="'.$format[0]->format_id.'" /></td>';
					
			for($i=1, $n=count($format); $i < $n; ++$i)
			{
				echo PHP_EOL.'											<td>'.$format[$i].'</td>';
			}
	
			echo PHP_EOL.'										</tr>';
		}
	
		echo '
									</tbody>
								</table>
							</fieldset>';
		
		return true;
	}
}