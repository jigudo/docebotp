<?php
// $Id: $


function docebo_mycourses_display() { 
	$res ='';
	
	$data =docebo_get_mycourses_data();	
	$xml =new SimpleXMLElement($data);	
	$nodes =$xml->xpath('//course_info');
	$auth =doceboGetAuth(doceboGetDrupalUsername());
	
	
	foreach($nodes as $item) {
		$res.='<div class="node node-page node-teaser clearfix">';
		// Title:
		$res.='<h2>
				<a href="'.$item->course_link.'&amp;auth='.$auth.'">'.$item->course_name.'</a>
			</h2>';
			
		// Description:
		$res.='<div class="content clearfix">
			<div class="field field-name-body field-type-text-with-summary field-label-hidden"><div class="field-items"><div property="content:encoded" class="field-item even">
				<p>'.$item->course_description.'</p>
			</div></div></div></div>';
		
		// Enter course link:
		$res.='<div class="link-wrapper">
			<ul class="links inline">
				<li class="node-readmore first last">
					<a title="'.$item->course_name.'" href="'.$item->course_link.'&amp;auth='.$auth.'">'.t('Enter course').'</a>
				</li>
			</ul></div>';
		$res.='</div>';
	}

	return $res; //var_export($nodes, true).var_export($data, true).$GLOBALS['user']->uid;
}
