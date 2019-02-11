<?php

class Am_Plugin_RandomAff extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_REVISION = '5.5.4';

    public function onSignupUserAdded(Am_Event $event)
    {
        $user = $event->getUser();
        if (!$user->aff_id) {
            $random_aff = $this->getDi()->db->select("SELECT user_id, login FROM ?_user WHERE user_id != ? ORDER BY RAND() LIMIT 1", $user->user_id);
            if (count($random_aff) > 0) {
                $update = $this->getDi()->userTable->findFirstByLogin($user->login);
                $update->aff_id = $random_aff[0]['user_id'];
                $update->save();

                $this->logDebug(sprintf("USER %s ASSIGNED TO AFF %s", $update->login, $random_aff[0]['login']));
            }

        }
    }
}