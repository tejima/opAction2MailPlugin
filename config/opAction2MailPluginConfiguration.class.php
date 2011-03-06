<?php

class opAction2MailPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect(
      'op_action.post_execute_message_sendToFriend',
      array($this,'sendToFriend')
    );
    $this->dispatcher->connect(
      'op_action.post_execute_communityEvent_create',
      array($this,'event2mail')
    );

  }

  public function event2mail($event){
    if(version_compare(OPENPNE_VERSION, '3.5.0', '<=')){
      require_once(dirname(__FILE__).'/dummyload.php');
    }
    $action = $event['actionInstance'];
    $ce_id = $action->form->getObject()->id;
    $object = $action->getRoute()->getObject();
    if ($object instanceof Community){
    }else{
      die("something wrong.");
    }
    $community_id = $object->id;
    $cm_list = Doctrine_query::create()->from("CommunityMember cm")->where("community_id = ?",$community_id)->execute();

    $data = $action->getRequestParameter('community_event');
    $url = sfConfig::get('op_base_url');    
    $message = <<<EOF
{$data['open_date']['month']}/{$data['open_date']['day']} {$data['name']}イベントのお知らせ

{$data['open_date']['month']}/{$data['open_date']['day']} {$data['name']}イベント開催のお知らせメールです。

{$data['body']}

イベントを開催するコミュニティの情報はこちら。
{$url}community/{$community_id}
EOF;
    foreach($cm_list as $cm){
      $member = Doctrine::getTable("Member")->find($cm->member_id);
      $this->notifyMail($member,$message);
    }
    
    $msg = "community_id:{$object->id}\n";
  }
  public function sendToFriend($event){
    if(version_compare(OPENPNE_VERSION, '3.5.0', '<=')){
      require_once(dirname(__FILE__).'/dummyload.php');
    }

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
{$url}message
EOF;
    if(!$action->getRequestParameter('is_draft')){
      self::notifyMail($member_to,$message);
    }
  }
  public static function notifyMail($member,$message)
  {
    $memberPcAddress = $member->getConfig('pc_address');
    $memberMobileAddress = $member->getConfig('mobile_address');
    $from = opConfig::get('ZUNIV_US_NOTIFYFROM',opConfig::get('admin_mail_address'));

    list($subject,$body) = explode("\n",$message,2);
    if (2 != $member->getConfig('ZUNIV_US_NOTIFYPC',1) && $memberPcAddress)
    {
      opMailSend::execute($subject, $memberPcAddress, $from, $body);
    }
    if (2 != $member->getConfig('ZUNIV_US_NOTIFYMOBILE',1) && $memberMobileAddress)
    {
      opMailSend::execute($subject, $memberMobileAddress, $from, $body);
    }
  }
}
