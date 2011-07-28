Ext.ns('Powermail');

Ext.onReady( function() {
	Ext.QuickTips.init();
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	if (Powermail.statics.mailsOnCurrentPage) {
		new Powermail.topMenu.init();
		new Powermail.grid.init();
	} else {
		new Powermail.noRows.init();
	}
});

Powermail.noRows = {
	init: function() {
		Ext.MessageBox.show({
			title: Powermail.lang.noMails1,
			msg: Powermail.lang.noMails2,
			buttons: Ext.MessageBox.OK,
			minWidth: 300,
			minHeight: 200,
			icon: Ext.MessageBox.INFO
		});
	},
	reload: function() {
		Ext.getCmp('noRows').destroy();		
		new Powermail.noRows.init();
	}
};

Powermail.topMenu = {
	init: function() {
        var filterUrlPart = '&pid=' + Powermail.statics.pid + '&startDateTime=' + Powermail.statics.startDateTime + '&endDateTime=' + Powermail.statics.endDateTime;
        var xls_button = Powermail.statics.enableXlsExport ? {tag: 'a', href: '?export=xls' + filterUrlPart, target:'_self', html: Powermail.statics.excelIcon, title: Powermail.lang.exportExcelText} : {};
        var csv_button = Powermail.statics.enableCsvExport ? {tag: 'a', href: '?export=csv' + filterUrlPart, target:'_self', html: Powermail.statics.csvIcon, title: Powermail.lang.exportCsvText} : {};
        var html_button = Powermail.statics.enableHtmlExport ? {tag: 'a', href: '?export=html' + filterUrlPart, target:'_self', html: Powermail.statics.htmlIcon, title: Powermail.lang.exportHtmlText} : {};
        var pdf_button = Powermail.statics.enablePdfExport ? {tag: 'a', href: '?export=pdf' + filterUrlPart, target:'_self', html: Powermail.statics.pdfIcon, title: Powermail.lang.exportPdfText} : {};
		var powermailtopmenu = new Ext.Toolbar({
			id: 'topmenu',
		    width: 'auto',
		    renderTo: 'typo3-docheader-row1',
		    hideBorders: true,
		    items: [
		        Powermail.lang.exportAs,
                {
                    xtype: 'box',
                    autoEl: xls_button
                },
		        {
					xtype: 'tbspacer', 
					width: 5
				},
		        {
		            xtype: 'box',
		            autoEl: csv_button
		        },
		        {
					xtype: 'tbspacer',
					width: 5
				},
		        {
		            xtype: 'box',
		            autoEl: html_button
		        },
		        {
					xtype: 'tbspacer', 
					width: 5
				},
		        {
		            xtype: 'box',
		            autoEl:  pdf_button
		        },
		        '->',
				{
		            xtype: 'box',
		            text: Powermail.lang.createShortcut,
		            html: Powermail.statics.shortcutLink
		        }
		    ]
		});
	},
	reload: function() {
		Ext.getCmp('topmenu').destroy();		
		new Powermail.topMenu.init();
	}
};
Powermail.utility = {
	updatePageTree: function() {
		if (top && top.content && top.content.nav_frame && top.content.nav_frame.Tree) {
			top.content.nav_frame.Tree.refresh();
		}
	}
};

Powermail.grid = {
	/**
	 * Initianalize the grid
	 *
	 * @return void
	 **/
	 init: function() {

 		/****************************************************
		 * row checkbox
		 ****************************************************/
	 	var sm = new Ext.grid.CheckboxSelectionModel({
	 		singleSelect: false
	 	});

		/****************************************************
		 * row expander
		 ****************************************************/

		var expander = new Ext.grid.RowExpander({
			tpl : new Ext.Template(
				'<div class="tx_powermail-piVars">{piVars}</div>'
			)
		});

		/****************************************************
		 * showPiVars
		 ****************************************************/

		var showPiVars = function(v, record){
	 		var returnPiVars = '<table>';
	 		i = 0;
	 		Ext.iterate(v,function(key, value) {
                if(!Ext.isObject(Powermail.statics[key])) {
                    Powermail.statics[key] = {'title': key, 'formtype': 'text'};
                }
	 			if(Ext.isObject(value)) {
	 				newValues = new Array();
	 				Ext.iterate(value, function(key2, value2){
	 					newValues.push(value2);
	 				});
	 				value = newValues.join(', ');
	 			} else if(Ext.isArray(value)) {
	 				newValues = new Array();
	 				Ext.each(value, function(item){
	 					newValues.push(item);
	 				});
	 				value = newValues.join(', ');
	 			}
                switch (Powermail.statics[key].formtype) {
                    case 'date':
                        value = (parseInt(value) == value) ? new Date(parseInt(value) * 1000 + new Date(parseInt(value) * 1000).getTimezoneOffset() * 60 * 1000).format(Powermail.statics.dateFormat) : value;
                        break;
                    case 'datetime':
                        value = (parseInt(value) == value) ? new Date(parseInt(value) * 1000 + new Date(parseInt(value) * 1000).getTimezoneOffset() * 60 * 1000).format(Powermail.statics.datetimeFormat) : value;
                        break;
                    case 'email':
                        value = makeEmailLink(value);
                        break;
                    case 'file':
                        value = makeFileLink(value);
                        break;
                    case 'text':
                        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
                        if(value.search(emailRegEx) != -1) {
                            value = makeEmailLink(value);
                        }
                }
	 			returnPiVars += '<tr class="' + ((i % 2) ? 'even' : 'odd') + '"><th>' + Powermail.statics[key].title + ':</th><td>' + value + '</td></tr>';
	 			i ++;
	 		});
	 		return returnPiVars + '</table>';
	 	}

		/****************************************************
		 * makeEmailLink
		 ****************************************************/

		var makeEmailLink = function(v, record){
	 		return '<a href="mailto:' + v + '">' + v + '</a>';
	 	}

        /****************************************************
        * makeFileLink
        ****************************************************/

        var makeFileLink = function(v, record){
          return '<a href="/' + Powermail.statics.uploadFolder + v + '" target="_blank">' + v + '</a>';
        }

		/****************************************************
		 * grid datastore
		 ****************************************************/

		var gridDs = new Ext.data.Store({
	 		storeId: 'powermailRecordsStore',
			reader: new Ext.data.JsonReader({
					totalProperty: 'results',
					mindatetime: 'mindatetime',
					maxdatetime: 'maxdatetime',
					root: 'rows'
				},[
					{name: 'id', type: 'int'},
					{name: 'uid', type: 'int'},
					{name: 'crdate'},
					{name: 'sender', convert: makeEmailLink},
					{name: 'recipient', convert: makeEmailLink},
					{name: 'senderIP'},
					{name: 'piVars', convert: showPiVars}
				]
			),
			sortInfo: {
				field: 'crdate',
				direction: 'DESC'
			},
			remoteSort: true,
			url: Powermail.statics.ajaxController + '&cmd=getItems',
			baseParams: {
				pid: Powermail.statics.pid,
				start: 0,
				pagingSize: Powermail.statics.pagingSize,
				sort: Powermail.statics.sort,
				dir: Powermail.statics.dir,
				startDateTime: Powermail.statics.startDateTime,
				endDateTime: Powermail.statics.endDateTime
			},
			listeners: {
				beforeload: function() {
						this.baseParams.pagingSize = Powermail.statics.pagingSize;
						this.baseParams.startDateTime = Powermail.statics.startDateTime;
						this.baseParams.endDateTime = Powermail.statics.endDateTime;
						this.removeAll();
					},
				load: function() {
						if(Powermail.statics.startDateTime == 0) {
							Ext.getCmp('beginDateTime').setValue(gridDs.reader.jsonData.mindatetime);
					    }
						if(Powermail.statics.endDateTime == 0) {
							Ext.getCmp('endDateTime').setValue(new Date().getTime());
						}
						gridContainer.getSelectionModel().clearSelections();
			 			//Powermail.statics.startDateTime = Ext.getCmp('beginDateTime').getValue().format('U');
			 			//Powermail.statics.endDateTime = Ext.getCmp('endDateTime').getValue().format('U');
						//Powermail.topMenu.reload();
					}
			}
		});
		
		
		/****************************************************
		 * deleting function
		 ****************************************************/

 		var function_delete = function(ob) {
			rowAction(ob, Powermail.lang.cmd_doDelete_confirmText, 'doDelete', Powermail.lang.title_delete, Powermail.lang.text_delete);
		};

		/****************************************************
		 * Row action function   ( deleted )
		 ****************************************************/
		
 		var rowAction = function(ob, confirmQuestion, cmd, confirmTitle, confirmText) {
			var recArray = gridContainer.getSelectionModel().getSelections();
			if (recArray.length > 0){
	            var prez = [];
	            for (i = 0; i < recArray.length; i++) {
	                prez.push(recArray[i].json.uid);
				}
				
				var frmConfirm = new Ext.Window({
					xtype: 'form',
					width: 200,
					height: 'auto',
					modal: true,
					title: confirmTitle,
					items: [
						{
							xtype: 'label',
							text: confirmText
						},{
							xtype: 'label',
							text:  confirmQuestion
						}
					],
					buttons: [
						{
							text: Powermail.lang.yes,
							handler: function(cmp, e) {
								Ext.Ajax.request({
									url: Powermail.statics.ajaxController + '&cmd=' + cmd,
									callback: function(options, success, response) {
										if (response.responseText === '1') {
											// reload the records and the table selector
											gridDs.reload();
										} else {
											alert('ERROR: ' + response.responseText);
										}
									},
									params: {'uids': Ext.encode(prez) }
								});

								frmConfirm.destroy();
							}
						},{
							text: Powermail.lang.no,
							handler: function(cmp, e) {
								frmConfirm.destroy();
							}
						}
					]
				});
				frmConfirm.show();
				
			} else {
				// no row selected
				Ext.MessageBox.show({
					title: Powermail.lang.error_NoSelectedRows_title,
					msg: Powermail.lang.error_NoSelectedRows_msg,
					buttons: Ext.MessageBox.OK,
					minWidth: 300,
					minHeight: 200,
					icon: Ext.MessageBox.INFO
				});
			}
		};
		
		/****************************************************
		 * Filter function
		 ****************************************************/

 		var function_filter = function(ob) {
 			
			filterAction(ob, 'getItems');
		};

		/****************************************************
		 * Filter action function
		 ****************************************************/
		
 		var filterAction = function(ob, cmd) {
 			Powermail.statics.startDateTime = Ext.getCmp('beginDateTime').getValue().format('U');
 			Powermail.statics.endDateTime = Ext.getCmp('endDateTime').getValue().format('U');
 			gridDs.reload();
 			Powermail.topMenu.reload();
		};
		
		/****************************************************
		 * grid container
		 ****************************************************/
		var gridContainer = new Ext.grid.GridPanel({
			layout: 'fit',
			renderTo: Powermail.statics.renderTo,
			width: '98%',
			frame: true,
			border: true,
			defaults: { autoScroll: false },
			plain: true,
			id: 'uid',
			loadMask: true,
			stripeRows: true,
			collapsible: false,
			animCollapse: false,
			store: gridDs,
			cm: new Ext.grid.ColumnModel ([
				sm,
				expander,
				{header: '#', width: 30, dataIndex: 'id', sortable: false, menuDisabled: true},
				{header: 'Uid', width: 30, dataIndex: 'uid', sortable: true, hidden: true},
				{header: Powermail.lang.date, width: 110, dataIndex: 'crdate', sortable: true},
				{header: Powermail.lang.sender, width: 150, dataIndex: 'sender', sortable: true},
				{header: Powermail.lang.receiver, width: 130, dataIndex: 'recipient', sortable: true},
				{header: Powermail.lang.senderIP, width: 170, dataIndex: 'senderIP', sortable: true}
			]),
			viewConfig: {
				forceFit: true
			},
			sm: sm,
			plugins: [expander, new Ext.ux.plugin.FitToParent()],
			tbar: [
		    	Powermail.lang.filterBegin,
		    	{
		    		xtype: 'xdatetime',
		    		id: 'beginDateTime',
		    		fieldLabel: Powermail.lang.filterBegin,
		    		timeFormat: 'H:i',
		    		timeWidth: 60,
		    		hiddenFormat: 'U',
	                timeConfig: {
		    			altFormats:'U',
	                	allowBlank:true    
	                },
	                dateFormat:'d.m.Y',
		    		dateWidth: 90,
	                dateConfig: {
	                    altFormats:'U',
	                    allowBlank:true
	                }
		    	},
		        {xtype: 'tbspacer', width: 10},
		        Powermail.lang.filterEnd,
		    	{
		    		xtype: 'xdatetime',
		    		id: 'endDateTime',
		    		fieldLabel: Powermail.lang.filterEnd,
		    		timeFormat: 'H:i',
		    		timeWidth: 60,
		    		hiddenFormat: 'U',
		    		otherToNow: true,
	                timeConfig: {
		    			altFormats:'U',
	                	allowBlank:true    
	                },
	                dateFormat:'d.m.Y',
		    		dateWidth: 90,
	                dateConfig: {
	                    altFormats:'U',
	                    allowBlank:true
	                }
		    	},
		        {xtype: 'tbspacer', width: 10},
				{
					xtype: 'button',
					id: 'filterButton',
					text: Powermail.statics.filterIcon,
					handler: function_filter
				}
			],
			bbar: [
				{
					/*****************************************************
					 * Paging toolbar
					 *****************************************************/

					id: 'recordPaging',
					xtype: 'paging',
					store: gridDs,
					pageSize: Powermail.statics.pagingSize,
					displayInfo: true,
					displayMsg: Powermail.lang.pagingMessage,
					emptyMsg: Powermail.lang.pagingEmpty,
					plugins: [new Ext.ux.plugin.PagingToolbarResizer( {options : [ 25, 50, 100, 1000 ], displayText: Powermail.lang.recordsPerPage, prependCombo: true})]
				}, '-', {
					/****************************************************
					 * Delete button
					 ****************************************************/

                    xtype: 'button',
                    width: 80,
                    id: 'deleteButton',
                    text: Powermail.lang.deleteButton_text,
                    title: Powermail.lang.deleteButton_tooltip,
                    iconCls: 'delete',
                    cls: 'x-btn-over',
                    handleMouseEvents: false,
                    handler: function_delete
				}
			]
		});

		/****************************************************
		 * get labels for piVars
		 ****************************************************/
	 	
		var labelstore = new Ext.data.JsonStore({
 			url: Powermail.statics.ajaxController + '&cmd=getLabelsAndFormtypes',
 			root: 'labels',
 			fields: [
 			   'uid', 
 			   'title',
               'formtype'
 			],
 			listeners: {
 				load: {
					fn: function(store, records, options){
						for(i=0; i < store.getCount(); i++) {
							Powermail.statics['uid' + store.getAt(i).data.uid] = {'title': store.getAt(i).data.title, 'formtype': store.getAt(i).data.formtype};
						}
						gridDs.load();
					}
				}
 			}
 		});
		
		labelstore.load({params: {pid: Powermail.statics.pid}});

 	}
};

function msg(string) {
	Ext.Msg.alert('Error:', string);
}