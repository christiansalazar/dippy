var Dippy = function(options){
	var div = $('#'+options.id);
	this.change = function(next, prior){  
		//alert('original '+options.id); 
	};

	var logger = function(text){
		var d = $('#'+options.logid);
		if(d != null)
			d.append("<p style='border: 1px dotted red; font-size: 10px; color: darkred; font-family: monospace'>"
				+text+"</p>");
	}

	var ajaxcmd = function(action,postdata,callback){
		var result=false;
		var nocache=function(){
			var dateObject = new Date();
			var uuid = dateObject.getTime();
			return "&nocache="+uuid;
		}
		var url = action+nocache();
		logger(url);
		jQuery.ajax({
			url: url,
			type: 'post',
			async: true,
			contentType: "application/json",
			data: postdata,
			success: function(data, textStatus, jqXHR){
				result = data;
				if(callback != null){
					logger('true. '+data);
					callback(true, data);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				logger('false. '+jqXHR.responseText);
				callback(false, jqXHR.responseText);
				return false;
			},
		});
		return result;
	}

	var onDelete = function(id, item){
		logger("onDelete "+id+" ");
		var parent = div.data('parent');
		if(confirm(options.deleteconfirmation+', item: '+id)){
			item.removeClass('drow');
			item.addClass('drow-removed');
			ajaxcmd(options.action+'&action=delete&parent='+parent+'&id='+id
					+'&data='+options.data,
				'',function(ok,data){
					if(ok){
						item.remove();
					}else{
						item.addClass('drow');
						item.removeClass('drow-removed');
					}
				});
		}
	}

	var validate = function(v){
		if(options.validateRegExp == '')
			return true;

		return true;
	}

	var onUpdate = function(id, item){
		var span = item.find('span.text');
		if(span.data('busy')==true){
			span.html(span.data('saved'));
			span.data('busy',false);
			return;
		}	

		var text = span.html();
		span.data('busy',true);
		span.data('saved',text);
		span.html("");
		span.append("<input class='drow-text' type='text' value='"+text+"'>");
		var input = span.find('input');
		input.attr('title',options.enterSaveText);
		input.keyup(function(){
			if(event.keyCode == 13){
				var value = jQuery.trim($(this).val());	
				if(validate(value)){
					item.removeClass("drow");
					item.addClass("drow-edit");
					input.attr('disabled','disabled');

					ajaxcmd(options.action+'&action=update&parent='+parent
						+'&id='+id+'&data='+options.data,
						value,function(ok,data){
							if(ok){
								span.html(data);
								span.data('busy',false);
								item.removeClass('drow-edit');
								item.addClass('drow');
							}else{
								item.removeClass('drow-edit');
								item.addClass('drow');
								input.attr('disabled',null);
							}
						});

				}else{
					alert(options.validateErrorText);
				}
			}
		});
	}

	var newItem = function(id, label){
		div.append( 
		"<div alt='"+id+"' class='drow'>"+
		"<img class='wait' src='"+options.imgWait+"'>"+
		"<span class='text' title='"+id+"'>"+label+"</span>"+
		"<img class='ddelete' src='"+options.imgDelete+"' >"+
		"<img class='dupdate' src='"+options.imgUpdate+"' >"+
		"</div>");

		var item = div.find("[alt|='"+id+"']");

		item.data('_this',this);

		item.find('img.ddelete').click(function(){ onDelete(item.attr('alt'), 
			item) });

		item.find('img.dupdate').click(function(){ onUpdate(item.attr('alt'), 
			item) });

		item.click(function(){
			var ci = div.data('current_item');
			var id = $(this).attr('alt');
			if(ci != id){
				var old_id = div.data('current_item');
				var prior = div.find("[alt|='"+old_id+"']");
				var item  = $(this);
				div.data('current_item',id);
				item.addClass('drow-selected');	
				prior.removeClass('drow-selected');
				var _div = item.parent();
				var _this = _div.data('_this');
				_this.change(item, prior);
				options.onChange(options.id, id);
			}
		});
	}
	
	var clearDiv = function(){
		div.find('.drow').each(function(){ $(this).remove(); });
	}


	var refresh = function(id){
		logger('refresh: '+id);
		div.data('parent',id);
		
		clearDiv();
		
		ajaxcmd(options.action+'&action=refresh&parent='+id
				+'&data='+options.data,
			'',function(ok,data){
				if(ok){
					$.each(data,function(k, v){
						newItem(k,v);
					});
				}else{

				}
			});
	}

	div.find('.dcontrol a.newItem').click(function(){
		var id = div.data('parent');
		var a = $(this);
		if(a.data('busy')==true)
			return;
		a.find('img').show();
		a.css('color','gray');
		a.data('busy',true);
		ajaxcmd(options.action+'&action=newitem&parent='+id
				+'&data='+options.data,
			'',function(ok,data){
				a.find('img').hide();
				a.css('color','blue');
				a.data('busy',false);
				if(ok){
					newItem(data.id, data.text);	
				}
			});
	});

	div.data('is_dippy',true);
	div.data('_this',this);

	var parent = $('#'+options.parent);
	if(parent.data('is_dippy') == true){
		var _this = parent.data('_this');
		_this.change = function(next, prior){
			var parent_id = next.attr('alt');		
			refresh(parent_id);
		};
	}else{
		var value = parent.val();
		parent.change(function(){
			refresh($(this).val());		
		});
		refresh(value);
	}


};
