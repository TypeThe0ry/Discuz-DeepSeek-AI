<?php
class DiscuzDeepseekaiPostComm {

	
	public function factoryAotu($text,$rolename,$cache,$promptctx=array()){
		$newcontent='';
		$isnewcontent=false;
		$reobj='';
		$role='';
		require dirname(__FILE__) . '/DiscuzDeepseekaiPost.class.php';
		$discuzdeepseekaipost=new DiscuzDeepseekaiPost();
		$reobj=$discuzdeepseekaipost->getTextDavinci($text,$rolename,$cache,$promptctx);

		$obj = json_decode($reobj);
		if($obj && isset($obj->choices[0]->message->content)){
			$isnewcontent=true;
			$newcontent=$obj->choices[0]->message->content;
		}

		return array($isnewcontent,$newcontent,$reobj);
	}

}

?>