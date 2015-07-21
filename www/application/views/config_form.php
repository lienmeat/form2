<?php
/**
 * config_form.php
 * Used to edit the config of a form
 */
$question_config = (object) array(
    'text' => 'Form title:',
    'alt' => '(What the form says at the top when you fill it out)',
    'name' => 'title',
    'type' => 'text',
    'value' => $form->title,
    'validation' => 'required',
);

$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

$question_config = (object) array(
    'text' => 'Form name: ',
    'alt' => 'Determines the URL of the form (letters, numbers, and hyphens only!)',
    'name' => 'name',
    'type' => 'text',
);

if ($mode != 'edit') {
    $question_config->validation = 'required|formnameformat|newformname';
} else {
    $question_config->value = $form->name;
    $question_config->attributes->disabled = 'disabled';
}

$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

if ($this->authorization->is('superadmin') or $this->authorization->is('formcreator')) {
    $question_config = (object) array(
        'text' => 'Owner: ',
        'alt' => 'Determines what user has rights admin rights on this form. (use lowercase!)',
        'name' => 'creator',
        'type' => 'text',
        'validation' => 'required',
    );
    if ($form->creator) {
        $question_config->value = $form->creator;
    }

    $this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));
}

// $question_config =(object) array(
//     'text'=>'URL of special receiving script:',
//     'alt'=>'(Use this ONLY if processing needs to happen outside of the form application)',
//     'type'=>'text',
//     'name'=>'config[processing_url]',
//     'value'=>$form->config->processing_url
// );

//todo: make this feature work
//$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

// $question_config =(object) array(
//     'text'=>'URL of script to forward the results to after the form is successfully submitted and saved.',
//     'alt'=>'(optional, and not compatible with the receiving script)',
//     'type'=>'text',
//     'name'=>'config[forward_results_url]',
//     'value'=>$form->config->processing_url,
// );

//todo: make this feature work
//$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config = (object) array(
    'text' => 'Thank You Text',
    'alt' => '(Message given after form has been successfully completed)',
    'type' => 'textarea',
    'name' => 'config[thankyou]',
    'value' => $form->config->thankyou,
);

$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

/*
$inputs = array(
(object) array('type'=>'radio', 'value'=>'1', 'label'=>'Yes', 'selected'=>array($form->config->login_required)),
(object) array('type'=>'radio', 'value'=>'0', 'label'=>'No', 'selected'=>array($form->config->login_required)),
);
 */
if (!$form->config->login_required) {
    $form->config->login_required = 'N';
}
$question_config = (object) array(
    'text' => 'Is a login required?',
    'alt' => '(If logged in, their username is automatically associated with the form result.)',
    'name' => 'config[login_required]',
    'type' => 'radio',
    'options' => array('Yes' => 'Y', 'No' => 'N'),
    'selected' => array($form->config->login_required),
);
$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

if (!$form->config->ad_groups && $mode != 'edit') {
    $form->config->ad_groups = array("all");
}

$question_config = (object) array(
    'text' => 'If a login is required, what WWU Active Directory statuses are permitted to view this form?',
    'name' => 'config[ad_groups][]',
    'type' => 'checkbox',
    'options' => array('All' => 'all', 'Student' => 'student', 'Staff' => 'staff', 'Faculty' => 'faculty', 'Administration' => 'administration'),
    'selected' => $form->config->ad_groups,
    'dependencies' => 'config[login_required]=Y',
);
$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

$question_config = (object) array(
    'text' => 'If limiting form viewing by AD statuses isn\'t sufficient, you can permit certain users by username:',
    'alt' => '(One per line. These users will be able to view in addtion to anyone with a status selected above!)',
    'name' => 'config[viewers]',
    'type' => 'textarea',
    'dependencies' => 'config[login_required]=Y&&config[ad_groups][]!=all*',
    'value' => $form->config->viewers,
);
$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

$question_config = (object) array(
    'text' => 'Notify these people when a form is submitted:',
    'alt' => '(Will notify any listed email addresses when this form is submitted.  One email address per line!)',
    'type' => 'textarea',
    'name' => 'config[notify]',
    'value' => $form->config->notify,
);

$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

$question_config = (object) array(
    'text' => 'Forward form response data to a url when completed:',
    'alt' => '(Optional advanced functionality! Data sent as POST, via Php\'s cURL.  Enables using formit2 forms with other applications.)',
    'type' => 'text',
    'name' => 'config[forward_to]',
    'value' => $form->config->forward_to,
    'attributes' => (object) array('size' => '100'),
);

$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));

$question_config = (object) array(
    'text' => 'Redirect to a url when completed, instead of showing usual "thank you" message:',
    'alt' => '(Optional advanced functionality! Form result data will be stored in $_SESSION[f2][lastresult].)',
    'type' => 'text',
    'name' => 'config[redirect_to]',
    'value' => $form->config->redirect_to,
    'attributes' => (object) array('size' => '100'),
);

$this->load->view('question/view_question', array('question' => (object) array('id' => uniqid(''), 'config' => $question_config)));
