(function() {
    tinymce.create('tinymce.plugins.lineheight', {
        init: function(ed, url) {
            ed.addButton('lineheightselect', {
                type: 'listbox',
                text: 'Line Height',
                icon: false,
                menu: [
                    
                    {text: '1', value: '1'},
                    {text: '1.2', value: '1.2'},
                    {text: '1.5', value: '1.5'},
                    {text: '1.8', value: '1.8'},
                    {text: '2', value: '2'},
                    {text: '2.5', value: '2.5'}
                ],
                onselect: function(e) {
                    var value = this.value();
                    var selectedNode = ed.selection.getNode();

                    // If the selected node is not a text node, apply the line-height to its parent element
                    if (selectedNode.nodeType === 3) {
                        selectedNode = selectedNode.parentNode;
                    }

                    // Check if the selected node has an inline style
                    var currentStyle = selectedNode.style.lineHeight;

                    // If line-height is already applied, use the existing line-height, otherwise set the new value
                    selectedNode.style.lineHeight = value;

                    // If other inline styles already exist, we don't want to erase them.
                    // We just append the new style to the inline styles.
                    if (currentStyle) {
                        selectedNode.style.lineHeight = value;
                    } else {
                        selectedNode.style.setProperty('line-height', value);
                    }
                }
            });
        }
    });

    // Register the plugin
    tinymce.PluginManager.add('lineheight', tinymce.plugins.lineheight);
})();
