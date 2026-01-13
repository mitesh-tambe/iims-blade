<script>
    // Global TomSelect instances
    let authorTS = null;
    let publicationTS = null;
    let languageTS = null;
    let categoryTS = null;

    document.addEventListener('DOMContentLoaded', () => {

        /* ================= AUTHOR ================= */
        if (document.getElementById('authorSelect')) {
            authorTS = new TomSelect('#authorSelect', {
                placeholder: 'Select Author',
                allowEmptyOption: true,
                maxOptions: 1000,
                dropdownParent: 'body',
            });
        }

        /* ================= PUBLICATION ================= */
        if (document.getElementById('publicationSelect')) {
            publicationTS = new TomSelect('#publicationSelect', {
                placeholder: 'Select Publication',
                allowEmptyOption: true,
                maxOptions: 1000,
                dropdownParent: 'body',
            });
        }

        /* ================= LANGUAGE ================= */
        if (document.getElementById('languageSelect')) {
            languageTS = new TomSelect('#languageSelect', {
                placeholder: 'Select Language',
                allowEmptyOption: true,
                maxOptions: 1000,
                dropdownParent: 'body',
            });
        }

        /* ================= CATEGORY ================= */
        if (document.getElementById('categorySelect')) {
            categoryTS = new TomSelect('#categorySelect', {
                placeholder: 'Select Category',
                allowEmptyOption: true,
                maxOptions: 1000,
                dropdownParent: 'body',
            });
        }

        /* ================= PRICE CALCULATION ================= */
        function roundAmount(value) {
            return Math.round(value);
        }
        const mrpInput = document.querySelector('input[name="mrp"]');

        const discCompanyInput = document.querySelector('input[name="disc_from_company"]');
        const amtCompanyInput = document.querySelector('input[name="amt_company"]');

        const discCustomerInput = document.querySelector('input[name="disc_for_customer"]');
        const amtCustomerInput = document.querySelector('input[name="amt_customer"]');

        let isUpdating = false; // 🔒 prevent infinite loop

        function calculateFromPercentage(type) {
            if (isUpdating) return;
            isUpdating = true;

            const mrp = parseFloat(mrpInput.value) || 0;
            if (mrp <= 0) {
                isUpdating = false;
                return;
            }

            if (type === 'company') {
                const percent = parseFloat(discCompanyInput.value) || 0;
                amtCompanyInput.value = roundAmount((mrp * percent) / 100);
            }

            if (type === 'customer') {
                const percent = parseFloat(discCustomerInput.value) || 0;
                amtCustomerInput.value = roundAmount((mrp * percent) / 100);
            }

            isUpdating = false;
        }

        function calculateFromAmount(type) {
            if (isUpdating) return;
            isUpdating = true;

            const mrp = parseFloat(mrpInput.value) || 0;
            if (mrp <= 0) {
                isUpdating = false;
                return;
            }

            if (type === 'company') {
                const amount = parseFloat(amtCompanyInput.value) || 0;
                discCompanyInput.value = ((amount / mrp) * 100).toFixed(2);
            }

            if (type === 'customer') {
                const amount = parseFloat(amtCustomerInput.value) || 0;
                discCustomerInput.value = ((amount / mrp) * 100).toFixed(2);
            }

            isUpdating = false;
        }

        /* ===== EVENT LISTENERS ===== */

        // % → Amount
        discCompanyInput.addEventListener('input', () => calculateFromPercentage('company'));
        discCustomerInput.addEventListener('input', () => calculateFromPercentage('customer'));

        // Amount → %
        amtCompanyInput.addEventListener('input', () => calculateFromAmount('company'));
        amtCustomerInput.addEventListener('input', () => calculateFromAmount('customer'));

        // Recalculate everything if MRP changes
        mrpInput.addEventListener('input', () => {
            calculateFromPercentage('company');
            calculateFromPercentage('customer');
        });

    });

    /* ================= EVENTS FROM MODALS ================= */

    // AUTHOR CREATED
    window.addEventListener('author-created', (e) => {
        if (!authorTS) return;

        const {
            id,
            name
        } = e.detail;

        authorTS.addOption({
            value: id,
            text: name
        });
        authorTS.refreshOptions(false);
        authorTS.setValue(id);
    });

    // PUBLICATION CREATED
    window.addEventListener('publication-created', (e) => {
        if (!publicationTS) return;

        const {
            id,
            name
        } = e.detail;

        publicationTS.addOption({
            value: id,
            text: name
        });
        publicationTS.refreshOptions(false);
        publicationTS.setValue(id);
    });

    // LANGUAGE CREATED
    window.addEventListener('language-created', (e) => {
        if (!languageTS) return;

        const {
            id,
            name
        } = e.detail;

        languageTS.addOption({
            value: id,
            text: name
        });
        languageTS.refreshOptions(false);
        languageTS.setValue(id);
    });

    // CATEGORY CREATED
    window.addEventListener('category-created', (e) => {
        if (!categoryTS) return;

        const {
            id,
            name
        } = e.detail;

        categoryTS.addOption({
            value: id,
            text: name
        });
        categoryTS.refreshOptions(false);
        categoryTS.setValue(id);
    });
</script>
