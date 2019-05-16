<?php

/*
 * Vertiso (https://vertiso.pl)
 *
 * @copyright: Copyright (c) 2018 Vertiso (https://vertiso.pl)
 * @author Marcin Zagórski <vertiso851@gmail.com>
 */

namespace Vertiso\WebsiteInstallator\CMS;

class WordpressQueryBuilder
{
    public function updateWebsiteTitle($title)
    {
        return 'UPDATE wp_options SET option_value = "' . $title . '" WHERE option_name = "blogname"';
    }

    public function updateHomeUrl($url)
    {
        return 'UPDATE wp_options SET option_value = "' . $url . '" WHERE option_name = "siteurl" OR option_name = "home"';
    }

    public function updateUser($displayName, $userNiceName, $login, $password, $email, $whereId)
    {
        return 'UPDATE wp_users SET display_name="' . $displayName . '",user_nicename="' . $userNiceName . '",user_login="' . $login . '",user_pass="' . $password . '",user_email="' . $email . '" WHERE ID="' . $whereId . '"';
    }
}
