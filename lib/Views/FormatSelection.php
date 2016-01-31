<?php

namespace MediaDownloader\Views;

use MediaDownloader\StreamEnum;

abstract class FormatSelection
{
	private static $format_table_header = array(
			StreamEnum::Both => array(
					'Format',
					'Extension',
					'Resolution',
					'Filesize',
					'Video codec',
					'Audio codec',
					'FPS'
			),
			StreamEnum::Video_only => array(
					'Format',
					'Extension',
					'Resolution',
					'Filesize',
					'Video codec',
					'FPS',
					'TBR'
			),
			StreamEnum::Audio_only => array(
					'Format',
					'Extension',
					'Filesize',
					'Audio codec',
					'TBR',
					'ABR'
			)
	);
	
	public static function PrintMediaFormatSelection($media, $manual_format_selection, $direct_download)
	{
		if($direct_download)
		{
			echo '
					<div class="modal" id="downloadModal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
									<h4 class="modal-title">Download to your computer</h4>
								</div>
								<div class="modal-body">
									<label for="filename">Suggested file name:</label>
									<input type="text" style="width:100%;" name="filename" readonly value="'.$media->data->_filename.'" />
									<h4>To download:</h4>
									<ol>
										<li>Click on suggested file name field above</li>
										<li>Copy the content (CTRL+C or right-click > copy)</li>
										<li>Right-click on the "Download" button and choose "Save target as..."</li>
										<li>Paste the suggested filename previously copied (CTRL+V or right-click > paste)</li>
										<li>Download!</li>
									</ol>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									<a id="download-link" href="" download class="btn btn-primary">Download Link</a>
								</div>
							</div>
						</div>
					</div>';
		}
		 
		echo '
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">'.$media->GetTitle().'</h3></div>
						<div class="panel-body">';
		
		self::PrintHeaderInfos($media);

		if($manual_format_selection)
		{
			$formats_info_by_stream = $media->GetFormatsInfoByStream();

			self::ListFormatForStream($media, $formats_info_by_stream, StreamEnum::Both, $direct_download);
			self::ListFormatForStream($media, $formats_info_by_stream, StreamEnum::Video_only, $direct_download);
			self::ListFormatForStream($media, $formats_info_by_stream, StreamEnum::Audio_only, $direct_download);
		}
		else
		{
			self::ListFormatForStream($media, array($media->selected_format->GetStreamType() => array($media->selected_format)), $media->selected_format->GetStreamType(), $direct_download);
		}
		
		echo '
						</div>
					</div>';
	}
	
	private static function PrintHeaderInfos($media)
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
			$cnt += self::PrintHeaderMiddleInfo('Creator', $media->data->creator);
		
		if(!empty($media->data->uploader))
			$cnt += self::PrintHeaderMiddleInfo('Uploader', $media->data->uploader);
		
		if(!empty($media->data->duration))
			$cnt += self::PrintHeaderMiddleInfo('Duration', date('i:s', $media->data->duration));
		
		if(!empty($media->data->upload_date) && $cnt < 3)
			$cnt += self::PrintHeaderMiddleInfo('Upload date', $media->data->upload_date);

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
	
	private static function ListFormatForStream($media, $formats_info_by_stream, $stream, $direct_download)
	{
		if(!array_key_exists($stream, $formats_info_by_stream) || count($formats_info_by_stream[$stream]) == 0)
			return false;

		echo '
							<fieldset>
								<legend>'.StreamEnum::$names[$stream].'</legend>
								<table class="table table-striped table-hover ">
									<thead>
										<tr>
											<th>Download?</th>';
		
		foreach(self::$format_table_header[$stream] as $head)
		{
			echo PHP_EOL.'											<th>'.$head.'</th>';
		}
			
		echo '
										</tr>
									</thead>
									<tbody>';

		foreach($formats_info_by_stream[$stream] as $format_info)
		{
			self::PrintFormatInfo($media, $format_info, $direct_download);
		}
	
		echo '
									</tbody>
								</table>
							</fieldset>';
		
		return true;
	}
	
	private static function PrintFormatInfo($media, $format_info, $direct_download)
	{
		echo '
										<tr>
											<td>';

		if($direct_download)
		{
			echo PHP_EOL.'											<a data-toggle="modal" data-formaturl="'.$format_info->data->url.'" data-filename="'.$format_info->GetFilename().'" data-target="#downloadModal" class="btn btn-primary">Download</a>';
		}
		else
		{
			echo PHP_EOL.'											<input type="checkbox" name="formats['.md5($media->data->webpage_url).'][]" value="'.$format_info->data->format_id.'" />';
		}
			
		echo PHP_EOL.'											</td>';
		
		$format_info_array = self::GetFormatInfoArray($format_info);
		
		foreach($format_info_array as $cell)
		{
			echo PHP_EOL.'											<td>'.$cell.'</td>';
		}
		
		echo PHP_EOL.'										</tr>';
	}
	
	private static function GetFormatInfoArray($format_info)
	{
		switch($format_info->GetStreamType())
		{
			case StreamEnum::Audio_only:
				return array(
					(empty($format_info->data->format) ? $format_info->data->format_note : $format_info->data->format),
					$format_info->data->ext,
					$format_info->GetFileSize(),
					(!empty($format_info->data->acodec) ? $format_info->data->acodec : '?'),
					(empty($format_info->data->tbr) ? '?' : $format_info->data->tbr),
					(empty($format_info->data->abr) ? '?' : $format_info->data->abr)
				);
					
			case StreamEnum::Video_only:
				return array(
					(empty($format_info->data->format) ? $format_info->data->format_note : $format_info->data->format),
					$format_info->data->ext,
					(!empty($format_info->data->width) ? $format_info->data->width.'x'.$format_info->data->height : '?'),
					$format_info->GetFileSize(),
					(!empty($format_info->data->vcodec) ? $format_info->data->vcodec : '?'),
					(!empty($format_info->data->fps) ? $format_info->data->fps : '?'),
					(empty($format_info->data->tbr) ? '?' : $format_info->data->tbr)
				);
					
			case StreamEnum::Both:
				return array(
					(empty($format_info->data->format) ? $format_info->data->format_note : $format_info->data->format),
					$format_info->data->ext,
					(!empty($format_info->data->width) ? $format_info->data->width.'x'.$format_info->data->height : '?'),
					$format_info->GetFileSize(),
					(!empty($format_info->data->vcodec) ? $format_info->data->vcodec : '?'),
					(!empty($format_info->data->acodec) ? $format_info->data->acodec : '?'),
					(!empty($format_info->data->fps) ? $format_info->data->fps : '?')
				);
		}
	}
}