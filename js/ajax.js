// JavaScript Document

function show_loading_screen()
{
	document.getElementById('loading_screen').style.display = 'block';
}
function hide_loading_screen()
{
	document.getElementById('loading_screen').style.display = 'none';
}

function get_state_on_ai(value1){
	show_loading_screen();
	var url = 'get_state.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_state_on_ai
	});
}
function show_state_on_ai(originalRequest)
{
	var res=originalRequest.responseText.split('~~',2);
	document.getElementById('state').value = res[1];
	document.getElementById('stateID').value = res[0];
	get_city_on_state(res[0]);
	hide_loading_screen();
	if(document.getElementById('category').value=="D")
		get_parent_list(document.getElementById("aiName").value, "B");
	else if(document.getElementById('category').value=="S")
		get_parent_list(document.getElementById("aiName").value, "D");
}

function get_city_on_state(value1){
	var url = 'get_city.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_city_on_state
	});
}
function show_city_on_state(originalRequest)
{
   document.getElementById('divLoc').innerHTML = originalRequest.responseText;
}

function get_parent_list(value1, value2){
	show_loading_screen();
	var url = 'get_parent.php';
	var pars = 'id='+value1+'&cgr='+value2;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_parent_list
	});
}
function show_parent_list(originalRequest)
{
   document.getElementById('spanPList').innerHTML = originalRequest.responseText;
	hide_loading_screen();
}

function get_licence_details(value1){
	show_loading_screen();
	var url = 'get_licence_details.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_licence_details
	});
}
function show_licence_details(originalRequest)
{
	var res=originalRequest.responseText.split('~~',6);
	document.getElementById('licenceFor').value = res[0];
	document.getElementById('licenceValidUpTo').value = res[1];
	document.getElementById('renewalNo').value = res[2];
	document.getElementById('renewalDate').value = res[3];
	document.getElementById('preIssueNo').value = res[4];
	document.getElementById('lastIssueDate').value = res[5];
	hide_loading_screen();
}

function get_dealer_details(value1){
	show_loading_screen();
	var url = 'get_dealer_details.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_dealer_details
	});
}
function show_dealer_details(originalRequest)
{
	var res=originalRequest.responseText.split('~~',9);
	document.getElementById('typeCategory').value = res[0];
	document.getElementById('ownerName').value = res[1];
	document.getElementById('cityName').value = res[2];
	document.getElementById('stateName').value = res[3];
	document.getElementById('recommByName').value = res[4];
	document.getElementById('recommById').value = res[5];
	document.getElementById('stateId').value = res[6];
	document.getElementById('pcApplyFor').value = res[8];
	get_licence(res[6],res[7]);
	hide_loading_screen();
}

function get_licence(value1, value2){
	var url = 'get_licence.php';
	var pars = 'id='+value1+'&lic='+value2;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_licence
	});
}
function show_licence(originalRequest)
{
	document.getElementById('spanLicence').innerHTML = originalRequest.responseText;
}

function get_dealer(){
	var url = 'get_dealer.php';
	var pars = '';
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_dealer
	});
}
function show_dealer(originalRequest)
{
	document.getElementById('showControl').innerHTML = originalRequest.responseText;
}

function get_state(){
	var url = 'get_state1.php';
	var pars = '';
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_state
	});
}
function show_state(originalRequest)
{
	document.getElementById('showControl').innerHTML = originalRequest.responseText;
}

function get_city1(){
	var url = 'get_city1.php';
	var pars = '';
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_city1
	});
}
function show_city1(originalRequest)
{
	document.getElementById('showControl').innerHTML = originalRequest.responseText;
}

function get_aidata(value1){
	var url = 'get_aidata.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_aidata
	});
}
function show_aidata(originalRequest)
{
	var res=originalRequest.responseText.split('~~',6);
	document.getElementById('aiName').value = res[0];
	document.getElementById('state').value = res[1];
	document.getElementById('userName').value = res[5];
	document.getElementById('userPass').value = '';
	document.getElementById('confirmPass').value = '';
	document.getElementById('mobile').value = res[2];
	document.getElementById('eMail').value = res[3];
	document.getElementById('uid').value = res[4];
	document.getElementById("tdButton").innerHTML = '<input type="submit" name="submit" id="submit" value=" Change ">&nbsp;&nbsp;<input type="button" name="delete" id="delete" value=" Delete " onclick="ConfirmDelete(document.getElementById(\'areaIncharge\').value)" />&nbsp;&nbsp;<input type="reset" name="reset" id="reset" value=" Reset "/>&nbsp;&nbsp;<input type="button" name="exit" id="exit" value=" Exit " onclick="window.close()" />';
}

function get_citydata(value1){
	var url = 'get_citydata.php';
	var pars = 'id='+value1;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_citydata
	});
}
function show_citydata(originalRequest)
{
	var res=originalRequest.responseText.split('~~',2);
	document.getElementById('cityName').value = res[0];
	document.getElementById('state').value = res[1];
	document.getElementById("tdButton").innerHTML = '<input type="submit" name="submit" id="submit" value=" Change ">&nbsp;&nbsp;<input type="button" name="delete" id="delete" value=" Delete " onclick="ConfirmDelete(document.getElementById(\'city\').value)" />&nbsp;&nbsp;<input type="reset" name="reset" id="reset" value=" Reset "/>&nbsp;&nbsp;<input type="button" name="exit" id="exit" value=" Exit " onclick="window.close()" />';
}

function get_dds_from_dealer_list_onstate(value1){
	get_dds_dealer_list(value1, document.getElementById('category').value);
}
function get_dds_from_dealer_list_oncategory(value1){
	get_dds_dealer_list(document.getElementById('stateName').value, value1);
}
function get_dds_dealer_list(value3, value4){
	var url = 'get_dds_dealer_list.php';
	var pars = 'sid='+value3+'&cgr='+value4;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_dds_dealer_list
	});
}
function show_dds_dealer_list(originalRequest)
{
   document.getElementById('spanDDSList').innerHTML = originalRequest.responseText;
}

function get_dds_from_pcdetails_onstate(value1){
	get_dds_pcdetails_list(value1, document.getElementById('category').value);
}
function get_dds_from_pcdetails_oncategory(value1){
	get_dds_pcdetails_list(document.getElementById('stateName').value, value1);
}
function get_dds_pcdetails_list(value3, value4){
	var url = 'get_dds_pcdetails_list.php';
	var pars = 'sid='+value3+'&cgr='+value4;
	var myAjax = new Ajax.Request(
	url,
	{
		method: 'post',
		parameters: pars,
		onComplete: show_dds_pcdetails_list
	});
}
function show_dds_pcdetails_list(originalRequest)
{
   document.getElementById('spanDDSList').innerHTML = originalRequest.responseText;
}