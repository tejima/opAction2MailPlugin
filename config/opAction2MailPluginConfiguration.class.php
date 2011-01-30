<?php

class opAction2MailPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect(
      'op_action.post_execute_message_sendToFriend',
      array($this,'sendToFriend')
    );
  }
  public function sendToFriend($event){
    $action = $event['actionInstance'];
    if(sfRequest::POST != $action->getRequest()->getMethod()){
      return;
    }
    $id = $action->getUser()->getMemberId();
    $data = $action->getRequestParameter('message');
    $member_from = Doctrine::getTable('Member')->find($id);
    $member_to = Doctrine::getTable('Member')->find($data['send_member_id']);

    $url = sfConfig::get('op_base_url');    
    $message = <<<EOF
{$member_from['name']}さんからメッセージが届いています。
件名:{$data['subject']}

{$data['body']}

メッセージに返信するには、こちらをクリックしてください。
{$url}/message
EOF;
    self::notifyMail($member_to,$message);
  }

  public static function notifyMail($member,$message)
  {
    $memberPcAddress = $member->getConfig('pc_address');
    $memberMobileAddress = $member->getConfig('mobile_address');
    $from = opConfig::get('ZUNIV_US_NOTIFYFROM',opConfig::get('admin_mail_address'));

    list($subject,$body) = explode("\n",$message,2);
    if ($memberPcAddress)
    {
      opMailSend::execute($subject, $memberPcAddress, $from, $body);
    }
    if ($memberMobileAddress)
    {
      opMailSend::execute($subject, $memberMobileAddress, $from, $body);
    }
/*
    $msg = <<<EOF
FROM:{$from}
TO_PC:{$memberPcAddress}
TO_MOBILE:{$memberMobileAddress}
MSG:
{$message}
EOF;
    error_log("$msg",3,'/tmp/log');
*/
  }
}
