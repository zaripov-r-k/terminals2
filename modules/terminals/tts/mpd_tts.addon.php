<?php

/*
Addon MPD for tts
*/

class mpd_tts extends tts_addon {
    
    // Private properties
    private $mpd;
    
    // Constructor
    function __construct($terminal) {
        $this->title       = 'Music Player Daemon (MPD)';
        $this->description = '<b>Описание:</b>&nbsp; Воспроизведение звука через кроссплатформенный музыкальный проигрыватель, который имеет клиент-серверную архитектуру.<br>';
        $this->terminal    = $terminal;
        $this->setting     = json_decode($this->terminal['TTS_SETING'], true);
        $this->port        = empty($this->setting['TTS_PORT']) ? 6600 : $this->setting['TTS_PORT'];
        $this->password    = $this->setting['TTS_PASSWORD'];
        // MPD
        include_once (DIR_MODULES . 'app_player/libs/mpd/mpd.class.php');
        
    }
    
    // Say
    function say_media_message($message, $terminal) {
        $outlink = $message['CACHED_FILENAME'];
        // берем ссылку http
        if (preg_match('/\/cms\/cached.+/', $outlink, $m)) {
            $server_ip = getLocalIp();
            if (!$server_ip) {
                DebMes("Server IP not found", 'terminals');
                return false;
            } else {
                $message_link = 'http://' . $server_ip . $m[0];
            }
        }
        $this->mpd = new mpd_player($this->terminal['HOST'], $this->port, $this->password);
        if (!is_null($this->mpd->connected)) {
            $this->mpd->PLClear();
            $this->mpd->PLAdd($message_link);
            $this->mpd->Play();
            sleep($message['MESSAGE_DURATION']);
            $this->success = TRUE;
        } else {
            $this->success = FALSE;
        }
        $this->mpd->Disconnect();
        return $this->success;
    }
    
    // Play
    function play($input, $time = 0) {
        if (strlen($input)) {
            $this->mpd = new mpd_player($this->terminal['HOST'], $this->port, $this->password);
            if (!is_null($this->mpd->connected)) {
                $this->mpd->PLClear();
                $this->mpd->PLAdd($input);
                $this->mpd->SeekTo($time);
                $this->mpd->Play();
                $this->success = TRUE;
            } else {
                $this->success = FALSE;
            }
            $this->mpd->Disconnect();
        } else {
            $this->success = FALSE;
        }
        return $this->success;
    }
    // Stop
    function stop() {
        $this->mpd = new mpd_player($this->terminal['HOST'], $this->port, $this->password);
        if (!is_null($this->mpd->connected)) {
            $this->mpd->Stop();
            $this->success = TRUE;
        } else {
            $this->success = FALSE;
        }
        $this->mpd->Disconnect();
        return $this->success;
    }
    
    // Set volume
    function set_volume($level) {
        if (strlen($level)) {
            $this->mpd = new mpd_player($this->terminal['HOST'], $this->port, $this->password);
            if (!is_null($this->mpd->connected)) {
                $this->mpd->SetVolume((int) $level);
                $this->success = TRUE;
            } else {
                $this->success = FALSE;
            }
            $this->mpd->Disconnect();
        } else {
            $this->success = FALSE;
        }
        return $this->success;
    }
    
    // ping terminal
    function ping() {
        $this->mpd = new mpd_player($this->terminal['HOST'], $this->port, $this->password);
        if (!is_null($this->mpd->connected)) {
            $this->success = TRUE;
        } else {
            $this->success = FALSE;
        }
        $this->mpd->Disconnect();
        return $this->success;
    }
    
}

?>
