(function () {
    var searchInput = document.getElementById('globalSearchInput');
    var dropdown = document.getElementById('searchDropdown');
    if (!searchInput || !dropdown) return;

    var searchUrl = searchInput.getAttribute('data-search-url');
    var searchIndexUrl = searchInput.getAttribute('data-search-index-url');
    if (!searchUrl || !searchIndexUrl) return;

    var searchTimeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        var query = this.value.trim();

        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(function () {
            fetch(searchUrl + '?q=' + encodeURIComponent(query))
                .then(function (res) { return res.json(); })
                .then(function (results) {
                    if (results.length === 0) {
                        dropdown.innerHTML =
                            '<div class="dropdown-item text-muted text-center py-3">' +
                                'No results found for "' + query + '"' +
                            '</div>';
                    } else {
                        var html = results.map(function (item) {
                            return '<a href="' + item.url + '" class="dropdown-item py-2">' +
                                '<div class="d-flex align-items-center">' +
                                    '<span class="badge badge-' + item.color + ' mr-3 p-2">' +
                                        '<i class="' + item.icon + '"></i>' +
                                    '</span>' +
                                    '<div>' +
                                        '<div class="font-weight-bold text-sm">' + item.title + '</div>' +
                                        '<small class="text-muted">' + item.subtitle + ' &mdash; ' + item.meta + '</small>' +
                                    '</div>' +
                                    '<span class="badge badge-light ml-auto">' + item.type + '</span>' +
                                '</div>' +
                            '</a>';
                        }).join('') +
                            '<div class="dropdown-divider"></div>' +
                            '<a href="' + searchIndexUrl + '?q=' + encodeURIComponent(query) +
                                '" class="dropdown-item text-center text-primary font-weight-bold">' +
                                '<i class="fas fa-search mr-1"></i> View all results for "' + query + '"' +
                            '</a>';
                        dropdown.innerHTML = html;
                    }
                    dropdown.style.display = 'block';
                })
                .catch(function () {
                    dropdown.style.display = 'none';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            submitGlobalSearch();
        }
    });

    window.submitGlobalSearch = function () {
        var query = searchInput.value.trim();
        if (query) {
            window.location.href = searchIndexUrl + '?q=' + encodeURIComponent(query);
        }
    };
})();
