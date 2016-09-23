
function eclassf_checkAll(checkWhat) {
  // Find all the checkboxes...
  var inputs = document.getElementsByTagName('input');

  // Loop through all form elements (input tags)
  for(index = 0; index < inputs.length; index++)
  {
    // ...if it's the type of checkbox we're looking for, toggle its checked status
    if(inputs[index].id == checkWhat)
      if(inputs[index].checked == 0)
      {
        inputs[index].checked = 1;
      }
      else if(inputs[index].checked == 1)
      {
        inputs[index].checked = 0;
      }
  }
}
/**
 *
 * @access public
 * @return void
 **/
function eclassf_showupload(){
	if($('eclassf_uparea').style.display=='none'){
	Effect.BlindDown('eclassf_uparea', { duration: 1.2 });
	$('eclassf_upit').value='Hide upload area';
	}else{
		$('eclassf_upit').value='Upload a Picture';
		Effect.BlindUp('eclassf_uparea', { duration: 1.2 });
	}
}
/**
 *
 * @access public
 * @return void
 **/
function eclassf_doupload(){
	var upFilesOK = false;
	var filefields='';
	filefields=document.getElementsByName('file_userfile[]');
	for (var i = 0; i < filefields.length; i++) {
   		if (filefields[i].value != ""){
			upFilesOK = true;
		}
	}
	if(!upFilesOK){
		var eclassf_adcat_msg='<ul>';
		eclassf_adcat_msg ='<li>No files specified for upload</li>';
		eclassf_adcat_msg = eclassf_adcat_msg + '</ul>';
		fb_message_box('validation',eclassf_adcat_msg);
	}else
	{
		$('eclassf_upsubmit').value='process';
		$('dataform').submit();
	}
}
/**
 *
 * @access public
 * @return void
 **/
function eclassf_checkcat(){
	var eclassf_adcat_ok=true;
	var eclassf_adcat_msg='<ul>';
	$('eclassf_catname').removeClassName('redit');
	if($F('eclassf_catname')==''){
		$('eclassf_catname').addClassName('redit');
		eclassf_adcat_ok=false;
		eclassf_adcat_msg ='<li>The category name is missing</li>';

	}
	eclassf_adcat_msg = eclassf_adcat_msg + '</ul>';
	if(!eclassf_adcat_ok){
		fb_message_box('validation',eclassf_adcat_msg);
	}
	return eclassf_adcat_ok;
}
function eclassf_checksubcat(){
	var eclassf_adcat_ok=true;
	var eclassf_adcat_msg='<ul>';
	$('eclassf_subname').removeClassName('redit');

	if($F('eclassf_subname')==''){
		$('eclassf_subname').addClassName('redit');
		eclassf_adcat_ok=false;
		eclassf_adcat_msg ='<li>The category name is missing</li>';
	}
	eclassf_adcat_msg = eclassf_adcat_msg + '</ul>';
	if(!eclassf_adcat_ok){
		fb_message_box('validation',eclassf_adcat_msg);
	}
	return eclassf_adcat_ok;
}
/**
 *
 * @access public
 * @return void
 **/
 var doneit=false;
function meclassf_checkok(thisform)
{

	var eclassf_msg_type='blank';
	var eclassf_msg_msg='<ul>';
	var eclassf_error=false;

	$('eclassf_name').removeClassName('redit');

	if (doneit)
	{

		eclassf_msg_msg+='<li>Form has already been submitted, please wait.</li>';
	}

	if ($F('eclassf_name')==''){
		eclassf_error=true;
		eclassf_msg_type='validation';
		$('eclassf_name').addClassName('redit');
		eclassf_msg_msg+='<li>A title must be entered</li>';
	}
	eclassf_msg_msg+='</ul>';
	if(eclassf_error){
		fb_message_box(eclassf_msg_type,eclassf_msg_msg);
		doneit=false;
		return false;
	}
	else
	{
		doneit=true;
		return true;
	}
}



