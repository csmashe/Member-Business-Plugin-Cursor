/**
 * Admin JavaScript for Post 116 Business Directory
 */

(function($) {
    'use strict';
    
    var Post116BDAdmin = {
        init: function() {
            this.bindEvents();
            this.initSortable();
        },
        
        bindEvents: function() {
            // Add owner
            $(document).on('click', '#add-owner', this.addOwner.bind(this));
            
            // Remove owner
            $(document).on('click', '.remove-owner', this.removeOwner.bind(this));
            
            // Add link
            $(document).on('click', '#add-link', this.addLink.bind(this));
            
            // Remove link
            $(document).on('click', '.remove-link', this.removeLink.bind(this));
            
            // Reorder owners
            $(document).on('sortstop', '#post116-owners-container', this.reorderOwners.bind(this));
            
            // Reorder links
            $(document).on('sortstop', '#post116-links-container', this.reorderLinks.bind(this));
        },
        
        initSortable: function() {
            if ($.fn.sortable) {
                $('#post116-owners-container').sortable({
                    items: '.post116-owner-group',
                    handle: '.owner-header',
                    placeholder: 'post116-sortable-placeholder',
                    update: function() {
                        Post116BDAdmin.updateOwnerIndexes();
                    }
                });
                
                $('#post116-links-container').sortable({
                    items: '.post116-link-group',
                    handle: '.link-header',
                    placeholder: 'post116-sortable-placeholder',
                    update: function() {
                        Post116BDAdmin.updateLinkIndexes();
                    }
                });
            }
        },
        
        addOwner: function(e) {
            e.preventDefault();
            
            var $container = $('#post116-owners-container');
            var currentCount = $container.find('.post116-owner-group').length;
            var index = currentCount;
            
            var ownerHtml = this.getOwnerHtml(index);
            $container.append(ownerHtml);
            
            this.updateOwnerIndexes();
        },
        
        removeOwner: function(e) {
            e.preventDefault();
            
            var $group = $(e.currentTarget).closest('.post116-owner-group');
            $group.fadeOut(300, function() {
                $(this).remove();
                Post116BDAdmin.updateOwnerIndexes();
            });
        },
        
        addLink: function(e) {
            e.preventDefault();
            
            var $container = $('#post116-links-container');
            var currentCount = $container.find('.post116-link-group').length;
            var index = currentCount;
            
            var linkHtml = this.getLinkHtml(index);
            $container.append(linkHtml);
            
            this.updateLinkIndexes();
        },
        
        removeLink: function(e) {
            e.preventDefault();
            
            var $group = $(e.currentTarget).closest('.post116-link-group');
            $group.fadeOut(300, function() {
                $(this).remove();
                Post116BDAdmin.updateLinkIndexes();
            });
        },
        
        getOwnerHtml: function(index) {
            return '<div class="post116-owner-group" data-index="' + index + '">' +
                '<div class="owner-header">' +
                    '<h4>Owner ' + (index + 1) + '</h4>' +
                    '<button type="button" class="button-link remove-owner">Remove</button>' +
                '</div>' +
                '<table class="form-table">' +
                    '<tr>' +
                        '<th><label for="owner_name_' + index + '">Name *</label></th>' +
                        '<td><input type="text" id="owner_name_' + index + '" name="owners[' + index + '][owner_name]" value="" class="regular-text" required /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label for="owner_role_' + index + '">Role</label></th>' +
                        '<td><input type="text" id="owner_role_' + index + '" name="owners[' + index + '][owner_role]" value="" class="regular-text" placeholder="e.g., Owner, Manager, Partner" /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label for="owner_email_' + index + '">Email</label></th>' +
                        '<td><input type="email" id="owner_email_' + index + '" name="owners[' + index + '][owner_email]" value="" class="regular-text" /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label for="owner_phone_' + index + '">Phone</label></th>' +
                        '<td><input type="tel" id="owner_phone_' + index + '" name="owners[' + index + '][owner_phone]" value="" class="regular-text" /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label for="owner_website_' + index + '">Website</label></th>' +
                        '<td><input type="url" id="owner_website_' + index + '" name="owners[' + index + '][owner_website]" value="" class="regular-text" placeholder="https://" /></td>' +
                    '</tr>' +
                '</table>' +
            '</div>';
        },
        
        getLinkHtml: function(index) {
            return '<div class="post116-link-group" data-index="' + index + '">' +
                '<div class="link-header">' +
                    '<button type="button" class="button-link remove-link">Remove</button>' +
                '</div>' +
                '<table class="form-table">' +
                    '<tr>' +
                        '<th><label for="link_label_' + index + '">Label</label></th>' +
                        '<td><input type="text" id="link_label_' + index + '" name="links[' + index + '][link_label]" value="" class="regular-text" placeholder="e.g., Facebook, LinkedIn" /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label for="link_url_' + index + '">URL</label></th>' +
                        '<td><input type="url" id="link_url_' + index + '" name="links[' + index + '][link_url]" value="" class="regular-text" placeholder="https://" /></td>' +
                    '</tr>' +
                '</table>' +
            '</div>';
        },
        
        updateOwnerIndexes: function() {
            $('#post116-owners-container .post116-owner-group').each(function(index) {
                var $group = $(this);
                $group.attr('data-index', index);
                
                // Update header
                $group.find('.owner-header h4').text('Owner ' + (index + 1));
                
                // Update form field names and IDs
                $group.find('input, label').each(function() {
                    var $field = $(this);
                    var name = $field.attr('name');
                    var id = $field.attr('id');
                    
                    if (name) {
                        var newName = name.replace(/owners\[\d+\]/, 'owners[' + index + ']');
                        $field.attr('name', newName);
                    }
                    
                    if (id) {
                        var newId = id.replace(/_\d+$/, '_' + index);
                        $field.attr('id', newId);
                    }
                });
                
                // Update label for attributes
                $group.find('label').each(function() {
                    var $label = $(this);
                    var forAttr = $label.attr('for');
                    if (forAttr) {
                        var newFor = forAttr.replace(/_\d+$/, '_' + index);
                        $label.attr('for', newFor);
                    }
                });
            });
        },
        
        updateLinkIndexes: function() {
            $('#post116-links-container .post116-link-group').each(function(index) {
                var $group = $(this);
                $group.attr('data-index', index);
                
                // Update form field names and IDs
                $group.find('input, label').each(function() {
                    var $field = $(this);
                    var name = $field.attr('name');
                    var id = $field.attr('id');
                    
                    if (name) {
                        var newName = name.replace(/links\[\d+\]/, 'links[' + index + ']');
                        $field.attr('name', newName);
                    }
                    
                    if (id) {
                        var newId = id.replace(/_\d+$/, '_' + index);
                        $field.attr('id', newId);
                    }
                });
                
                // Update label for attributes
                $group.find('label').each(function() {
                    var $label = $(this);
                    var forAttr = $label.attr('for');
                    if (forAttr) {
                        var newFor = forAttr.replace(/_\d+$/, '_' + index);
                        $label.attr('for', newFor);
                    }
                });
            });
        },
        
        reorderOwners: function() {
            this.updateOwnerIndexes();
        },
        
        reorderLinks: function() {
            this.updateLinkIndexes();
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        Post116BDAdmin.init();
    });
    
})(jQuery);