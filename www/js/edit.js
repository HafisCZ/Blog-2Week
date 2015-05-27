//Event.observe(window, 'load', init, false);

function init() {
  makeEditable('comment');
}

function makeEditable(id) {
  alert("Editable maken !!!");
  Event.observe(id, 'click', function(){edit($(id))}, false);
}

function edit(obj) {
  alert("Even got pressed !!!");
  Element.hide(obj);
  
  var textarea = '<div id="' + obj.id + '_editor"><textarea id="' + obj.id + '_edit" name="' + obj.id + '" rows="4" cols="60">' + obj.innerHTML + '</textarea>';
  var button = '<input id="' + obj.id + '_save" type="button" value="Uložit" /> <input id="' + obj.id  + '_cancel" type="button" value="Zrušit"/></div>';
  
  new Insertion.After(obj, textarea+button);
  
  Event.observe(obj.id+'_save', 'click', function(){saveChanges(obj)}, false);
  Event.observe(obj.id+'_cancel', 'click', function(){cleanUp(obj)}, false);  
}

function cleanUp(obj) {
  Element.remove(obj.id + '_editor');
  Element.show(obj);
}

function saveChanges(obj) {
  var new_data = escape($F(obj.id + '_edit'));
  obj.innerHTML = "Ukládání ...";
  cleanUp(obj);
  
  var success = function(t){editCompelete(t, obj);}
  var failure = function(t){editFailed(t, obj);}
  
  var url = comment.php;
  var pars = 'id=' + obj.id + '$content=' + new_content;
  var ajax = new Ajax.Request(url, {method:'post', postBody:pars, onSuccess:success, onFailure:failure});
}

function editComplete(t, obj) {
  obj.innerHTML = t.responseText;
}

function editFailed(t, obj) {
  obj.innerHTML = 'Nastala chyba při ukládání.';
  cleanUp(obj);
}