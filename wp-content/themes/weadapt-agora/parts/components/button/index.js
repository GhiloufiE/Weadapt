import Cookies from 'js-cookie';

const bindEvents = (links, themeCheckbox, qualityCheckbox) => {
    for (let i = 0; i < links.length; i++) {
        const element = links[i];
        const elHref = element.hash.replace('#', '');

        if(elHref === 'enable-dark-mode' || elHref === 'enable-light-mode') {
            addListeners(element, elHref, 'weadapt-dark-mode', themeCheckbox, 'enable-dark-mode', 'enable-light-mode')
        }
        if(elHref === 'enable-low-quality-images' || elHref === 'enable-high-quality-images') {
            addListeners(element, elHref, 'weadapt-low-quality-images', qualityCheckbox, 'enable-low-quality-images', 'enable-high-quality-images')
        }
    }
}

const addListeners = (targetElement, targetHref, cookieName, checkbox, enabledHref, disabledHref ) => {
    targetElement.addEventListener('click', (e) => {
        e.preventDefault();
        const cookieVal = Cookies.get(cookieName);

        if(targetHref === enabledHref && cookieVal !== 'true' && checkbox !== null) {
            checkbox.click();
            return;
        }
        if(targetHref === disabledHref && cookieVal === 'true' && checkbox !== null) {
            checkbox.click();
        }
    });
};

const initGreenSettingButtonEvents = () => {
    const links = document.querySelectorAll('a[href^="#"]');
    const menuCheckboxElements = document.querySelectorAll('.main-header__nav .true-false-button');
    let themeCheckbox = null;
    let qualityCheckbox = null;

    for (let i = 0; i < menuCheckboxElements.length; i++) {
        const menuCheckboxElement = menuCheckboxElements[i];

        if (menuCheckboxElement.getAttribute('for') === 'dark-mode') {
            themeCheckbox = menuCheckboxElement;
        }
        if (menuCheckboxElement.getAttribute('for') === 'low-quality-images') {
            qualityCheckbox = menuCheckboxElement;
        }
    }

    bindEvents(links, themeCheckbox, qualityCheckbox);
}

initGreenSettingButtonEvents();
