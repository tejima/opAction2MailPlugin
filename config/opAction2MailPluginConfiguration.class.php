<?php

class opAction2MailPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {

    $this->dispatcher->connect(
      'op_action.post_execute_member_updateActivity',
      array($this,'executeUpdateActivityEvent')
    );
  }
  public  function executeUpdateActivityEvent($event){
    $action = $event['actionInstance'];
    $id = $action->getUser()->getMemberId();
    $data = $action->getRequestParameter('activity_data');
    $queue_list = ToPNE::processQueing($id,$data['body']);
    error_log("executeUpdateActivityEvent<" . $event['actionName'].">".count($queue_list)."\n",3,'/tmp/log');
  }

}

