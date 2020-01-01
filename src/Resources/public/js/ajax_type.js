ListerAjax = {
    options: {
        updateState: true
    },
    selectors: {
        ajaxLister: '.js-povs-lister-ajax',
        trigger: 'js-povs-lister-ajax-trigger',
        listerData: '.js-povs-lister-ajax-list-data',
        dynamicContainer: '.js-povs-lister-ajax-update'
    },
    loading: false,

    /**
     * Initiates lister ajax type
     */
    init: function()
    {
        let parentEl = document.querySelector(this.selectors.ajaxLister),
            self = this;

        if (!parentEl) {
            return;
        }

        //Updates table when <a href=""> elements with class js-povs-lister-ajax-trigger are clicked.
        //Such elements are: pagination, sorting, data length change
        parentEl.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains(self.selectors.trigger)) {
                e.preventDefault();
                self.refreshTable(e.target.getAttribute('href'), parentEl, true);
            }
        });

        //Updates table when filter form is submitted
        parentEl.addEventListener('submit', function(e) {
            if (!self.isSupported()) {
                return;
            }

            e.preventDefault();
            let action = e.target.getAttribute('action'),
                params = new URLSearchParams(new FormData(e.target)).toString();

            self.refreshTable(action +'?'+ params, parentEl, false);
        });

        //Updates table when user changes window state (browser navigation)
        if (self.options.updateState) {
            window.onpopstate = function (e) {
                if (e.state) {
                    parentEl.querySelector(self.selectors.dynamicContainer).innerHTML = e.state.html;
                }
            }
        }
    },

    /**
     * Refreshes table data by provided url.
     *
     * @param url               url from where to fetch data (must return string html response)
     * @param parentEl          parentEl which content will be replaced with response
     * @param updateFilterForm  whether to update filter form values by url parameters
     */
    refreshTable: function(url, parentEl, updateFilterForm)
    {
        if (this.loading) {
            return;
        }

        this.loading = true;
        this.triggerEvent('povs_lister_ajax_pre_update', parentEl);
        let request = new XMLHttpRequest(),
            self = this;
        request.open('GET', url, true);
        request.setRequestHeader('Ajax-Request', '1');

        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                let html = JSON.parse(this.response);
                parentEl.querySelector(self.selectors.dynamicContainer).innerHTML = html;

                if (self.options.updateState) {
                    window.history.pushState({'html': html}, "", url);
                }

                if (updateFilterForm) {
                    self.updateFilterForm(url, parentEl)
                }

                self.loading = false;
                self.triggerEvent(
                    'povs_lister_ajax_post_update',
                    parentEl,
                    {response: this.response, url: url}
                );
            } else {
                self.loading = false;
                self.triggerEvent('povs_lister_ajax_error', parentEl);
            }
        };

        request.onerror = function() {
            self.loading = false;
            self.triggerEvent('povs_lister_ajax_error', parentEl);
        };

        request.send();
    },

    /**
     * Triggers event on the element
     *
     * @param event which event to trigger
     * @param el    on what element to trigger
     * @param data  data that will be passed with event
     */
    triggerEvent: function(event, el, data)
    {
        let e = document.createEvent('CustomEvent');

        if (data) {
            e.initCustomEvent(event, false, false, data);
        } else {
            e.initEvent(event, false, false);
        }

        el.dispatchEvent(e);
    },

    /**
     * Updates filter form within parentElement by url parameters
     *
     * @param url
     * @param parentEl
     */
    updateFilterForm: function(url, parentEl)
    {
        if (!this.isSupported()) {
            return;
        }

        let searchParams = new URLSearchParams(url.substring(url.indexOf('?') + 1)),
            listerData = parentEl.querySelector(this.selectors.listerData),
            fields = listerData.getAttribute('data-fields').split(',');

        listerData.innerHTML = '';

        searchParams.forEach(function(value, key) {
            let input = document.createElement('input'),
                name = key.split('[')[0];

            if (fields.indexOf(name) === -1) {
                return;
            }

            input.setAttribute('type', 'hidden');
            input.setAttribute('name', key);
            input.setAttribute('value', value);
            listerData.appendChild(input);
        });
    },

    /**
     * Returns whether URLSearchParams function is supported
     */
    isSupported: function()
    {
        return window.URLSearchParams
            && window.URLSearchParams.prototype.get
            && window.FormData
            && window.FormData.prototype.get;
    }
};