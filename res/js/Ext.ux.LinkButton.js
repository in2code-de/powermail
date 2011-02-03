/**
 * @class Ext.LinkButton
 * @extends Ext.Button
 * A Button which encapsulates an &lt;a> element to enable navigation, or downloading of files.
 * @constructor
 * Creates a new LinkButton
 */ 
Ext.LinkButton = Ext.extend(Ext.Button, {
    template: new Ext.Template(
        '<table id="{4}" cellspacing="0" class="x-btn {3}"><tbody class="{1}">',
        '<tr><td class="x-btn-tl"><i> </i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i> </i></td></tr>',
        '<tr><td class="x-btn-ml"><i> </i></td><td class="x-btn-mc"><em class="{2}" unselectable="on"><a href="{5}" style="display:block" target="{6}" class="x-btn-text">{0}</a></em></td><td class="x-btn-mr"><i> </i></td></tr>',
        '<tr><td class="x-btn-bl"><i> </i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i> </i></td></tr>',
        '</tbody></table>').compile(),

    buttonSelector : 'a:first',

    /** 
     * @cfg String href
     * The URL to create a link for.
     */
    /** 
     * @cfg String target
     * The target for the &lt;a> element.
     */
    /** 
     * @cfg Object
     * A set of parameters which are always passed to the URL specified in the href
     */
    baseParams: {},

//  private
    params: {},

    getTemplateArgs: function() {
        return Ext.Button.prototype.getTemplateArgs.apply(this).concat([this.getHref(), this.target]);
    },

    onClick : function(e){
        if(e.button != 0){
            return;
        }
        if(this.disabled){
            e.stopEvent();
        } else {
            if (this.fireEvent("click", this, e) !== false) {
                if(this.handler){
                    this.handler.call(this.scope || this, this, e);
                }
            }
        }
    },

    // private
    getHref: function() {
        var result = this.href;
        var p = Ext.urlEncode(Ext.apply(Ext.apply({}, this.baseParams), this.params));
        if (p.length) {
            result += ((this.href.indexOf('?') == -1) ? '?' : '&') + p;
        }
        return result;
    },

    /**
     * Sets the href of the link dynamically according to the params passed, and any {@link #baseParams} configured.
     * @param {Object} Parameters to use in the href URL.
     */
    setParams: function(p) {
        this.params = p;
        this.el.child(this.buttonSelector, true).href = this.getHref();
    }
});
Ext.reg('linkbutton', Ext.LinkButton);