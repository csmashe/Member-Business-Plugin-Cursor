/**
 * Public JavaScript for Post 116 Business Directory
 */

(function($) {
    'use strict';
    
    var Post116BD = {
        init: function() {
            this.bindEvents();
            this.initAutocomplete();
        },
        
        bindEvents: function() {
            // Search input
            $(document).on('input', '#post116-search-input', this.debounce(this.handleSearch.bind(this), 300));
            
            // Filter changes
            $(document).on('change', '.post116-filter, .post116-ownership-filter', this.handleFilterChange.bind(this));
            
            // Clear search
            $(document).on('click', '#post116-search-clear', this.clearSearch.bind(this));
            
            // Load more
            $(document).on('click', '#post116-load-more', this.loadMore.bind(this));
            
            // Autocomplete selection
            $(document).on('click', '.post116-autocomplete-item', this.selectAutocompleteItem.bind(this));
        },
        
        initAutocomplete: function() {
            var $searchInput = $('#post116-search-input');
            if ($searchInput.length === 0) return;
            
            var autocompleteContainer = $('<div class="post116-autocomplete"></div>');
            $searchInput.after(autocompleteContainer);
            
            // Hide autocomplete when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.post116-search-bar').length) {
                    $('.post116-autocomplete').hide();
                }
            });
        },
        
        handleSearch: function(e) {
            var searchTerm = $(e.target).val();
            var $clearBtn = $('#post116-search-clear');
            
            if (searchTerm.length > 0) {
                $clearBtn.show();
                this.showAutocomplete(searchTerm);
            } else {
                $clearBtn.hide();
                $('.post116-autocomplete').hide();
            }
            
            this.performSearch();
        },
        
        handleFilterChange: function() {
            this.performSearch();
        },
        
        clearSearch: function() {
            $('#post116-search-input').val('');
            $('#post116-search-clear').hide();
            $('.post116-autocomplete').hide();
            this.performSearch();
        },
        
        showAutocomplete: function(searchTerm) {
            if (searchTerm.length < 2) {
                $('.post116-autocomplete').hide();
                return;
            }
            
            var self = this;
            $.ajax({
                url: post116_bd_ajax.rest_url + 'autocomplete',
                method: 'GET',
                data: {
                    search: searchTerm,
                    limit: 5
                },
                beforeSend: function() {
                    $('.post116-autocomplete').html('<div class="post116-autocomplete-loading">' + post116_bd_ajax.strings.loading + '</div>').show();
                },
                success: function(response) {
                    self.renderAutocomplete(response);
                },
                error: function() {
                    $('.post116-autocomplete').hide();
                }
            });
        },
        
        renderAutocomplete: function(suggestions) {
            var $container = $('.post116-autocomplete');
            
            if (suggestions.length === 0) {
                $container.hide();
                return;
            }
            
            var html = '<ul class="post116-autocomplete-list">';
            suggestions.forEach(function(item) {
                html += '<li class="post116-autocomplete-item" data-value="' + escapeHtml(item.name || item.title) + '">';
                html += '<span class="post116-autocomplete-text">' + escapeHtml(item.name || item.title) + '</span>';
                if (item.type) {
                    html += '<span class="post116-autocomplete-type">' + escapeHtml(item.type) + '</span>';
                }
                html += '</li>';
            });
            html += '</ul>';
            
            $container.html(html).show();
        },
        
        selectAutocompleteItem: function(e) {
            var value = $(e.currentTarget).data('value');
            $('#post116-search-input').val(value);
            $('.post116-autocomplete').hide();
            this.performSearch();
        },
        
        performSearch: function() {
            var self = this;
            var searchData = this.getSearchData();
            
            $.ajax({
                url: post116_bd_ajax.rest_url + 'search',
                method: 'GET',
                data: searchData,
                beforeSend: function() {
                    $('#post116-loading').show();
                    $('#post116-results').addClass('loading');
                },
                success: function(response) {
                    self.renderResults(response);
                },
                error: function() {
                    self.showError();
                },
                complete: function() {
                    $('#post116-loading').hide();
                    $('#post116-results').removeClass('loading');
                }
            });
        },
        
        getSearchData: function() {
            var data = {
                search: $('#post116-search-input').val(),
                category: $('#post116-category-filter').val(),
                veteran_owned: $('#post116-veteran-filter').is(':checked'),
                sons_owned: $('#post116-sons-filter').is(':checked'),
                auxiliary_owned: $('#post116-auxiliary-filter').is(':checked'),
                per_page: $('.post116-directory').data('per-page') || 20,
                page: 1
            };
            
            return data;
        },
        
        renderResults: function(response) {
            var $results = $('#post116-results');
            var $businessesGrid = $results.find('.post116-businesses-grid');
            
            if (response.businesses.length === 0) {
                $results.html('<div class="post116-no-results"><p>' + post116_bd_ajax.strings.no_results + '</p></div>');
                return;
            }
            
            var html = '<div class="post116-businesses-grid">';
            response.businesses.forEach(function(business) {
                html += this.renderBusinessCard(business);
            }.bind(this));
            html += '</div>';
            
            // Add pagination if needed
            if (response.pages > 1) {
                html += '<div class="post116-pagination">';
                html += '<button type="button" id="post116-load-more" class="post116-load-more" data-page="2" data-total-pages="' + response.pages + '">';
                html += 'Load More';
                html += '</button>';
                html += '</div>';
            }
            
            $results.html(html);
        },
        
        renderBusinessCard: function(business) {
            var html = '<div class="post116-business-card" data-business-id="' + business.id + '">';
            
            // Header
            html += '<div class="post116-business-header">';
            if (business.logo) {
                html += '<div class="post116-business-logo">';
                html += '<img src="' + escapeHtml(business.logo) + '" alt="' + escapeHtml(business.title) + '" />';
                html += '</div>';
            }
            
            html += '<div class="post116-business-info">';
            html += '<h3 class="post116-business-title">';
            html += '<a href="' + escapeHtml(business.url) + '">' + escapeHtml(business.title) + '</a>';
            html += '</h3>';
            
            // Categories
            if (business.categories && business.categories.length > 0) {
                html += '<div class="post116-business-categories">';
                business.categories.forEach(function(category) {
                    html += '<span class="post116-category-tag">' + escapeHtml(category.name) + '</span>';
                });
                html += '</div>';
            }
            
            // Ownership flags
            if (business.ownership.veteran_owned || business.ownership.sons_owned || business.ownership.auxiliary_owned) {
                html += '<div class="post116-ownership-flags">';
                if (business.ownership.veteran_owned) {
                    html += '<span class="post116-flag veteran">Veteran Owned</span>';
                }
                if (business.ownership.sons_owned) {
                    html += '<span class="post116-flag sons">Sons Owned</span>';
                }
                if (business.ownership.auxiliary_owned) {
                    html += '<span class="post116-flag auxiliary">Auxiliary Owned</span>';
                }
                html += '</div>';
            }
            
            html += '</div></div>';
            
            // Details
            html += '<div class="post116-business-details">';
            
            // Owners
            if (business.owners && business.owners.length > 0) {
                html += '<div class="post116-business-owners">';
                html += '<strong>Owners:</strong> ';
                var ownerNames = business.owners.map(function(owner) { return owner.owner_name; });
                html += escapeHtml(ownerNames.join(', '));
                html += '</div>';
            }
            
            // Location
            if (business.address.city) {
                html += '<div class="post116-business-location">';
                html += '<strong>Location:</strong> ' + escapeHtml(business.address.city);
                html += '</div>';
            }
            
            // Phone
            if (business.contact.phone) {
                html += '<div class="post116-business-phone">';
                html += '<strong>Phone:</strong> <a href="tel:' + escapeHtml(business.contact.phone) + '">' + escapeHtml(business.contact.phone) + '</a>';
                html += '</div>';
            }
            
            // Services
            if (business.services) {
                html += '<div class="post116-business-services">';
                html += '<strong>Services:</strong>';
                html += '<p>' + escapeHtml(business.services.substring(0, 100)) + (business.services.length > 100 ? '...' : '') + '</p>';
                html += '</div>';
            }
            
            html += '</div>';
            
            // Footer
            html += '<div class="post116-business-footer">';
            html += '<a href="' + escapeHtml(business.url) + '" class="post116-view-details">View Details</a>';
            html += '</div>';
            
            html += '</div>';
            
            return html;
        },
        
        loadMore: function(e) {
            var $button = $(e.currentTarget);
            var currentPage = parseInt($button.data('page'));
            var totalPages = parseInt($button.data('total-pages'));
            
            if (currentPage > totalPages) {
                $button.hide();
                return;
            }
            
            var self = this;
            var searchData = this.getSearchData();
            searchData.page = currentPage;
            
            $.ajax({
                url: post116_bd_ajax.rest_url + 'search',
                method: 'GET',
                data: searchData,
                beforeSend: function() {
                    $button.text('Loading...').prop('disabled', true);
                },
                success: function(response) {
                    var $grid = $('.post116-businesses-grid');
                    response.businesses.forEach(function(business) {
                        $grid.append(self.renderBusinessCard(business));
                    });
                    
                    // Update button
                    var nextPage = currentPage + 1;
                    if (nextPage <= totalPages) {
                        $button.data('page', nextPage).text('Load More').prop('disabled', false);
                    } else {
                        $button.hide();
                    }
                },
                error: function() {
                    $button.text('Load More').prop('disabled', false);
                }
            });
        },
        
        showError: function() {
            $('#post116-results').html('<div class="post116-error"><p>An error occurred while searching. Please try again.</p></div>');
        },
        
        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var later = function() {
                    clearTimeout(timeout);
                    func.apply(this, arguments);
                }.bind(this);
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // Utility function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        Post116BD.init();
    });
    
})(jQuery);