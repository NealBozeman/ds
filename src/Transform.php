<?php
namespace njb\ds;

Class Transform {


	const TASK_ESCAPE_CHARS = ['\\\\','|','$','.','='];
	const TASK_META_DELIMITERS = ['|','$','.'];


	public static function flattenArrayValues($array) {
		$arr = [];
		foreach (array_keys($array) as $item) {
			$arr[$item] = $array[$item][0];
		}	
		return $arr;
	}


	public static function serialize($obj, $path='', $pos='$', &$results=[]) {
		//scenarios
		// $obj has keys, so check keys
		//	- key is an int so create an array positional and send back to serializeMeta. key|
		//	- key is a key so add key to path as key.
		// $obj is just a value, so print the value.

		if (is_array($obj)) {
			foreach ($obj as $key=>$val) {
				if(is_numeric($key)) {
					if(is_array($val)) {
						self::serialize($val, $path . '|', $pos . ',' . $key, $results);
					} else {
						$results[]=$path.'|'.self::escapeVal($val). $pos . ',' . $key;
					}
				} else {
					if(is_array($val)) {
						self::serialize($val, $path .$key . '.', $pos, $results);
					} else {
						$results[]=$path.$key.'='.self::escapeVal($val) . $pos;
					}
				}
				
			}
		} else {
			$results[]=$path . self::escapeVal($obj) .$pos;
		}

		if($path=='') { // clean up shit
			$results = preg_replace('/\.\|/','|',$results);	
			$results = preg_replace('/\$,([0-9,]+)$/','\$$1',$results);	
		}
		
		return $results;
	}

	public static function escapeVal($val) {
		foreach(TASK_ESCAPE_CHARS as $char) {
			$val=preg_replace('/([^\\\])(['.$char.'])/', '$1\\\\$2', $val);
		}
		return ($val);	  
	}

	public static function unescapeVal($val) {
		foreach (TASK_ESCAPE_CHARS as $char) {
			$val=preg_replace('/([\\\])(['.$char.'])/', '$2', $val);
		}
		return $val;
	}

	public static function unserialize($arr) {
		$obj=[];
		$lpos=0;
		$cpos=0;
		$arrlocs=[];
		$parts=[];
		$obj = Array();
		foreach($arr as $path) {
			$to_eval="\$tmp_obj";
			$arrpos=-1;
			$arrlocs=substr($path,strrpos($path,'$')+1);
			$arrlocs=explode(',',$arrlocs);
			print_r ($arrlocs);
			preg_match_all('/.*?(?<!\\\\)[\.\|\$]/', $path, $matches);
			print_r($matches);
			foreach($matches[0] as $shit_to_interp) {
				$node = substr($shit_to_interp, 0, strlen($shit_to_interp)-1);
				switch($end_char=substr($shit_to_interp, -1)) {
					case '$':
						
						if(preg_match_all("/(.*?)(?<!\\\\)[\.](.*?)(?<!\\\\)[\=](.*?)(?<!\\\\)[\$]/", $shit_to_interp, $smatch)) {
							//found this.value=something
							$tmp = addslashes(self::unescapeVal($smatch[3][0]));
							$to_eval.="['{$smatch[1][0]}']['{$smatch[2][0]}']='{$tmp}'";
							print_r($smatch);

						}elseif(preg_match_all("/(.*?)(?<!\\\\)[\=](.*?)(?<!\\\\)[\$]/", $shit_to_interp, $smatch)) {
							$tmp = addslashes(self::unescapeVal($smatch[2][0]));
							$to_eval.="['{$smatch[1][0]}']='{$tmp}'";
							echo 'yay!!'; print_r($smatch); 


						} else {
							$to_eval.="='" . addslashes(self::unescapeVal($node)) . "'";
						}
					break;

					case '|':
						$arrpos++; echo 'yo!';
						$to_eval.="['$node'][$arrlocs[$arrpos]]";
					break;

					case '.':
						$to_eval.="['$node']";
						
					default:
						echo 'no delimiting char found :(';
				}

			}

			echo 'toeval='.$to_eval;
			eval($to_eval . ';');
			$obj = array_merge($obj, $tmp_obj);
			print_r($obj);
			


			
		}

	}



}