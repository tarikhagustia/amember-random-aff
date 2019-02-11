<?php

class Am_Plugin_RandomAff extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_REVISION = '5.5.4';
    protected $_configPrefix = 'misc.';

    public function getTitle()
    {
        return 'Random AFF';
    }

    public function _initSetupForm(Am_Form_Setup $form)
    {
        $list = $this->getDi()->db->select("SELECT login FROM ?_user ORDER BY login", DBSIMPLE_ARRAY_KEY);
        $newList = [];
        foreach ($list as $user) {
            $newList[$user['login']] = $user['login'];
        }
        $form->addSortableMagicSelect('random_aff.list', array('class' => 'am-combobox'))
            ->setLabel(___("Select random users\nnew user will registered under this random users"))
            ->loadOptions($newList);
    }

    public function onSignupUserAdded(Am_Event $event)
    {
        $user = $event->getUser();
        if (!$user->aff_id) {
            $in  = join("','",$this->getConfig('random_aff.list'));
            $random_aff = $this->getDi()->db->select("SELECT user_id, login FROM ?_user WHERE user_id != ? AND login IN ('{$in}') ORDER BY RAND() LIMIT 1", $user->user_id);
            if (count($random_aff) > 0) {
                $update = $this->getDi()->userTable->findFirstByLogin($user->login);
                $update->aff_id = $random_aff[0]['user_id'];
                $update->save();

                $this->logDebug(sprintf("USER %s ASSIGNED TO AFF %s", $update->login, $random_aff[0]['login']));
            }

        }
    }
}