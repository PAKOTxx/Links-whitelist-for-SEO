<?php
    global $allpages, $memcached;
    if (!empty($hook_data)) {
        // Fix URL
        $hook_data['url'] = $allpages->all($hook_data['category'])->url() .
            ($hook_data['alias'] ? $hook_data['alias'] . '-' . (is_numeric($hook_data['extcode']) ? $hook_data['extcode'] : $hook_data['absnum'] ) : $hook_data['absnum']);
        $hook_data['href'] = $hook_data['url'] . '.html';

        // Fix views
        if (is_object($memcached)) {
            $views = $memcached->get('a_' . $hook_data['absnum']);
            if ($memcached->getResultCode() != Memcached::RES_NOTFOUND) {
                $hook_data['views'] = (int)$views;
            }
        }

        $whitelist = array(
            'vesti-ukr.com',
            'ubr.ua',
            'bbc.com',
            'cnn.com',
            'gov.ua',
            '24tv.ua',
            'zaplatka.ua',
            'test.org.ua',
            'viva.ua',
            'ddaudio.com.ua',
            'pmg.kiev.ua',
            'betonmobile.ru',
            'bistrozaim.ua',
            'vapecorp.com.ua',
            'credit365.ua',
            'bi.ua',
            'www.moyo.ua',
            'y.ua',
            'holz.ua',
            'viyar.ua',
            'redlight.com.ua',
            'toplivo.app',
            'moto-store.com.ua',
            'www.gup.ua',
            'vao.com.ua',
            'hotline.finance'
        );

        $reg_exUrl = '/<a.*(href="(http|https|ftp|ftps)\:\/\/(\/?[^"]+)").*<\/a>/Uu';

        if(preg_match_all($reg_exUrl, $hook_data['body'], $tmp)){
            foreach($tmp[3] as $k => $domain){
                $domain = preg_replace('[/.*]','',$domain);
                if(!in_array($domain, $whitelist) && strpos($tmp[0][$k], 'rel="nofollow"') === false){
                    $to_text = str_replace($tmp[1][$k], $tmp[1][$k] . ' rel="nofollow"', $tmp[0][$k]);
                    $hook_data['body'] = str_replace($tmp[0][$k], $to_text, $hook_data['body']);
                }
            }
        }

        return $hook_data;
    }
