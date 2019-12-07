povs_lister_ajax = {
    selectors: {
        ajaxLister: '.js-povs-lister-ajax',
        ignore: 'js-povs-lister-ajax-ignore',
        listerData: '.js-povs-lister-ajax-list-data',
        dynamicContainer: '.js-povs-lister-ajax-update'
    },

    /**
     * Initiates lister ajax type
     */
    init: function()
    {
        let parentEl = document.querySelector(this.selectors.ajaxLister),
            self = this;

        parentEl.addEventListener('click', function(e) {
            if (e.target.nodeName === 'A' && !e.target.classList.contains(self.selectors.ignore)) {
                e.preventDefault();
                self.refreshTable(e.target.getAttribute('href'), parentEl, true, true);
            }
        });

        parentEl.addEventListener('submit', function(e) {
            e.preventDefault();
            let action = e.target.getAttribute('action'),
                params = new URLSearchParams(new FormData(e.target)).toString();
            self.refreshTable(action +'?'+ params, parentEl, true, false);
        });

        window.onpopstate = function () {
            self.refreshTable(location.href, parentEl, false, true);
        }
    },

    /**
     * Refreshes table data by provided url.
     *
     * @param url               url from where to fetch data (must return string html response)
     * @param parentEl          parentEl which content will be replaced with response
     * @param pushState         whether to push new url
     * @param updateFilterForm  whether to update filter form values by url parameters
     */
    refreshTable: function(url, parentEl, pushState, updateFilterForm)
    {
        this.triggerEvent('povs_lister_ajax_pre_update', parentEl);
        let request = new XMLHttpRequest(),
            self = this;
        request.open('GET', url, true);
        request.setRequestHeader('Ajax-Request', '1');

        request.onload = function() {
            if (this.status >= 200 && this.status < 400) {
                parentEl.querySelector(self.selectors.dynamicContainer).innerHTML = JSON.parse(this.response);

                if (pushState) {
                    window.history.pushState({}, "", url);
                }

                if (updateFilterForm) {
                    self.updateFilterForm(url, parentEl)
                }

                self.triggerEvent(
                    'povs_lister_ajax_post_update',
                    parentEl,
                    {response: this.response, url: url}
                );
            } else {
                self.triggerEvent('povs_lister_ajax_error', parentEl);
            }
        };

        request.onerror = function() {
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
        event = data ? new CustomEvent(event, {'detail': data}) : new Event(event);
        el.dispatchEvent(event);
    },

    /**
     * Updates filter form within parentElement by url parameters
     *
     * @param url
     * @param parentEl
     */
    updateFilterForm: function(url, parentEl)
    {
        let searchParams = new URLSearchParams(url.substring(url.indexOf('?') + 1)),
            listerData = parentEl.querySelector(this.selectors.listerData),
            fields = listerData.getAttribute('data-fields').split(',');

        listerData.innerHTML = '';

        searchParams.forEach(function(value, key) {
            let input = document.createElement('input'),
                name = key.split('[')[0];

            if (!fields.includes(name)) {
                return;
            }

            input.setAttribute('type', 'hidden');
            input.setAttribute('name', key);
            input.setAttribute('value', value);
            listerData.appendChild(input);
        });
    }
};