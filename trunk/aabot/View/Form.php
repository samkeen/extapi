<?php
class View_Form {
	
	public function renderSelectList($select_name, $items, $selected=null, $top_option_label=null, $attibutes_string=null) {
		$select_name_parts = explode('.',strtolower($select_name));
		$name = "{$select_name_parts[0]}[{$select_name_parts[1]}]";
		$id = "{$select_name_parts[0]}-{$select_name_parts[1]}";
		
		$output = "\n<select name=\"".$name.'" id="'.$id.'" ';
		$output .= ($attibutes_string) ? $attibutes_string." >\n" : " >\n";
		$output .= $top_option_label ? "\t<option value=\"\">$top_option_label</option>\n" : '';
		$selected = ($selected===null && isset($_REQUEST[$name])) ? $_REQUEST[$name] : $selected;
	
		foreach ($items as $value => $label) {
			$output.= "\t<option value=\"$value\" ".($selected==$value?' selected="true" ':'').'>'.h($label,false)."</opion>\n";
		}
		$output.="</select>\n";
		echo $output;
	}
}
?>