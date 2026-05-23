<?php
if (! defined('IN_DISCUZ')) {
    exit('Access Denied');
}
class DiscuzDeepseekaiPost
{

    const COMP = 'https://api.deepseek.com/chat/completions';
    private function fetch($url, $postdata = "", $auth = "", $headers = "")
    {


        $curl = curl_init($url);
        if ($postdata) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        } else {
            curl_setopt($curl, CURLOPT_POST, false);
        }
        if ($auth) {
            curl_setopt($curl, CURLOPT_USERPWD, $auth);
        }
        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        try{
            $response = curl_exec($curl);

            if($response===false){
                if(curl_errno($curl) >0){
                    return json_encode(array('error'=>array('message'=>'Failed to connect to api.deepseek.com port 443: Timed out ,Code:'.curl_errno($curl))));
                }
            }
        }catch(Exception $e){

            return $e->getMessage();
        }
        if (empty($response)) {
            die(curl_error($curl));
            curl_close($curl);
        } else {

            curl_close($curl);
        }
        return $response;
    }
    public function getTextDavinci($prompt,$rolename,$cache,$promptctx=array())
    {
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . trim($cache['apikey'])
        );

        if($rolename)
            $messages[] = array('role' => 'system', 'content' => $this->_convUtf($rolename,CHARSET));

        // Custom prompt support: when admin fills in $cache['custom_prompt'],
        // replace {title}/{content}/{forum}/{author} placeholders and use it as the
        // system prompt. When empty, fall back to the original behavior so legacy
        // installs are unaffected.
        // NOTE: prompt length is not capped here; if needed in the future, truncate
        // $custom_prompt below (e.g. via mb_substr) to enforce a length limit.
        $custom_prompt = isset($cache['custom_prompt']) ? trim($cache['custom_prompt']) : '';
        if ($custom_prompt !== '') {
            $ctx = is_array($promptctx) ? $promptctx : array();
            $title   = isset($ctx['title'])   ? (string)$ctx['title']   : '';
            $content = isset($ctx['content']) ? (string)$ctx['content'] : '';
            $forum   = isset($ctx['forum'])   ? (string)$ctx['forum']   : '';
            $author  = isset($ctx['author'])  ? (string)$ctx['author']  : '';
            $custom_prompt = str_replace(
                array('{title}', '{content}', '{forum}', '{author}'),
                array($title, $content, $forum, $author),
                $custom_prompt
            );
            $messages[] = array(
                'role' => 'system',
                'content' => $this->_convUtf($custom_prompt, CHARSET),
            );
        }

        $messages[] = array(
            'role' => 'user',
            'content' => $this->_convUtf($prompt, CHARSET),
        );

        if($cache['deepseekllm']==2){
            $model='deepseek-v4-pro';
            $postdata = array(
                'model' => $model,
                'messages' => $messages
            );
        }else{
            $model='deepseek-v4-flash';
            $postdata = array(
                'model' => $model,
                'messages' => $messages
            );
        }

        $resp = $this->fetch(self::COMP, json_encode($postdata), '', $headers);


        return $resp;
    }

    private function _convUtf($var, $charset)
    {
        if ($charset == 'gbk') {
            $var = diconv($var, $charset, 'utf-8');
        }
        return $var;
    }
}

?>