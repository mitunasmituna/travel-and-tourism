document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.primary-nav');

    if (menuToggle && navigation) {
        menuToggle.addEventListener('click', () => {
            const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', String(!isOpen));
            menuToggle.setAttribute('aria-label', isOpen ? 'Open navigation' : 'Close navigation');
            navigation.classList.toggle('is-open', !isOpen);
            document.body.classList.toggle('menu-open', !isOpen);
        });
        navigation.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                menuToggle.setAttribute('aria-expanded', 'false');
                navigation.classList.remove('is-open');
                document.body.classList.remove('menu-open');
            });
        });
    }

    const search = document.querySelector('#destination-search');
    const destinationCards = [...document.querySelectorAll('.listing-card')];
    const noDestinations = document.querySelector('#no-destinations');
    if (search) {
        search.addEventListener('input', () => {
            const query = search.value.trim().toLocaleLowerCase();
            let visible = 0;
            destinationCards.forEach((card) => {
                const matches = (card.dataset.search || '').includes(query);
                card.hidden = !matches;
                if (matches) visible += 1;
            });
            if (noDestinations) noDestinations.hidden = visible !== 0;
        });
    }

    const packageSearch = document.querySelector('#package-search');
    const packageDestination = document.querySelector('#package-destination');
    const packageCards = [...document.querySelectorAll('.package-card[data-search]')];
    const packageCount = document.querySelector('#package-result-count');
    const packageEmpty = document.querySelector('#package-no-results');
    const packageReset = document.querySelector('#package-filter-reset');
    if (packageSearch && packageDestination) {
        const filterPackages = () => {
            const query = packageSearch.value.trim().toLocaleLowerCase();
            const destinationId = packageDestination.value;
            let visible = 0;
            packageCards.forEach((card) => {
                const matches = (card.dataset.search || '').includes(query)
                    && (!destinationId || card.dataset.destination === destinationId);
                card.hidden = !matches;
                if (matches) visible += 1;
            });
            if (packageCount) packageCount.textContent = `${visible} ${visible === 1 ? 'journey' : 'journeys'}`;
            if (packageEmpty) packageEmpty.hidden = visible !== 0;
            if (packageReset) packageReset.hidden = !query && !destinationId;
        };
        packageSearch.addEventListener('input', filterPackages);
        packageDestination.addEventListener('change', filterPackages);
        packageReset.addEventListener('click', () => {
            packageSearch.value = '';
            packageDestination.value = '';
            filterPackages();
            packageSearch.focus();
        });
    }

    const destinationSelect = document.querySelector('#booking-destination');
    const packageSelect = document.querySelector('#booking-package');
    if (destinationSelect && packageSelect) {
        const filterPackages = () => {
            const destinationId = destinationSelect.value;
            [...packageSelect.options].forEach((option) => {
                if (!option.value) return;
                option.hidden = Boolean(destinationId) && option.dataset.destination !== destinationId;
                if (option.hidden && option.selected) packageSelect.value = '';
            });
        };
        const initiallySelectedPackage = packageSelect.selectedOptions[0];
        if (!destinationSelect.value && initiallySelectedPackage && initiallySelectedPackage.dataset.destination) {
            destinationSelect.value = initiallySelectedPackage.dataset.destination;
        }
        destinationSelect.addEventListener('change', filterPackages);
        filterPackages();
        packageSelect.addEventListener('change', () => {
            const selected = packageSelect.selectedOptions[0];
            if (selected && selected.dataset.destination) {
                destinationSelect.value = selected.dataset.destination;
                filterPackages();
                packageSelect.value = selected.value;
            }
        });
    }

    document.querySelectorAll('form[data-validate]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const password = form.querySelector('[name="password"]');
            const confirmation = form.querySelector('[name="confirm_password"]');
            if (password && confirmation && password.value !== confirmation.value) {
                confirmation.setCustomValidity('Passwords do not match.');
            } else if (confirmation) {
                confirmation.setCustomValidity('');
            }
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        });
        const confirmation = form.querySelector('[name="confirm_password"]');
        if (confirmation) confirmation.addEventListener('input', () => confirmation.setCustomValidity(''));
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Are you sure you want to continue?')) {
                event.preventDefault();
            }
        });
    });

    const lightbox = document.querySelector('#gallery-lightbox');
    if (lightbox) {
        const lightboxImage = lightbox.querySelector('img');
        const caption = lightbox.querySelector('p');
        document.querySelectorAll('.gallery-item').forEach((item) => {
            item.addEventListener('click', () => {
                lightboxImage.src = item.dataset.image;
                lightboxImage.alt = item.dataset.caption || '';
                caption.textContent = item.dataset.caption || '';
                lightbox.showModal();
            });
        });
        lightbox.querySelector('.lightbox-close').addEventListener('click', () => lightbox.close());
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) lightbox.close();
        });
    }

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.destination-card, .experience-card, .package-card, .gallery-item, .stats-section > div').forEach((element) => {
        element.classList.add('reveal');
        revealObserver.observe(element);
    });
});
