/**
 * PagingToolbarResizer plugin for Ext PagingToolbar
 *
 * Contains a combobox where user can choose the pagesize dynamically
 *
 * @author    Loiane Groner <http://loianegroner.com> <http://loiane.com>
 * @date      June 01, 2010
 * @version   1
 *
 * @license Ext.ux.plugin.PagingToolbarResizer is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 * 
 * License details: http://www.gnu.org/licenses/lgpl.html
 */
Ext.namespace('Ext.ux.plugin');

/**
 * @class Ext.ux.plugin.PagingToolbarResizer
 * @extends Ext.Component
 *
 * Creates new PagingToolbarResizer plugin
 * @constructor
 * @param {Object} config The config object
 * 
 * How to use

 * 
	Just instaciate a new PagingToolbarResizer inside PagingToolbar plugins option:

	bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: store,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display",
            plugins : [new Ext.ux.plugin.PagingToolbarResizer( {options : [ 50, 100, 500 ] })]
    })
 */
Ext.ux.plugin.PagingToolbarResizer = Ext.extend(Object, {

  /**
   * @cfg {Ext.data.Store} options
   * The {@link Ext.data.Store} combobox should use as its data source (required).
   * You can also use an array of integers.
   * Defaults to [5, 10, 15, 20, 25, 30, 50, 75, 100, 200, 300, 500, 1000]
   */	
  options: [5, 10, 15, 20, 25, 30, 50, 75, 100, 200, 300, 500, 1000],
  
  /**
   * @cfg {String} mode Acceptable values are:
   * 
   * 
	'remote' : Default
   * 
	Automatically loads the {@link #store} the first time the trigger
   * is clicked. If you do not want the store to be automatically loaded the first time the trigger is
   * clicked, set to 'local' and manually load the store.  To force a requery of the store
   * every time the trigger is clicked see {@link #lastQuery}.
   * 
	'local' :
   * 
	ComboBox loads local data
   * 
   * 
   */
  mode: 'remote',
  
  /**
   * @cfg {String} displayText
   * The message to display before the combobox (defaults to 'Records per Page')
   */
  displayText: 'Records per Page',
  
  /**
   * @cfg {Boolean} prependCombo
   * true to insert the combobox before the paging buttons.
   * Defaults to false.
   */
  prependCombo: false,

  constructor: function(config){
	
    Ext.apply(this, config);
    Ext.ux.plugin.PagingToolbarResizer.superclass.constructor.call(this, config);
  },

  init : function(pagingToolbar) {
	
	var comboStore = this.options;
	  
    var combo = new Ext.form.ComboBox({
      typeAhead: false,
      triggerAction: 'all',
      forceSelection: true,
      selectOnFocus:true,
      lazyRender:true,
      editable: false,
      mode: this.mode,
      value: pagingToolbar.pageSize,
      width:50,
      store: comboStore,
      listeners: {
        select: function(combo, value, i){
          pagingToolbar.pageSize = comboStore[i];
          Powermail.statics.pagingSize = pagingToolbar.pageSize;
          pagingToolbar.doLoad(Math.floor(pagingToolbar.cursor/pagingToolbar.pageSize)*pagingToolbar.pageSize);
        }
      }
    });

    var index = 0;
    
    if (this.prependCombo){
    	index = pagingToolbar.items.indexOf(pagingToolbar.first);
    	index--;
    } else{
    	index = pagingToolbar.items.indexOf(pagingToolbar.refresh);
        pagingToolbar.insert(++index,'-');
    }
    
    pagingToolbar.insert(++index, this.displayText);
    pagingToolbar.insert(++index, combo);
    
    if (this.prependCombo){
    	 pagingToolbar.insert(++index,'-');
    }
    
    //destroy combobox before destroying the paging toolbar
    pagingToolbar.on({
      beforedestroy: function(){
    	combo.destroy();
      }
    });

  }
});
