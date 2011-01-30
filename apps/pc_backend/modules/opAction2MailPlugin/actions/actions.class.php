<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opAction2MailPlugin actions.
 *
 * @package    OpenPNE
 * @subpackage opAction2MailPlugin
 * @author     Mamoru Tejima tejima@tejimaya.com
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class opAction2MailPluginActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */

  public function executeIndex(sfWebRequest $request){
    /*
    if('' == opConfig::get('ZUNIV_US_NOTIFYFROM','')){
      //set default value to admin email
      Doctrine::getTable('SnsConfig')->set('ZUNIV_US_NOTIFYFROM', opConfig::get('admin_mail_address'));
    }
    */
    $this->form = new opAction2MailPluginConfigForm();
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request->getParameter('action2mail'));
      if ($this->form->isValid())
      {
        $this->form->save();
        $this->redirect('opAction2MailPlugin/index');
      }
    }
  }
}
