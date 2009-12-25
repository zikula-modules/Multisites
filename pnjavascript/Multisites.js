/**
 * Create a new instance based on a specified model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	form sumbition
*/
function newInstance(){
	document.newInstance.submit();
}

/**
 * Update module configuration
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	form sumbition
*/
function config(){
	document.config.submit();
}

/**
 * Create a new model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	form sumbition
*/
function newModel(){
	document.newModel.submit();
}
/**
 * edit instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: 
 * @return:	form sumbition
*/
function editInstance(){
	document.editInstance.submit();
}

/**
 * edit Model
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param: 
 * @return:	form sumbition
*/
function editModel(){
	document.editModel.submit();
}

/**
 * Delete an instance
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	form sumbition
*/
function deleteInstance(){
	document.deleteInstance.submit();
}

/**
 * Modify a module state in a site
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	site module state information
*/
function modifyActivation(moduleName,instanceId,newState){
	var pars = "module=Multisites&func=modifyActivation&moduleName=" + moduleName + "&instanceId=" + instanceId + "&newState=" + newState;
	Element.update('module_' + moduleName, '<img src="images/ajax/circle-ball-dark-antialiased.gif">');
	var myAjax = new Ajax.Request("ajax.php", 
	{
		method: 'get', 
		parameters: pars, 
		onComplete: modifyActivation_response,
		onFailure: modifyActivation_failure
	});
}

function modifyActivation_response(req){
    // show error if necessary
	if (req.status != 200 ) { 
		pnshowajaxerror(req.responseText);
		return;
	}
	var json = pndejsonize(req.responseText);
	
	Element.update('module_' + json.moduleName, json.content);
}

function modifyActivation_failure(){
}

/**
 * Create, delete or change a module state in a site depending on the module initial state
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	site module state information
*/
function allowModule(moduleName,instanceId){
	var pars = "module=Multisites&func=allowModule&moduleName=" + moduleName + "&instanceId=" + instanceId;
	Element.update('module_' + moduleName, '<img src="images/ajax/circle-ball-dark-antialiased.gif">');
	var myAjax = new Ajax.Request("ajax.php", 
	{
		method: 'get', 
		parameters: pars, 
		onComplete: allowModule_response,
		onFailure: allowModule_failure
	});
}

function allowModule_response(req){
    // show error if necessary
	if (req.status != 200 ) { 
		pnshowajaxerror(req.responseText);
		return;
	}
	var json = pndejsonize(req.responseText);
	
	Element.update('module_' + json.moduleName, json.content);
}

function allowModule_failure(){
}

/**
 * Create or delete a theme state in a site depending on the theme initial state
 * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
 * @param:
 * @return:	site theme state information
*/
function allowTheme(themeName,instanceId){
	var pars = "module=Multisites&func=allowTheme&themeName=" + themeName + "&instanceId=" + instanceId;
	Element.update('theme_' + themeName, '<img src="images/ajax/circle-ball-dark-antialiased.gif">');
	var myAjax = new Ajax.Request("ajax.php", 
	{
		method: 'get', 
		parameters: pars, 
		onComplete: allowTheme_response,
		onFailure: allowTheme_failure
	});
}

function allowTheme_response(req){
    // show error if necessary
	if (req.status != 200 ) { 
		pnshowajaxerror(req.responseText);
		return;
	}
	var json = pndejsonize(req.responseText);
	
	Element.update('theme_' + json.themeName, json.content);
}

function allowTheme_failure(){
}

/*
function actualize(){
	var modulesString = '';
	for (var i=0;i<document.actualize.elements.length;i++) {
		var e = document.actualize.elements[i];
		if (e.type == 'checkbox' && e.checked) {
			var modulesString = modulesString + e.name + '|';
		}
	}
	if(modulesString == ''){
		alert(notModulesSelected);
		return;
	}
	var pars = "module=Multisites&func=actualize&modules=" + modulesString;
	//Element.update('theme_' + themeName, '<img src="images/ajax/circle-ball-dark-antialiased.gif">');
	var myAjax = new Ajax.Request("ajax.php", 
	{
		method: 'get', 
		parameters: pars, 
		onComplete: actualize_response,
		onFailure: actualize_failure
	});
}

function actualize_response(req){
    // show error if necessary
	if (req.status != 200 ) { 
		pnshowajaxerror(req.responseText);
		return;
	}
	var json = pndejsonize(req.responseText);
	Element.update('content', json.content);
	if (json.stop != 1) {
		var pars = "module=Multisites&func=actualize&modules=" + json.modules + "&id=" + json.id;
		var myAjax = new Ajax.Request("ajax.php", {
			method: 'get',
			parameters: pars,
			onComplete: actualize_response,
			onFailure: actualize_failure
		});
	}
}

function actualize_failure(){
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}
*/
