<?php

class telegramm extends app_player_addon
{
    
    // Private properties
    private $curl;
    private $address;
    
    // Constructor
    function __construct($terminal)
    {
        $this->title       = 'Терминал для модуля Телеграмм';
        $this->description = 'Описание: Терминал использующий прием-передачу сообщений через модуль Телеграмм';
        $this->description.= 'Терминал имеет обратную связь с сайтом Телеграмм.';
        $this->terminal    = $terminal;
        $this->reset_properties();
        unsubscribeFromEvent('telegram', 'SAY');
        unsubscribeFromEvent('telegram', 'SAYTO');
        unsubscribeFromEvent('telegram', 'ASK');
        unsubscribeFromEvent('telegram', 'SAYREPLY');
    }
    
    // Say
    function sayttotext($message, $event, $lang, $langfull)
    {
        $this->reset_properties();
        if (file_exists(DIR_MODULES . 'telegram/telegram.class.php')) {
            $users   = SQLSelect("SELECT * FROM tlg_user ");
            $c_users = count($users);
            
            if ($message AND $c_users) {
                for ($j = 0; $j < $c_users; $j++) {
                    $user_id = $users[$j]['USER_ID'];
                    if ($user_id === '0') {
                        $user_id = $users[$j]['NAME'];
                    }
                    $url = BASE_URL . "/ajax/telegram.html?sendMessage=1&user=" . $user_id . "&text=" . urlencode($message);
                    $out = getURL($url, 0);
                    if ($out = 'Ok') {
                        $this->success = TRUE;
                        $this->message = 'OK';
                    } else {
                        $this->success = FALSE;
                        $this->message = 'Command execution error!';
                    }
                }
            } else {
                $this->success = FALSE;
                $this->message = 'Command execution error!';
            }
        } else {
            $this->success = FALSE;
            $this->message = 'Input is missing!';
        }
        return $this->success;
    }
}

?>
