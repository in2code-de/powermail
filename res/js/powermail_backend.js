Ext.ns('Powermail');

Ext.onReady( function() {
	Ext.QuickTips.init();
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	new Powermail.topMenu.init();
	new Powermail.grid.init();
});

Powermail.topMenu = {
	init: function() {
		var icon_xls = '<a href="?export=xls&pid=' + Powermail.statics.pid + '&startDateTime=' + Powermail.statics.startDateTime + '&endDateTime=' + Powermail.statics.endDateTime + '" target="_blank">' + Powermail.statics.excelIcon + '</a>';
		var icon_csv = '<a href="?export=csv&pid=' + Powermail.statics.pid + '&startDateTime=' + Powermail.statics.startDateTime + '&endDateTime=' + Powermail.statics.endDateTime + '" target="_blank">' + Powermail.statics.csvIcon + '</a>';
		var icon_html = '<a href="?export=html&pid=' + Powermail.statics.pid + '&startDateTime=' + Powermail.statics.startDateTime + '&endDateTime=' + Powermail.statics.endDateTime + '" target="_blank">' + Powermail.statics.htmlIcon + '</a>';
		var icon_pdf = '<a href="?export=pdf&pid=' + Powermail.statics.pid + '&startDateTime=' + Powermail.statics.startDateTime + '&endDateTime=' + Powermail.statics.endDateTime + '" target="_blank">' + Powermail.statics.pdfIcon + '</a>';
		if (!Powermail.statics.phpexcel_library_loaded) {
			icon_xls = '<a href="#" onclick="msg(\'' + Powermail.lang.noExcel + '\'); return false;" target="_blank" style="filter:alpha(opacity=30); -moz-opacity: 0.30; opacity: 0.30;">' + Powermail.statics.excelIcon + '</a>';
		}
		var powermailtopmenu = new Ext.Toolbar({
			id: 'topmenu',
		    width: 'auto',
		    renderTo: 'typo3-docheader-row1',
		    hideBorders: true,
		    items: [
		        Powermail.lang.exportAs,
				{
		            xtype: 'linkbutton',
		            text: Powermail.lang.exportExcelText,
		            html: icon_xls
		        },
		        {
					xtype: 'tbspacer', 
					width: 5
				},
		        {
		            xtype: 'linkbutton',
		            text: Powermail.lang.exportCsvText,
		            html: icon_csv
		        },
		        {
		            xtype: 'linkbutton',
					xtype: 'tbspacer', 
					width: 5
				},
		        {
		            xtype: 'linkbutton',
		            text: Powermail.lang.exportHtmlText,
		            html: icon_html
		        },
		        {
					xtype: 'tbspacer', 
					width: 5
				},
		        {
		            xtype: 'linkbutton',
		            text: Powermail.lang.exportPdfText,
		            html: icon_pdf
		        },
		        '->',
				{
		            xtype: 'button',
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
	 		//alert(v);
	 		i = 0;
	 		Ext.iterate(v,function(key, value) { 
	 			if(Ext.isObject(value)) {
	 				//console.log(value);
	 				newValues = new Array();
	 				Ext.iterate(value, function(key2, value2){
	 					newValues.push(value2);
	 					//console.log(value2);
	 				});
	 				value = newValues.join(', ');
	 			} else if(Ext.isArray(value)) {
	 				newValues = new Array();
	 				Ext.each(value, function(item){
	 					newValues.push(item);
	 				});
	 				value = newValues.join(', ');
	 			}
	 			returnPiVars += '<tr class="' + ((i % 2) ? 'even' : 'odd') + '"><th>' + Powermail.statics[key] + ':</th><td>' + value + '</td></tr>';
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
					height: 100,
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
					text: 'Filter',
					id: 'filterButton',
					html: Powermail.statics.filterIcon,
					handler: function_filter
				}
			],
			bbar: [
				{
					/****************************************************
					 * Paging toolbar
					 ****************************************************/
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
					tooltip: Powermail.lang.deleteButton_tooltip,
					iconCls: 'delete',
					handler: function_delete
				}
			]
		});

		/****************************************************
		 * get labels for piVars
		 ****************************************************/
	 	
		var labelstore = new Ext.data.JsonStore({
 			url: Powermail.statics.ajaxController + '&cmd=getLabels',
 			root: 'labels',
 			fields: [
 			   'uid', 
 			   'title'
 			],
 			listeners: {
 				load: {
					fn: function(store, records, options){
						for(i=0; i < store.getCount(); i++) {
							Powermail.statics['uid' + store.getAt(i).data.uid] = store.getAt(i).data.title;
						}
						gridDs.load();
					}
				}
 			}
 		});
		
		labelstore.load({params: {pid: Powermail.statics.pid}});

         /****************************************************
          * get field types for piVars
          ****************************************************/

         var formtypestore = new Ext.data.JsonStore({
              url: Powermail.statics.ajaxController + '&cmd=getFormTypes',
              root: 'formTypes',
              fields: [
                 'uid',
                 'formtype'
              ],
              listeners: {
                  load: {
                     fn: function(store, records, options){
                         for(i=0; i < store.getCount(); i++) {
                             Powermail.statics['uid' + store.getAt(i).data.uid] = store.getAt(i).data.formtype;
                         }
                         gridDs.load();
                     }
                 }
              }
          });

         formtypestore.load({params: {pid: Powermail.statics.pid}});
		//gridDs.load();
	}
};

function msg(string) {
	Ext.Msg.alert('Error:', string);
}